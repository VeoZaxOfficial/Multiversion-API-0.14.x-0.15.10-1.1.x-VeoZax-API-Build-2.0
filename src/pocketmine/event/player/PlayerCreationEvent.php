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
namespace pocketmine\event\player;
use pocketmine\event\Event;use pocketmine\network\SourceInterface;use pocketmine\Player;use RuntimeException;use function is_a;
class PlayerCreationEvent extends Event{
	private $interface;
	private $address;
	private $port;
	private $baseClass;
	private $playerClass;
	public function __construct(SourceInterface $interface, $baseClass, $playerClass, string $address, int $port){
		$this->interface = $interface;
		$this->address = $address;
		$this->port = $port;
		if(!is_a($baseClass, Player::class, true)){
			throw new RuntimeException("Base class $baseClass must extend " . Player::class);
		}
		$this->baseClass = $baseClass;
		if(!is_a($playerClass, Player::class, true)){
			throw new RuntimeException("Class $playerClass must extend " . Player::class);
		}
		$this->playerClass = $playerClass;
	}
	public function getInterface() : SourceInterface{
		return $this->interface;
	}
	public function getAddress() : string{
		return $this->address;
	}
	public function getPort() : int{
		return $this->port;
	}
	public function getBaseClass(){
		return $this->baseClass;
	}
	public function setBaseClass($class){
		if(!is_a($class, $this->baseClass, true)){
			throw new RuntimeException("Base class $class must extend " . $this->baseClass);
		}
		$this->baseClass = $class;
	}
	public function getPlayerClass(){
		return $this->playerClass;
	}
	public function setPlayerClass($class){
		if(!is_a($class, $this->baseClass, true)){
			throw new RuntimeException("Class $class must extend " . $this->baseClass);
		}
		$this->playerClass = $class;
	}}