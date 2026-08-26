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
namespace pocketmine\command;
use InvalidArgumentException;use pocketmine\lang\TextContainer;use pocketmine\permission\PermissibleBase;use pocketmine\permission\Permission;use pocketmine\permission\PermissionAttachment;use pocketmine\permission\PermissionAttachmentInfo;use pocketmine\plugin\Plugin;use pocketmine\Server;use function explode;use function trim;use const PHP_INT_MAX;
class ConsoleCommandSender implements CommandSender{
	private $perm;
	protected $lineHeight = null;
	public function __construct(){
		$this->perm = new PermissibleBase($this);
	}
	public function isPermissionSet($name) : bool{
		return $this->perm->isPermissionSet($name);
	}
	public function hasPermission($name) : bool{
		return $this->perm->hasPermission($name);
	}
	public function addAttachment(Plugin $plugin, string $name = null, bool $value = null) : PermissionAttachment{
		return $this->perm->addAttachment($plugin, $name, $value);
	}
	public function removeAttachment(PermissionAttachment $attachment){
		$this->perm->removeAttachment($attachment);
	}
	public function recalculatePermissions(){
		$this->perm->recalculatePermissions();
	}
	public function getEffectivePermissions() : array{
		return $this->perm->getEffectivePermissions();
	}
	public function getServer(){
		return Server::getInstance();
	}
	public function sendMessage($message){
		if($message instanceof TextContainer){
			$message = $this->getServer()->getLanguage()->translate($message);
		}else{
			$message = $this->getServer()->getLanguage()->translateString($message);
		}
		foreach(explode("\n", trim($message)) as $line){
			\GlobalLogger::get()->info($line);
		}
	}
	public function getName() : string{
		return "CONSOLE";
	}
	public function isOp() : bool{
		return true;
	}
	public function setOp(bool $value){
	}
	public function getScreenLineHeight() : int{
		return $this->lineHeight ?? PHP_INT_MAX;
	}
	public function setScreenLineHeight(int $height = null){
		if($height !== null and $height < 1){
			throw new InvalidArgumentException("Line height must be at least 1");
		}
		$this->lineHeight = $height;
	}}