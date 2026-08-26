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
namespace pocketmine\event\block;
use InvalidArgumentException;use pocketmine\block\Block;use pocketmine\event\Cancellable;use pocketmine\Player;use function count;
class SignChangeEvent extends BlockEvent implements Cancellable{
	private $player;
	private $lines = [];
	public function __construct(Block $theBlock, Player $thePlayer, array $theLines){
		parent::__construct($theBlock);
		$this->player = $thePlayer;
		$this->setLines($theLines);
	}
	public function getPlayer() : Player{
		return $this->player;
	}
	public function getLines() : array{
		return $this->lines;
	}
	public function getLine(int $index) : string{
		if($index < 0 or $index > 3){
			throw new InvalidArgumentException("Index must be in the range 0-3!");
		}
		return $this->lines[$index];
	}
	public function setLines(array $lines) : void{
		if(count($lines) !== 4){
			throw new InvalidArgumentException("Array size must be 4!");
		}
		$this->lines = $lines;
	}
	public function setLine(int $index, string $line) : void{
		if($index < 0 or $index > 3){
			throw new InvalidArgumentException("Index must be in the range 0-3!");
		}
		$this->lines[$index] = $line;
	}}