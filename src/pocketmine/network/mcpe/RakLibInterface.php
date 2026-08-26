<?php

/* 
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *  This API has now modified by VeoZax under GNU Lesser General Public License.
 *  Feel free to use it + if you are willing to modify or Enhance this API,
 *  Make sure to publish your changes to the GitHub open sourced.
 *  Do Not Own This API Privately Since this API is made to use Freely for Every
 *  Legacy users from 0.14.x - 0.15.10 - 1.1.x
 *   
 *               ╦  ╦┌─┐┌─┐╔═╗┌─┐─┐ ┬  ╔═╗┌─┐┬
 *               ╚╗╔╝├┤ │ │╔═╝├─┤┌┴┬┘  ╠═╣├─┘│
 *                ╚╝ └─┘└─┘╚═╝┴ ┴┴ └─  ╩ ╩┴  ┴
 *  
 *  	         » Multi-Version API by VeoZax 
 *             » Accepted MCPE Versions: 0.14x - 0.15.10 - 1.1.x
 *  			     » YouTube: @VeoZax
 *            » Discord: https://discord.gg/dCzgPYam2J
 *               » Website: https://info.veozax.xyz
 */


declare(strict_types=1);
namespace pocketmine\network\mcpe;
use Exception;use pocketmine\event\player\PlayerCreationEvent;use pocketmine\network\AdvancedSourceInterface;use pocketmine\network\mcpe\encryption\DecryptionException;use pocketmine\network\mcpe\protocol\BatchPacket;use pocketmine\network\mcpe\protocol\DataPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\SetActorMotionPacket;use pocketmine\utils\BinaryDataException;use pocketmine\utils\Metaverse;use pocketmine\network\Network;use pmmp\thread\Thread as NativeThread;use pocketmine\Player;use pocketmine\Server;use pocketmine\snooze\SleeperNotifier;use raklib\protocol\EncapsulatedPacket;use raklib\protocol\PacketReliability;use raklib\RakLib;use raklib\server\RakLibServer;use raklib\server\ServerHandler;use raklib\server\ServerInstance;use raklib\utils\InternetAddress;use Throwable;use function addcslashes;use function base64_encode;use function chr;use function get_class;use function implode;use function rtrim;use function spl_object_hash;use function unserialize;use const pocketmine\COMPOSER_AUTOLOADER_PATH;
class RakLibInterface implements ServerInstance, AdvancedSourceInterface{
	private const MCPE_RAKNET_PROTOCOL_VERSION = 8;
	private const V_0_15_0_MCPE_RAKNET_PROTOCOL_VERSION = 7;
	private const V_0_14_0_MCPE_RAKNET_PROTOCOL_VERSION = 6; 
	private const MCPE_RAKNET_PACKET_ID = "\xfe";
	private const LEGACY_RAKNET_PACKET_ID = "\x8e"; 
	private $server;
	private $network;
	private $rakLib;
	private $players = [];
	private $identifiers = [];
	private $identifiersACK = [];
	private $interface;
	private $sleeper;
	public function __construct(Server $server){
		$this->server = $server;
		$this->sleeper = new SleeperNotifier();
		$this->rakLib = new RakLibServer(
			$this->server->getLogger(),
			$this->server->getLoader(),
			new InternetAddress($this->server->getIp(), $this->server->getPort(), 4),
			1492, 
			[
			    self::MCPE_RAKNET_PROTOCOL_VERSION,
			    self::V_0_15_0_MCPE_RAKNET_PROTOCOL_VERSION,
			    self::V_0_14_0_MCPE_RAKNET_PROTOCOL_VERSION,
			],
			$this->sleeper
		);
		$this->interface = new ServerHandler($this->rakLib, $this);
	}
	public function start(){
		$this->server->getTickSleeper()->addNotifier($this->sleeper, function() : void{
			$this->process();
		});
		$this->rakLib->start(NativeThread::INHERIT_CONSTANTS); 
		$this->interface->sendOption("gracePeriod", (string) $this->server->getProperty("veozax.reconnect-grace-period", 3.0));
	}
	public function setNetwork(Network $network){
		$this->network = $network;
	}
	public function process() : void{
		while($this->interface->handlePacket()){}
		if(!$this->rakLib->isRunning() and !$this->rakLib->isShutdown()){
			throw new Exception("RakLib Thread crashed");
		}
	}
	public function closeSession(string $identifier, string $reason) : void{
		if(isset($this->players[$identifier])){
			$player = $this->players[$identifier];
			unset($this->identifiers[spl_object_hash($player)]);
			unset($this->players[$identifier]);
			unset($this->identifiersACK[$identifier]);
			$player->close($player->getLeaveMessage(), $reason);
		}
	}
	public function close(Player $player, string $reason = "unknown reason"){
		if(isset($this->identifiers[$h = spl_object_hash($player)])){
			unset($this->players[$this->identifiers[$h]]);
			unset($this->identifiersACK[$this->identifiers[$h]]);
			$this->interface->closeSession($this->identifiers[$h], $reason);
			unset($this->identifiers[$h]);
		}
	}
	public function shutdown(){
		$this->server->getTickSleeper()->removeNotifier($this->sleeper);
		$this->interface->shutdown();
	}
	public function emergencyShutdown(){
		$this->server->getTickSleeper()->removeNotifier($this->sleeper);
		$this->interface->emergencyShutdown();
	}
	public function openSession(string $identifier, string $address, int $port, int $clientID, int $protocolVersion) : void{
		$ev = new PlayerCreationEvent($this, Player::class, Player::class, $address, $port);
		$ev->call();
		$class = $ev->getPlayerClass();
		$player = new $class($this, $ev->getAddress(), $ev->getPort(), $protocolVersion);
		$this->players[$identifier] = $player;
		$this->identifiersACK[$identifier] = 0;
		$this->identifiers[spl_object_hash($player)] = $identifier;
		$this->server->addPlayer($player);
	}
	private const LEGACY_P70_PACKET_CLASSES = [
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::LOGIN_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\LoginPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::TEXT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\TextPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::MOVE_PLAYER_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\MovePlayerPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::REMOVE_BLOCK_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\RemoveBlockPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::PLAYER_ACTION_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\PlayerActionPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::HURT_ARMOR_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\HurtArmorPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::INTERACT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\InteractPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::USE_ITEM_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\UseItemPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::MOB_EQUIPMENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\MobEquipmentPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::MOB_ARMOR_EQUIPMENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\MobArmorEquipmentPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::ENTITY_EVENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\EntityEventPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::ANIMATE_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\AnimatePacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::RESPAWN_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\RespawnPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::DROP_ITEM_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\DropItemPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::CONTAINER_CLOSE_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\ContainerClosePacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::CONTAINER_SET_SLOT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\ContainerSetSlotPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::CRAFTING_EVENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\CraftingEventPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::ADVENTURE_SETTINGS_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\AdventureSettingsPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::PLAYER_INPUT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\PlayerInputPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::SET_PLAYER_GAMETYPE_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\SetPlayerGameTypePacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::CLIENTBOUND_MAP_ITEM_DATA_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\ClientboundMapItemDataPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::MAP_INFO_REQUEST_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\MapInfoRequestPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::REQUEST_CHUNK_RADIUS_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\RequestChunkRadiusPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p70\Info::SET_ENTITY_DATA_PACKET => \pocketmine\network\mcpe\protocol\legacy\p70\SetEntityDataPacket::class,
	];
	private static function getLegacyP70Packet(int $packetId) : ?\pocketmine\network\mcpe\protocol\legacy\p70\DataPacket{
		$class = self::LEGACY_P70_PACKET_CLASSES[$packetId] ?? null;
		return $class === null ? null : new $class();
	}
	private const LEGACY_P84_PACKET_CLASSES = [
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::TEXT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::MOVE_PLAYER_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\MovePlayerPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::REMOVE_BLOCK_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\RemoveBlockPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::PLAYER_ACTION_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\PlayerActionPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::HURT_ARMOR_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\HurtArmorPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::INTERACT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\InteractPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::USE_ITEM_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\UseItemPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::MOB_EQUIPMENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\MobEquipmentPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::MOB_ARMOR_EQUIPMENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\MobArmorEquipmentPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::ENTITY_EVENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\EntityEventPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::ANIMATE_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\AnimatePacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::RESPAWN_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\RespawnPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::DROP_ITEM_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\DropItemPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::CONTAINER_CLOSE_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\ContainerClosePacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::CONTAINER_SET_SLOT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\ContainerSetSlotPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::CRAFTING_EVENT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\CraftingEventPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::ADVENTURE_SETTINGS_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\AdventureSettingsPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::PLAYER_INPUT_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\PlayerInputPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::SET_PLAYER_GAMETYPE_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\SetPlayerGameTypePacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::REQUEST_CHUNK_RADIUS_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\RequestChunkRadiusPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::SET_ENTITY_DATA_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\SetEntityDataPacket::class,
		\pocketmine\network\mcpe\protocol\legacy\p84\Info::ITEM_FRAME_DROP_ITEM_PACKET => \pocketmine\network\mcpe\protocol\legacy\p84\ItemFrameDropItemPacket::class,
	];
	private static function getLegacyP84Packet(int $packetId) : ?\pocketmine\network\mcpe\protocol\legacy\p84\DataPacket{
		$class = self::LEGACY_P84_PACKET_CLASSES[$packetId] ?? null;
		return $class === null ? null : new $class();
	}
	public function handleEncapsulated(string $identifier, EncapsulatedPacket $packet, int $flags) : void{
		if(isset($this->players[$identifier])){
			$player = $this->players[$identifier];
			$address = $player->getAddress();
			try{
				if($packet->buffer !== ""){
					$player->getPacketBatchLimiter()->decrement();
					if(strlen($packet->buffer) >= 1 && ord($packet->buffer[0]) === 0x8e){
						if(!$player->loggedIn && strlen($packet->buffer) >= 2 && ord($packet->buffer[1]) === 0x8f){
							$player->handleLegacyLogin($packet->buffer);
							return;
						}
						if(strlen($packet->buffer) >= 2){
								$packetId  = ord($packet->buffer[1]);
							$rawPayload = substr($packet->buffer, 2);
							$wireProtocol = ProtocolInfo::PROTOCOL_70;
							if($packetId === 0x92){
								$size = unpack('N', substr($rawPayload, 0, 4))[1];
								$compressed = substr($rawPayload, 4, $size);
								$decompressed = @zlib_decode($compressed);
								if($decompressed === false) return;
								$offset = 0;
								while($offset < strlen($decompressed)){
									$innerLen = unpack('N', substr($decompressed, $offset, 4))[1];
									$offset += 4;
									$innerBuf = substr($decompressed, $offset, $innerLen);
									$offset += $innerLen;
									if(strlen($innerBuf) < 1) continue;
									$innerId = ord($innerBuf[0]);
									$innerPayload = substr($innerBuf, 1);
									$ipk = self::getLegacyP70Packet($innerId);
									if($ipk === null){
										continue;
									}
									$ipk->setBuffer($innerPayload, 0);
									$ipk->decode();
									$modernIpk = \pocketmine\network\mcpe\AnyVersionManager::parseLegacyPacket($player, $ipk);
									if($modernIpk === null){
										continue;
									}
									$player->handleDataPacket($modernIpk);
								}
							} else {
								$pk = self::getLegacyP70Packet($packetId);
								if($pk === null){
									return;
								}
								$pk->setBuffer($rawPayload, 0);
								$pk->decode();
								$modernPk = \pocketmine\network\mcpe\AnyVersionManager::parseLegacyPacket($player, $pk);
								if($modernPk === null){
									return;
								}
								$player->handleDataPacket($modernPk);
							}
						}
						return;
					}
					if($player->getOriginalProtocol() === ProtocolInfo::PROTOCOL_84
						&& strlen($packet->buffer) >= 1 && ord($packet->buffer[0]) === 0xfe){
						$rawPayload = substr($packet->buffer, 1);
						$cipher = $player->getCipher();
						if($cipher !== null){
							try{
								$rawPayload = $cipher->decrypt($rawPayload);
							}catch(DecryptionException $e){
								throw new BinaryDataException("Packet decryption error");
							}
						}
						if(strlen($rawPayload) < 5 || ord($rawPayload[0]) !== \pocketmine\network\mcpe\protocol\legacy\p84\Info::BATCH_PACKET){
							return;
						}
						$batchLen = unpack('N', substr($rawPayload, 1, 4))[1];
						$batchPayload = substr($rawPayload, 5, $batchLen);
						$decompressed = strlen($batchPayload) > 0 ? @zlib_decode($batchPayload, 1024 * 1024 * 2) : false;
						if($decompressed === false){
							return;
						}
						$offset = 0;
						$len = strlen($decompressed);
						$count = 0;
						while($offset + 4 <= $len){
							if($count++ > 1024){
								break;
							}
							$innerLen = unpack('N', substr($decompressed, $offset, 4))[1];
							$offset += 4;
							$innerBuf = substr($decompressed, $offset, $innerLen);
							$offset += $innerLen;
							if(strlen($innerBuf) < 1){
								continue;
							}
							$innerId = ord($innerBuf[0]);
							$innerPayload = substr($innerBuf, 1);
							$ipk = self::getLegacyP84Packet($innerId);
							if($ipk === null){
								continue;
							}
							$ipk->setBuffer($innerPayload, 0);
							$ipk->decode();
							$modernIpk = \pocketmine\network\mcpe\P84VersionManager::parseLegacyPacket($player, $ipk);
							if($modernIpk === null){
								continue;
							}
							$player->handleDataPacket($modernIpk);
						}
						return;
					}
					$cipher = $player->getCipher();
					$buffer = substr($packet->buffer, 1);
					if($cipher !== null){
						try{
					    	$buffer = $cipher->decrypt($buffer);
						}catch(DecryptionException $e){
							throw new BinaryDataException("Packet decryption error");
						}
					}
				    $buffer = self::MCPE_RAKNET_PACKET_ID . $buffer;
				    $pk = new BatchPacket($buffer);
				    $pk->setProtocol($player->getProtocol());
					$player->handleDataPacket($pk);
				}
			}catch(Throwable $e){
				$logger = $this->server->getLogger();
				$logger->debug("Packet " . (isset($pk) ? get_class($pk) : "unknown") . ": " . base64_encode($packet->buffer));
				$logger->logException($e);
				$player->close($player->getLeaveMessage(), "§fServer Caused Some Error");
				$this->interface->blockAddress($address, 5);
			}
		}
	}
	public function blockAddress(string $address, int $timeout = 300){
		$this->interface->blockAddress($address, $timeout);
	}
	public function unblockAddress(string $address){
		$this->interface->unblockAddress($address);
	}
	public function handleRaw(string $address, int $port, string $payload) : void{
		$this->server->handlePacket($this, $address, $port, $payload);
	}
	public function sendRawPacket(string $address, int $port, string $payload){
		$this->interface->sendRaw($address, $port, $payload);
	}
	public function notifyACK(string $identifier, int $identifierACK) : void{
	}
	public function setName(string $name){
		$info = $this->server->getQueryInformation();
		$this->interface->sendOption("name", implode(";",
			[
				"MCPE",
				rtrim(addcslashes($name, ";"), '\\'),
				Metaverse::getPingProtocol(),
				Metaverse::getPingVersion(),
				$info->getPlayerCount(),
				$info->getMaxPlayerCount(),
				$this->rakLib->getServerId(),
				$this->server->getName(),
				Server::getGamemodeName(Player::getClientFriendlyGamemode($this->server->getGamemode()))
			]) . ";"
		);
	}
	public function setPortCheck($name){
		$this->interface->sendOption("portChecking", (bool) $name);
	}
	public function handleOption(string $option, string $value) : void{
		if($option === "bandwidth"){
			$v = unserialize($value);
			$this->network->addStatistics($v["up"], $v["down"]);
		}
	}
	public function sendLegacyBuffer(Player $player, string $encodedBuffer, bool $immediate = true) : void {
		if(!isset($this->identifiers[$h = spl_object_hash($player)])) return;
		$identifier = $this->identifiers[$h];
		$pk = new EncapsulatedPacket();
		$pk->identifierACK = null;
		$pk->buffer = self::LEGACY_RAKNET_PACKET_ID . $encodedBuffer;
		$pk->reliability = PacketReliability::RELIABLE_ORDERED;
		$pk->orderChannel = 0;
		$this->interface->sendEncapsulated($identifier, $pk, $immediate ? RakLib::PRIORITY_IMMEDIATE : RakLib::PRIORITY_NORMAL);
	}
	public function sendP84Buffer(Player $player, string $encodedBuffer, bool $immediate = true) : void {
		if(!isset($this->identifiers[$h = spl_object_hash($player)])) return;
		$identifier = $this->identifiers[$h];
		$pk = new EncapsulatedPacket();
		$pk->identifierACK = null;
		$pk->buffer = self::MCPE_RAKNET_PACKET_ID . $encodedBuffer;
		$pk->reliability = PacketReliability::RELIABLE_ORDERED;
		$pk->orderChannel = 0;
		$this->interface->sendEncapsulated($identifier, $pk, $immediate ? RakLib::PRIORITY_IMMEDIATE : RakLib::PRIORITY_NORMAL);
	}
	public function putPacket(Player $player, $packet, bool $needACK = false, bool $immediate = true){
		if(isset($this->identifiers[$h = spl_object_hash($player)])){
			$identifier = $this->identifiers[$h];
		if($player->getOriginalProtocol() === ProtocolInfo::PROTOCOL_84){
		    if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\DataPacket){
		        $legacyPacket = $packet;
		    }else{
		        $legacyPacket = \pocketmine\network\mcpe\P84VersionManager::parsePacket($player, $packet);
		        if($legacyPacket === null){
		            return null;
		        }
		    }
		    if(!$legacyPacket->isEncoded){ $legacyPacket->encode(); }
		    $legBatch = new \pocketmine\network\mcpe\protocol\legacy\p84\BatchPacket();
		    $legBatch->payload = zlib_encode(
		        pack('N', strlen($legacyPacket->buffer)) . $legacyPacket->buffer,
		        ZLIB_ENCODING_DEFLATE,
		        $this->server->networkCompressionLevel
		    );
		    $legBatch->encode();
		    $this->sendP84Buffer($player, $legBatch->buffer, $immediate);
		    return null;
		}
		if($player->getOriginalProtocol() <= ProtocolInfo::PROTOCOL_70){
		    $legacyBatchThreshold = 256;
		    if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p70\DataPacket){
		        $legacyPacket = $packet;
		    }else{
		        $legacyPacket = \pocketmine\network\mcpe\AnyVersionManager::parsePacket($player, $packet);
		        if($legacyPacket === null) return null;
		    }
		    if(!$legacyPacket->isEncoded){ $legacyPacket->encode(); }
		    if(strlen($legacyPacket->buffer) < $legacyBatchThreshold){
		        $this->sendLegacyBuffer($player, $legacyPacket->buffer, $immediate);
		    }else{
		        $legBatch = new \pocketmine\network\mcpe\protocol\legacy\p70\BatchPacket();
		        $legBatch->payload = zlib_encode(
		            pack('N', strlen($legacyPacket->buffer)) . $legacyPacket->buffer,
		            ZLIB_ENCODING_DEFLATE,
		            $this->server->networkCompressionLevel
		        );
		        $legBatch->encode();
		        $this->sendLegacyBuffer($player, $legBatch->buffer, $immediate);
		    }
		    return null;
		}
			if($packet instanceof SetActorMotionPacket && $player->getOriginalProtocol() >= ProtocolInfo::PROTOCOL_81 && $player->getOriginalProtocol() < ProtocolInfo::PROTOCOL_84){
			    $legacyHorizontalScale = 0.55;
			    $motion = $packet->motion ?? null;
			    if($motion !== null){
			        $packet->motion = new \pocketmine\math\Vector3(
			            $motion->x * $legacyHorizontalScale,
			            $motion->y,
			            $motion->z * $legacyHorizontalScale
			        );
			        $packet->isEncoded = false;
			    }
			}
			if(!$packet->isEncoded){
				$packet->encode();
			}
			if($packet instanceof BatchPacket){
			    $buffer = $packet->buffer;
		    	$cipher = $player->getCipher();
		    	$rawBuffer = substr($buffer, 1);
		    	if($player->getProtocol() < ProtocolInfo::PROTOCOL_110){
		    	    $rawBuffer = chr(0x6) . $rawBuffer;
		    	    $buffer = self::MCPE_RAKNET_PACKET_ID . ($cipher !== null ? $cipher->encrypt($rawBuffer) : $rawBuffer);
		    	}else{
		    	    $buffer = self::MCPE_RAKNET_PACKET_ID . ($cipher !== null ? $cipher->encrypt($rawBuffer) : $rawBuffer);
		    	}
		    	$pk = new EncapsulatedPacket();
				$pk->identifierACK = $needACK ? $this->identifiersACK[$identifier]++ : null;
				$pk->buffer = $buffer;
				$pk->reliability = PacketReliability::RELIABLE_ORDERED;
				$pk->orderChannel = 0;
				$this->interface->sendEncapsulated($identifier, $pk, ($needACK ? RakLib::FLAG_NEED_ACK : 0) | ($immediate ? RakLib::PRIORITY_IMMEDIATE : RakLib::PRIORITY_NORMAL));
				return $pk->identifierACK;
			}else{
				$this->server->batchPackets([$player], [$packet], true, $immediate);
				return null;
			}
		}
		return null;
	}
	public function updatePing(string $identifier, int $pingMS) : void{
		if(isset($this->players[$identifier])){
			$this->players[$identifier]->updatePing($pingMS);
		}
	}}