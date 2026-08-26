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
namespace pocketmine\network\mcpe\protocol\types;
use pocketmine\utils\Color;
class MapDecoration{
	public const TYPE_PLAYER = 0;
	public const TYPE_FRAME = 1;
	public const TYPE_RED_MARKER = 2;
	public const TYPE_BLUE_MARKER = 3;
	public const TYPE_TARGET_X = 4;
	public const TYPE_TARGET_POINT = 5;
	public const TYPE_PLAYER_OFF_MAP = 6;
	public const TYPE_PLAYER_OFF_LIMITS = 7;
	public const TYPE_MANSION = 8;
	public const TYPE_MONUMENT = 9;
	private $icon;
	private $rotation;
	private $xOffset;
	private $yOffset;
	private $label;
	private $color;
	public function __construct(int $icon, int $rotation, int $xOffset, int $yOffset, string $label, Color $color){
		$this->icon = $icon;
		$this->rotation = $rotation;
		$this->xOffset = $xOffset;
		$this->yOffset = $yOffset;
		$this->label = $label;
		$this->color = $color;
	}
	public function getIcon() : int{
		return $this->icon;
	}
	public function getRotation() : int{
		return $this->rotation;
	}
	public function getXOffset() : int{
		return $this->xOffset;
	}
	public function getYOffset() : int{
		return $this->yOffset;
	}
	public function getLabel() : string{
		return $this->label;
	}
	public function getColor() : Color{
		return $this->color;
	}}