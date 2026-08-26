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
use pocketmine\event\entity\EntityDamageEvent;use pocketmine\item\enchantment\Enchantment;use pocketmine\item\enchantment\ProtectionEnchantment;use pocketmine\math\Vector3;use pocketmine\nbt\tag\IntTag;use pocketmine\Player;use pocketmine\utils\Binary;use pocketmine\utils\Color;use function lcg_value;use function mt_rand;
abstract class Armor extends Durable{
	public const TAG_CUSTOM_COLOR = "customColor"; 
	public function getMaxStackSize() : int{
		return 1;
	}
	abstract public function getArmorSlot() : int;
	public function getCustomColor() : ?Color{
		if($this->getNamedTag()->hasTag(self::TAG_CUSTOM_COLOR, IntTag::class)){
			return Color::fromARGB(Binary::unsignInt($this->getNamedTag()->getInt(self::TAG_CUSTOM_COLOR)));
		}
		return null;
	}
	public function setCustomColor(Color $color) : void{
		$this->setNamedTagEntry(new IntTag(self::TAG_CUSTOM_COLOR, Binary::signInt($color->toARGB())));
	}
	public function clearCustomColor() : void{
		$this->removeNamedTagEntry(self::TAG_CUSTOM_COLOR);
	}
	public function getEnchantmentProtectionFactor(EntityDamageEvent $event) : int{
		$epf = 0;
		foreach($this->getEnchantments() as $enchantment){
			$type = $enchantment->getType();
			if($type instanceof ProtectionEnchantment and $type->isApplicable($event)){
				$epf += $type->getProtectionFactor($enchantment->getLevel());
			}
		}
		return $epf;
	}
	protected function getUnbreakingDamageReduction(int $amount) : int{
		if(($unbreakingLevel = $this->getEnchantmentLevel(Enchantment::UNBREAKING)) > 0){
			$negated = 0;
			$chance = 1 / ($unbreakingLevel + 1);
			for($i = 0; $i < $amount; ++$i){
				if(mt_rand(1, 100) > 60 and lcg_value() > $chance){ 
					$negated++;
				}
			}
			return $negated;
		}
		return 0;
	}
	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		$current = $player->getArmorInventory()->getItem($this->getArmorSlot());
		if($current->isNull()){
			$player->getArmorInventory()->setItem($this->getArmorSlot(), $this->pop());
			return true;
		}elseif(!$current->equals($this) and $player->getInventory()->canAddItem($current)){
			$player->getArmorInventory()->setItem($this->getArmorSlot(), $this->pop());
			$player->getInventory()->addItem($current);
			return true;
		}
		return false;
	}}