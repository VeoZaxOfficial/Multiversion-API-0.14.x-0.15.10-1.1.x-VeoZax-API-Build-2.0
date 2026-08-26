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
namespace pocketmine\entity\object;
class PaintingMotive{
	protected static $motives = [];
	public static function init() : void{
		foreach([
			new PaintingMotive(1, 1, "Alban"),
			new PaintingMotive(1, 1, "Aztec"),
			new PaintingMotive(1, 1, "Aztec2"),
			new PaintingMotive(1, 1, "Bomb"),
			new PaintingMotive(1, 1, "Kebab"),
			new PaintingMotive(1, 1, "Plant"),
			new PaintingMotive(1, 1, "Wasteland"),
			new PaintingMotive(1, 2, "Graham"),
			new PaintingMotive(1, 2, "Wanderer"),
			new PaintingMotive(2, 1, "Courbet"),
			new PaintingMotive(2, 1, "Creebet"),
			new PaintingMotive(2, 1, "Pool"),
			new PaintingMotive(2, 1, "Sea"),
			new PaintingMotive(2, 1, "Sunset"),
			new PaintingMotive(2, 2, "Bust"),
			new PaintingMotive(2, 2, "Earth"),
			new PaintingMotive(2, 2, "Fire"),
			new PaintingMotive(2, 2, "Match"),
			new PaintingMotive(2, 2, "SkullAndRoses"),
			new PaintingMotive(2, 2, "Stage"),
			new PaintingMotive(2, 2, "Void"),
			new PaintingMotive(2, 2, "Water"),
			new PaintingMotive(2, 2, "Wind"),
			new PaintingMotive(2, 2, "Wither"),
			new PaintingMotive(4, 2, "Fighters"),
			new PaintingMotive(4, 3, "DonkeyKong"),
			new PaintingMotive(4, 3, "Skeleton"),
			new PaintingMotive(4, 4, "BurningSkull"),
			new PaintingMotive(4, 4, "Pigscene"),
			new PaintingMotive(4, 4, "Pointer")
		] as $motive){
			self::registerMotive($motive);
		}
	}
	public static function registerMotive(PaintingMotive $motive) : void{
		self::$motives[$motive->getName()] = $motive;
	}
	public static function getMotiveByName(string $name) : ?PaintingMotive{
		return self::$motives[$name] ?? null;
	}
	public static function getAll() : array{
		return self::$motives;
	}
	protected $name;
	protected $width;
	protected $height;
	public function __construct(int $width, int $height, string $name){
		$this->name = $name;
		$this->width = $width;
		$this->height = $height;
	}
	public function getName() : string{
		return $this->name;
	}
	public function getWidth() : int{
		return $this->width;
	}
	public function getHeight() : int{
		return $this->height;
	}
	public function __toString() : string{
		return "PaintingMotive(name: " . $this->getName() . ", height: " . $this->getHeight() . ", width: " . $this->getWidth() . ")";
	}}