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
namespace pocketmine\item;
use pocketmine\item\enchantment\Enchantment;use pocketmine\nbt\tag\ByteTag;use function lcg_value;use function min;
abstract class Durable extends Item{
	public function isUnbreakable() : bool{
		return $this->getNamedTag()->getByte("Unbreakable", 0) !== 0;
	}
	public function setUnbreakable(bool $value = true){
		$this->setNamedTagEntry(new ByteTag("Unbreakable", $value ? 1 : 0));
	}
	public function applyDamage(int $amount) : bool{
		if($this->isUnbreakable() or $this->isBroken()){
			return false;
		}
		$amount -= $this->getUnbreakingDamageReduction($amount);
		$this->meta = min($this->meta + $amount, $this->getMaxDurability());
		if($this->isBroken()){
			$this->onBroken();
		}
		return true;
	}
	protected function getUnbreakingDamageReduction(int $amount) : int{
		if(($unbreakingLevel = $this->getEnchantmentLevel(Enchantment::UNBREAKING)) > 0){
			$negated = 0;
			$chance = 1 / ($unbreakingLevel + 1);
			for($i = 0; $i < $amount; ++$i){
				if(lcg_value() > $chance){
					$negated++;
				}
			}
			return $negated;
		}
		return 0;
	}
	protected function onBroken() : void{
		$this->pop();
	}
	abstract public function getMaxDurability() : int;
	public function isBroken() : bool{
		return $this->meta >= $this->getMaxDurability();
	}}