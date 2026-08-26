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
namespace pocketmine\level\particle;
use pocketmine\math\Vector3;use pocketmine\entity\Entity;use pocketmine\entity\object\ItemEntity;use pocketmine\network\mcpe\protocol\AddActorPacket;use pocketmine\network\mcpe\protocol\RemoveActorPacket;
class FloatingTextParticle extends Particle{
	protected $text;
	protected $title;
	protected $entityId;
	protected $invisible = false;
	public function __construct(Vector3 $pos, string $text, string $title = ""){
		parent::__construct($pos->x, $pos->y, $pos->z);
		$this->text = $text;
		$this->title = $title;
	}
	public function getText() : string{
		return $this->text;
	}
	public function setText(string $text) : void{
		$this->text = $text;
	}
	public function getTitle() : string{
		return $this->title;
	}
	public function setTitle(string $title) : void{
		$this->title = $title;
	}
	public function isInvisible() : bool{
		return $this->invisible;
	}
	public function setInvisible(bool $value = true) : void{
		$this->invisible = $value;
	}
    public function encode(){
        $p = [];
        if($this->entityId === null){
            $this->entityId = Entity::$entityCount++;
        }else{
            $pk0 = new RemoveActorPacket();
            $pk0->entityUniqueId = $this->entityId;
            $p[] = $pk0;
        }
        if(!$this->invisible){
            $pk = new AddActorPacket();
            $pk->entityRuntimeId = $this->entityId;
            $pk->type = ItemEntity::NETWORK_ID;
            $pk->position = $this->asVector3();
            $flags = (
                (1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG) |
                (1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG) |
                (1 << Entity::DATA_FLAG_IMMOBILE) |
                (1 << Entity::DATA_FLAG_SILENT) |
                (1 << Entity::DATA_FLAG_CAN_CLIMB)
            );
            $pk->metadata = [
                Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
                Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0.01], 
                Entity::DATA_BOUNDING_BOX_WIDTH => [Entity::DATA_TYPE_FLOAT, 0.01],
                Entity::DATA_BOUNDING_BOX_HEIGHT => [Entity::DATA_TYPE_FLOAT, 0.01],
                Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $this->title . ($this->text !== "" ? "\n" . $this->text : "")]
            ];
            $p[] = $pk;
        }
        return $p;
    }}