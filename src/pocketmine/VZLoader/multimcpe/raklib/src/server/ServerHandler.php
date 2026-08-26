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
namespace raklib\server;
use pocketmine\utils\Binary;use raklib\protocol\EncapsulatedPacket;use raklib\RakLib;use function chr;use function ord;use function pack;use function strlen;use function substr;use function unpack;
class ServerHandler{
	protected $server;
	protected $instance;
	public function __construct(RakLibServer $server, ServerInstance $instance){
		$this->server = $server;
		$this->instance = $instance;
	}
	public function sendEncapsulated(string $identifier, EncapsulatedPacket $packet, int $flags = RakLib::PRIORITY_NORMAL) : void{
		$buffer = chr(RakLib::PACKET_ENCAPSULATED) . chr(strlen($identifier)) . $identifier . chr($flags) . $packet->toInternalBinary();
		$this->server->pushMainToThreadPacket($buffer);
	}
	public function sendRaw(string $address, int $port, string $payload) : void{
		$buffer = chr(RakLib::PACKET_RAW) . chr(strlen($address)) . $address . (pack("n", $port)) . $payload;
		$this->server->pushMainToThreadPacket($buffer);
	}
	public function closeSession(string $identifier, string $reason) : void{
		$buffer = chr(RakLib::PACKET_CLOSE_SESSION) . chr(strlen($identifier)) . $identifier . chr(strlen($reason)) . $reason;
		$this->server->pushMainToThreadPacket($buffer);
	}
	public function sendOption(string $name, $value) : void{
		$buffer = chr(RakLib::PACKET_SET_OPTION) . chr(strlen($name)) . $name . $value;
		$this->server->pushMainToThreadPacket($buffer);
	}
	public function blockAddress(string $address, int $timeout) : void{
		$buffer = chr(RakLib::PACKET_BLOCK_ADDRESS) . chr(strlen($address)) . $address . (pack("N", $timeout));
		$this->server->pushMainToThreadPacket($buffer);
	}
	public function unblockAddress(string $address) : void{
		$buffer = chr(RakLib::PACKET_UNBLOCK_ADDRESS) . chr(strlen($address)) . $address;
		$this->server->pushMainToThreadPacket($buffer);
	}
	public function shutdown() : void{
		$buffer = chr(RakLib::PACKET_SHUTDOWN);
		$this->server->pushMainToThreadPacket($buffer);
		$this->server->shutdown();
		$this->server->join();
	}
	public function emergencyShutdown() : void{
		$this->server->shutdown();
		$this->server->pushMainToThreadPacket(chr(RakLib::PACKET_EMERGENCY_SHUTDOWN));
	}
	public function handlePacket() : bool{
		if(($packet = $this->server->readThreadToMainPacket()) !== null){
			$id = ord($packet[0]);
			$offset = 1;
			if($id === RakLib::PACKET_ENCAPSULATED){
				$len = ord($packet[$offset++]);
				$identifier = substr($packet, $offset, $len);
				$offset += $len;
				$flags = ord($packet[$offset++]);
				$buffer = substr($packet, $offset);
				$this->instance->handleEncapsulated($identifier, EncapsulatedPacket::fromInternalBinary($buffer), $flags);
			}elseif($id === RakLib::PACKET_RAW){
				$len = ord($packet[$offset++]);
				$address = substr($packet, $offset, $len);
				$offset += $len;
				$port = (unpack("n", substr($packet, $offset, 2))[1]);
				$offset += 2;
				$payload = substr($packet, $offset);
				$this->instance->handleRaw($address, $port, $payload);
			}elseif($id === RakLib::PACKET_SET_OPTION){
				$len = ord($packet[$offset++]);
				$name = substr($packet, $offset, $len);
				$offset += $len;
				$value = substr($packet, $offset);
				$this->instance->handleOption($name, $value);
			}elseif($id === RakLib::PACKET_OPEN_SESSION){
				$len = ord($packet[$offset++]);
				$identifier = substr($packet, $offset, $len);
				$offset += $len;
				$len = ord($packet[$offset++]);
				$address = substr($packet, $offset, $len);
				$offset += $len;
				$port = (unpack("n", substr($packet, $offset, 2))[1]);
				$offset += 2;
				$protocol = ord($packet[$offset++]);
				$clientID = Binary::readLong(substr($packet, $offset, 8));
				$this->instance->openSession($identifier, $address, $port, $clientID, $protocol);
			}elseif($id === RakLib::PACKET_CLOSE_SESSION){
				$len = ord($packet[$offset++]);
				$identifier = substr($packet, $offset, $len);
				$offset += $len;
				$len = ord($packet[$offset++]);
				$reason = substr($packet, $offset, $len);
				$this->instance->closeSession($identifier, $reason);
			}elseif($id === RakLib::PACKET_INVALID_SESSION){
				$len = ord($packet[$offset++]);
				$identifier = substr($packet, $offset, $len);
				$this->instance->closeSession($identifier, "Invalid session");
			}elseif($id === RakLib::PACKET_ACK_NOTIFICATION){
				$len = ord($packet[$offset++]);
				$identifier = substr($packet, $offset, $len);
				$offset += $len;
				$identifierACK = (unpack("N", substr($packet, $offset, 4))[1] << 32 >> 32);
				$this->instance->notifyACK($identifier, $identifierACK);
			}elseif($id === RakLib::PACKET_REPORT_PING){
				$len = ord($packet[$offset++]);
				$identifier = substr($packet, $offset, $len);
				$offset += $len;
				$pingMS = (unpack("N", substr($packet, $offset, 4))[1] << 32 >> 32);
				$this->instance->updatePing($identifier, $pingMS);
			}
			return true;
		}
		return false;
	}}