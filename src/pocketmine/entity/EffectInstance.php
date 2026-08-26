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
namespace pocketmine\entity;
use InvalidArgumentException;use pocketmine\utils\Color;use function max;use const INT32_MAX;
class EffectInstance{
	private $effectType;
	private $duration;
	private $amplifier;
	private $visible;
	private $ambient;
	private $color;
	public function __construct(Effect $effectType, ?int $duration = null, int $amplifier = 0, bool $visible = true, bool $ambient = false, ?Color $overrideColor = null){
		$this->effectType = $effectType;
		$this->setDuration($duration ?? $effectType->getDefaultDuration());
		$this->amplifier = $amplifier;
		$this->visible = $visible;
		$this->ambient = $ambient;
		$this->color = $overrideColor ?? $effectType->getColor();
	}
	public function getId() : int{
		return $this->effectType->getId();
	}
	public function getType() : Effect{
		return $this->effectType;
	}
	public function getDuration() : int{
		return $this->duration;
	}
	public function setDuration(int $duration) : EffectInstance{
		if($duration < 0 or $duration > INT32_MAX){
			throw new InvalidArgumentException("Effect duration must be in range 0 - " . INT32_MAX . ", got $duration");
		}
		$this->duration = $duration;
		return $this;
	}
	public function decreaseDuration(int $ticks) : EffectInstance{
		$this->duration = max(0, $this->duration - $ticks);
		return $this;
	}
	public function hasExpired() : bool{
		return $this->duration <= 0;
	}
	public function getAmplifier() : int{
		return $this->amplifier;
	}
	public function getEffectLevel() : int{
		return $this->amplifier + 1;
	}
	public function setAmplifier(int $amplifier) : EffectInstance{
		$this->amplifier = $amplifier;
		return $this;
	}
	public function isVisible() : bool{
		return $this->visible;
	}
	public function setVisible(bool $visible = true) : EffectInstance{
		$this->visible = $visible;
		return $this;
	}
	public function isAmbient() : bool{
		return $this->ambient;
	}
	public function setAmbient(bool $ambient = true) : EffectInstance{
		$this->ambient = $ambient;
		return $this;
	}
	public function getColor() : Color{
		return clone $this->color;
	}
	public function setColor(Color $color) : EffectInstance{
		$this->color = clone $color;
		return $this;
	}
	public function resetColor() : EffectInstance{
		$this->color = $this->effectType->getColor();
		return $this;
	}}