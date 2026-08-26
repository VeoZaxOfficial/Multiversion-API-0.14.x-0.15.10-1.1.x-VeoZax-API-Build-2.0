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
namespace pocketmine\network\mcpe\protocol;
use pocketmine\math\Vector3;use pocketmine\nbt\NetworkLittleEndianNBTStream;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\ListTag;use pocketmine\network\mcpe\multiversion\block\BlockPalette;use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;use pocketmine\network\mcpe\protocol\types\ChatRestrictionLevel;use pocketmine\network\mcpe\protocol\types\EditorWorldType;use pocketmine\network\mcpe\protocol\types\EducationEditionOffer;use pocketmine\network\mcpe\protocol\types\EducationUriResource;use pocketmine\network\mcpe\protocol\types\Experiments;use pocketmine\network\mcpe\protocol\types\GameRuleType;use pocketmine\network\mcpe\protocol\types\GeneratorType;use pocketmine\network\mcpe\protocol\types\MultiplayerGameVisibility;use pocketmine\network\mcpe\protocol\types\NetworkPermissions;use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;use pocketmine\network\mcpe\protocol\types\PlayerPermissions;use pocketmine\network\mcpe\protocol\types\SpawnSettings;use pocketmine\utils\UUID;use UnexpectedValueException;use function count;use function file_get_contents;use function json_decode;use function ord;use function pack;use const pocketmine\RESOURCE_PATH;
class StartGamePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::START_GAME_PACKET;
	private static $itemTableCache = null;
	public $entityUniqueId;
	public $entityRuntimeId;
	public $playerGamemode;
	public $playerPosition;
	public $pitch;
	public $yaw;
	public $seed;
	public $spawnSettings;
	public $generator = GeneratorType::OVERWORLD;
	public $worldGamemode;
	public $hardcore = false;
	public $difficulty;
	public $spawnX;
	public $spawnY;
	public $spawnZ;
	public $hasAchievementsDisabled = true;
	public $isEditorMode = false;
	public $editorWorldType = EditorWorldType::NON_EDITOR;
	public $createdInEditorMode = false;
	public $exportedFromEditorMode = false;
	public $time = -1;
	public $dayCycleStopTime = false;
	public $eduEditionOffer = EducationEditionOffer::NONE;
	public $eduMode = false;
	public $hasEduFeaturesEnabled = false;
	public $eduProductUUID = "";
	public $rainLevel;
	public $lightningLevel;
	public $hasConfirmedPlatformLockedContent = false;
	public $isMultiplayerGame = true;
	public $hasLANBroadcast = true;
	public $hasXboxLiveBroadcast = false;
	public $xboxLiveBroadcastMode = MultiplayerGameVisibility::PUBLIC;
	public $platformBroadcastMode = MultiplayerGameVisibility::PUBLIC;
	public $commandsEnabled;
	public $isTexturePacksRequired = true;
	public $gameRules = [ 
		"naturalregeneration" => [GameRuleType::BOOL, false, false] 
	];
	public $experiments;
	public $hasBonusChestEnabled = false;
	public $hasStartWithMapEnabled = false;
	public $hasTrustPlayersEnabled = false;
	public $defaultPlayerPermission = PlayerPermissions::MEMBER; 
	public $serverChunkTickRadius = 4; 
	public $hasPlatformBroadcast = false;
	public $xboxLiveBroadcastIntent = false;
	public $hasLockedBehaviorPack = false;
	public $hasLockedResourcePack = false;
	public $isFromLockedWorldTemplate = false;
	public $useMsaGamertagsOnly = false;
	public $isFromWorldTemplate = false;
	public $isWorldTemplateOptionLocked = false;
	public $onlySpawnV1Villagers = false;
	public $disablePersona = false;
	public $disableCustomSkins = false;
	public $muteEmoteAnnouncements = false;
	public $vanillaVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
	public $limitedWorldWidth = 0;
	public $limitedWorldLength = 0;
	public $isNewNether = false;
	public $eduSharedUriResource = null;
	public $experimentalGameplayOverride = null;
	public $chatRestrictionLevel = ChatRestrictionLevel::NONE;
	public $disablePlayerInteractions = false;
	public $serverIdentifier = "";
	public $worldIdentifier = "";
	public $scenarioIdentifier = "";
	public $levelId = ""; 
	public $worldName;
	public $premiumWorldTemplateId = "";
	public $isTrial = false;
	public $playerMovementSettings;
	public $isMovementServerAuthoritative = false;
	public $currentTick = 0; 
	public $enchantmentSeed = 0;
	public $multiplayerCorrelationId = ""; 
	public $enableNewInventorySystem = false; 
	public $serverSoftwareVersion;
	public $playerActorProperties;
	public $blockPaletteChecksum;
    public $worldTemplateId;
    public $enableClientSideChunkGeneration = false;
	public $blockNetworkIdsAreHashes = false; 
	public $networkPermissions;
	public $blockTable = null;
	public $blockPalette = [];
	public $itemTable = null;
	protected function decodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->entityUniqueId = $this->getEntityUniqueId();
	    	$this->entityRuntimeId = $this->getEntityRuntimeId();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	        	$this->playerGamemode = $this->getVarInt();
	    	}
	    	$this->playerPosition = $this->getVector3();
	    	$this->pitch = $this->getLFloat();
	    	$this->yaw = $this->getLFloat();
	    	
	        	$this->seed = $this->getVarInt();
	    	
	    	
	    	$this->spawnSettings = SpawnSettings::read($this);
	    	$this->generator = $this->getVarInt();
	    	$this->worldGamemode = $this->getVarInt();
	    	
	    	$this->difficulty = $this->getVarInt();
	    	$this->getBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
            $this->hasAchievementsDisabled = $this->getBool();
	    	
	    	$this->time = $this->getVarInt();
	    	
                $this->eduMode = $this->getBool();
	    	
	    	
	    	$this->rainLevel = $this->getLFloat();
	    	$this->lightningLevel = $this->getLFloat();
	    	
	    	
	    	$this->commandsEnabled = $this->getBool();
	    	$this->isTexturePacksRequired = $this->getBool();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		    	$this->gameRules = $this->getGameRules();
		    	
	    	}
	        $this->levelId = $this->getString();
	        $this->worldName = $this->getString();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	            $this->premiumWorldTemplateId = $this->getString();
	         	$this->isTrial = $this->getBool();
	   	     	
	        	 $this->currentTick = $this->getLLong();
	    	}
            
	    }else{
	        $this->seed = $this->getInt();
	    	$this->spawnSettings = SpawnSettings::read($this);
		    $this->generator = $this->getInt();
		    $this->worldGamemode = $this->getInt();
	    	$this->entityRuntimeId = $this->getEntityRuntimeId();
	    	$this->spawnX = $this->getInt();
	    	$this->spawnY = $this->getInt();
	    	$this->spawnZ = $this->getInt();
	    	$this->playerPosition = $this->getVector3();
	    	$this->hasAchievementsDisabled = $this->getBool();
	    	$this->dayCycleStopTime = $this->getBool();
	    	$this->eduMode = $this->getBool();
	    	$this->levelId = $this->getShortString();
	    }
	}
	protected function encodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putEntityUniqueId($this->entityUniqueId);
	    	$this->putEntityRuntimeId($this->entityRuntimeId);
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		        $this->putVarInt($this->playerGamemode);
	    	}
	    	$this->putVector3($this->playerPosition);
            $this->putLFloat($this->pitch);
            $this->putLFloat($this->yaw);
	    	
	        	$this->putVarInt($this->seed);
	    	
	    	
	        $this->spawnSettings->write($this);
	    	$this->putVarInt($this->generator);
	    	$this->putVarInt($this->worldGamemode);
	    	
		    $this->putVarInt($this->difficulty);
		    $this->putBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
            $this->putBool($this->hasAchievementsDisabled);
	    	
		    $this->putVarInt($this->time);
	    	
                $this->putBool($this->eduMode);
	    	
	    	
	    	($this->buffer .= (pack("g", $this->rainLevel)));
	    	($this->buffer .= (pack("g", $this->lightningLevel)));
		    
	    	
            $this->putBool($this->commandsEnabled);
            $this->putBool($this->isTexturePacksRequired);
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		        $this->putGameRules($this->gameRules);
		        
            }
		    $this->putString($this->levelId);
		    $this->putString($this->worldName);
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
                $this->putString($this->premiumWorldTemplateId);
                $this->putBool($this->isTrial);
		        
                $this->putLLong($this->currentTick);
		    }
            
	    }else{
	    	$this->putInt($this->seed);
	    	$this->spawnSettings->write($this);
		    $this->putInt($this->generator);
		    $this->putInt($this->worldGamemode);
	    	$this->putEntityRuntimeId($this->entityRuntimeId);
	    	$this->putInt($this->spawnX);
	    	$this->putInt($this->spawnY);
	    	$this->putInt($this->spawnZ);
	    	$this->putVector3($this->playerPosition);
	    	$this->putBool($this->hasAchievementsDisabled);
	    	$this->putBool($this->dayCycleStopTime);
	    	$this->putBool($this->eduMode);
	    	$this->putShortString($this->levelId);
	    }
	}
	private static function serializeItemTable(array $table) : string{
		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($table));
		foreach($table as $name => $legacyId){
			$stream->putString($name);
			$stream->putLShort($legacyId);
		}
		return $stream->getBuffer();
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleStartGame($this);
	}}