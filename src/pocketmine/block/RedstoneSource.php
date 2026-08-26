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
namespace pocketmine\block;
use pocketmine\item\Item;use pocketmine\level\Level;use pocketmine\math\Vector3;use pocketmine\Player;use function in_array;
class RedstoneSource extends Flowable{
	protected $maxStrength = 15;
	protected $activated = false;
	public function getMaxStrength() : int{
		return $this->maxStrength;
	}
	public function isActivated(Block $from = null) : bool{
		return $this->activated;
	}
	public function canCalc() : bool{
		return $this->getLevelNonNull()->getServer()->redstoneEnabled;
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$place = $this->getLevelNonNull()->setBlock($this, $this, true);
		if($this->isActivated()){
			$this->activate();
		}
		return $place;
	}
	public function onBreak(Item $item, Player $player = null) : bool{
		$break = $this->getLevelNonNull()->setBlock($this, new Air(), true);
		if($this->isActivated()){
			$this->deactivate();
		}
		return $break;
	}
	public function activateBlockWithoutWire(Block $block) : void{
		if(($block instanceof Door) or ($block instanceof Trapdoor) or ($block instanceof FenceGate)){
			if(!$block->isOpened()){
				$block->onActivate(new Item(0));
			}
		}
		if($block->getId() === Block::TNT){
			$block->ignite();
		}
		if($block->getId() === Block::REDSTONE_LAMP){
			$block->turnOn();
		}
		if($block->getId() === Block::DROPPER or $block->getId() === Block::DISPENSER or $block->getId() === Block::PISTON or $block->getId() === Block::STICKY_PISTON){
			$block->activate();
		}
		if($block->getId() === Block::UNPOWERED_REPEATER){
			if($this->equals($block->getSide($block->getDirection()))){
				$block->activate();
			}
		}
	}
	public function activateBlock(Block $block) : void{
		$this->activateBlockWithoutWire($block);
		if($block->getId() === Block::REDSTONE_WIRE){
			$wire = $block;
			$wire->calcSignal($this->maxStrength, RedstoneWire::ON);
		}
	}
	public function deactivateBlock(Block $block) : void{
		$this->deactivateBlockWithoutWire($block);
		if($block->getId() === Block::REDSTONE_WIRE){
			$wire = $block;
			$wire->calcSignal(0, RedstoneWire::OFF);
		}
	}
	public function deactivateBlockWithoutWire(Block $block) : void{
		if(!$this->checkPower($block)){
			if(($block instanceof Door) or ($block instanceof Trapdoor) or ($block instanceof FenceGate)){
				if($block->isOpened()){
					$block->onActivate(new Item(0));
				}
			}
	    	if($block->getId() === Block::PISTON or $block->getId() === Block::STICKY_PISTON){
		    	$block->deactivate();
	    	}
			if($block->getId() === Block::LIT_REDSTONE_LAMP){
                $block->turnOff();
            }
		}
		if($block->getId() === Block::POWERED_REPEATER){
			if($this->equals($block->getSide($block->getDirection()))){
				$block->deactivate();
			}
		}
	}
	public function activate(array $ignore = []){
		if($this->canCalc()){
			$this->activated = true;
			$sides = [
				Vector3::SIDE_EAST,
				Vector3::SIDE_WEST,
				Vector3::SIDE_SOUTH,
				Vector3::SIDE_NORTH,
				Vector3::SIDE_DOWN
			];
			foreach($sides as $side){
				if(!in_array($side, $ignore)){
					$block = $this->getSide($side);
					$this->activateBlock($block);
				}
			}
		}
	}
	public function deactivate(array $ignore = []){
		if($this->canCalc()){
			$this->activated = false;
			$sides = [
				Vector3::SIDE_EAST,
				Vector3::SIDE_WEST,
				Vector3::SIDE_SOUTH,
				Vector3::SIDE_NORTH
			];
			foreach($sides as $side){
				if(!in_array($side, $ignore)){
					$block = $this->getSide($side);
					$this->deactivateBlock($block);
				}
			}
			if(!in_array(Vector3::SIDE_DOWN, $ignore)){
				$block = $this->getSide(Vector3::SIDE_DOWN);
				if(!$this->checkPower($block)){
					if($block->getId() === Block::LIT_REDSTONE_LAMP){
						$block->turnOff();
					}
				}
				$block = $this->getSide(Vector3::SIDE_DOWN, 2);
				$this->deactivateBlock($block);
			}
		}
	}
	public function checkPower(Block $block, array $ignore = [], $ignoreWire = false) : bool{
		if($block instanceof PoweredRepeater){
			if($block->getSide($block->getDirection())->isActivated($block)){
				return true;
			}
			return false;
		}
		$sides = [
			Vector3::SIDE_EAST,
			Vector3::SIDE_WEST,
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_NORTH
		];
		foreach($sides as $side){
			if(!in_array($side, $ignore)){
				$pos = $block->getSide($side);
				if($pos instanceof RedstoneSource){
					if($pos->isActivated($this)){
						if(($ignoreWire and $pos->getId() !== self::REDSTONE_WIRE) or (!$ignoreWire and $pos->getId() !== self::REDSTONE_WIRE)){
							return true;
						}
						if(!$ignoreWire and $pos->getId() === self::REDSTONE_WIRE){
							$cb = $pos->getUnconnectedSide();
							if(!$cb[0]){
								return false;
							}
							if($this->equals($pos->getSide($cb[0]))){
								return true;
							}
						}
					}
				}
			}
		}
		if($block->getId() === Block::LIT_REDSTONE_LAMP and !in_array(Vector3::SIDE_UP, $ignore)){
			$pos = $block->getSide(Vector3::SIDE_UP);
			if($pos instanceof RedstoneSource and $pos->getId() !== self::REDSTONE_TORCH){
				if($pos->isActivated($this)){
					return true;
				}
			}
		}
		return false;
	}
	public function checkTorchOn(Block $pos, array $ignore = []) : void{
		$sides = [
			Vector3::SIDE_EAST,
			Vector3::SIDE_WEST,
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_NORTH,
			Vector3::SIDE_UP
		];
		foreach($sides as $side){
			if(!in_array($side, $ignore)){
				$block = $pos->getSide($side);
				if($block->getId() === self::REDSTONE_TORCH){
					$faces = [
						1 => 4,
						2 => 5,
						3 => 2,
						4 => 3,
						5 => 0,
						6 => 0,
						0 => 0,
					];
					if($block->getSide($faces[$block->meta])->equals($pos)){
						$ignoreBlock = $this->getSide(static::getOppositeSide($faces[$block->meta]));
						$block->turnOff((string) Level::chunkBlockHash($ignoreBlock->x, $ignoreBlock->y, $ignoreBlock->z));
					}
				}
			}
		}
	}
	public function checkTorchOff(Block $pos, array $ignore = []) : void{
		$sides = [
			Vector3::SIDE_EAST,
			Vector3::SIDE_WEST,
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_NORTH,
			Vector3::SIDE_UP
		];
		foreach($sides as $side){
			if(!in_array($side, $ignore)){
				$block = $pos->getSide($side);
				if($block->getId() === self::UNLIT_REDSTONE_TORCH){
					$faces = [
						1 => 4,
						2 => 5,
						3 => 2,
						4 => 3,
						5 => 0,
						6 => 0,
						0 => 0,
					];
					if($block->getSide($faces[$block->meta])->equals($pos)){
						$ignoreBlock = $this->getSide(static::getOppositeSide($faces[$block->meta]));
						$block->turnOn((string) Level::chunkBlockHash($ignoreBlock->x, $ignoreBlock->y, $ignoreBlock->z));
					}
				}
			}
		}
	}
	public function getStrength() : int{
		if($this->isActivated()){
			return $this->maxStrength;
		}
		return 0;
	}}