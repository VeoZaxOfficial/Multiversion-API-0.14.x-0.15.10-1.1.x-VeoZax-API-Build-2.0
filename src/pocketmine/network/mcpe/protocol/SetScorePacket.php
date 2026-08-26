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
use InvalidArgumentException;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;use UnexpectedValueException;use function count;
class SetScorePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SCORE_PACKET;
	public const TYPE_CHANGE = 0;
	public const TYPE_REMOVE = 1;
	public $type;
	public $entries = [];
	protected function decodePayload(){
		$this->type = $this->getByte();
		for($i = 0, $i2 = $this->getUnsignedVarInt(); $i < $i2; ++$i){
			$entry = new ScorePacketEntry();
			$entry->scoreboardId = $this->getVarLong();
			$entry->objectiveName = $this->getString();
			$entry->score = $this->getLInt();
			if($this->type !== self::TYPE_REMOVE){
				$entry->type = $this->getByte();
				switch($entry->type){
					case ScorePacketEntry::TYPE_PLAYER:
					case ScorePacketEntry::TYPE_ENTITY:
						$entry->entityUniqueId = $this->getEntityUniqueId();
						break;
					case ScorePacketEntry::TYPE_FAKE_PLAYER:
						$entry->customName = $this->getString();
						break;
					default:
						throw new UnexpectedValueException("Unknown entry type $entry->type");
				}
			}
			$this->entries[] = $entry;
		}
	}
	protected function encodePayload(){
        $this->putByte($this->type);
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putVarLong($entry->scoreboardId);
			$this->putString($entry->objectiveName);
            $this->putLInt($entry->score);
			if($this->type !== self::TYPE_REMOVE){
                $this->putByte($entry->type);
				switch($entry->type){
					case ScorePacketEntry::TYPE_PLAYER:
					case ScorePacketEntry::TYPE_ENTITY:
						$this->putEntityUniqueId($entry->entityUniqueId);
						break;
					case ScorePacketEntry::TYPE_FAKE_PLAYER:
						$this->putString($entry->customName);
						break;
					default:
						throw new InvalidArgumentException("Unknown entry type $entry->type");
				}
			}
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleSetScore($this);
	}}