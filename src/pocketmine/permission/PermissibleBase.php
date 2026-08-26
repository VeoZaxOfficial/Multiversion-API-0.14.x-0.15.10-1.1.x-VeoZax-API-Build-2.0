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
namespace pocketmine\permission;
use pocketmine\plugin\Plugin;use pocketmine\plugin\PluginException;use pocketmine\timings\Timings;use function spl_object_hash;
class PermissibleBase implements Permissible{
	private $opable;
	private $parent = null;
	private $attachments = [];
	private $permissions = [];
	public function __construct(ServerOperator $opable){
		$this->opable = $opable;
		if($opable instanceof Permissible){
			$this->parent = $opable;
		}
	}
	public function isOp() : bool{
		return $this->opable->isOp();
	}
	public function setOp(bool $value){
		$this->opable->setOp($value);
	}
	public function isPermissionSet($name) : bool{
		return isset($this->permissions[$name instanceof Permission ? $name->getName() : $name]);
	}
	public function hasPermission($name) : bool{
		if($name instanceof Permission){
			$name = $name->getName();
		}
		if($this->isPermissionSet($name)){
			return $this->permissions[$name]->getValue();
		}
		if(($perm = PermissionManager::getInstance()->getPermission($name)) !== null){
			$perm = $perm->getDefault();
			return $perm === Permission::DEFAULT_TRUE or ($this->isOp() and $perm === Permission::DEFAULT_OP) or (!$this->isOp() and $perm === Permission::DEFAULT_NOT_OP);
		}else{
			return Permission::$DEFAULT_PERMISSION === Permission::DEFAULT_TRUE or ($this->isOp() and Permission::$DEFAULT_PERMISSION === Permission::DEFAULT_OP) or (!$this->isOp() and Permission::$DEFAULT_PERMISSION === Permission::DEFAULT_NOT_OP);
		}
	}
	public function addAttachment(Plugin $plugin, string $name = null, bool $value = null) : PermissionAttachment{
		if(!$plugin->isEnabled()){
			throw new PluginException("Plugin " . $plugin->getDescription()->getName() . " is disabled");
		}
		$result = new PermissionAttachment($plugin, $this->parent ?? $this);
		$this->attachments[spl_object_hash($result)] = $result;
		if($name !== null and $value !== null){
			$result->setPermission($name, $value);
		}
		$this->recalculatePermissions();
		return $result;
	}
	public function removeAttachment(PermissionAttachment $attachment){
		if(isset($this->attachments[spl_object_hash($attachment)])){
			unset($this->attachments[spl_object_hash($attachment)]);
			if(($ex = $attachment->getRemovalCallback()) !== null){
				$ex->attachmentRemoved($attachment);
			}
			$this->recalculatePermissions();
		}
	}
	public function recalculatePermissions(){
		Timings::$permissibleCalculationTimer->startTiming();
		$this->clearPermissions();
		$permManager = PermissionManager::getInstance();
		$defaults = $permManager->getDefaultPermissions($this->isOp());
		$permManager->subscribeToDefaultPerms($this->isOp(), $this->parent ?? $this);
		foreach($defaults as $perm){
			$name = $perm->getName();
			$this->permissions[$name] = new PermissionAttachmentInfo($this->parent ?? $this, $name, null, true);
			$permManager->subscribeToPermission($name, $this->parent ?? $this);
			$this->calculateChildPermissions($perm->getChildren(), false, null);
		}
		foreach($this->attachments as $attachment){
			$this->calculateChildPermissions($attachment->getPermissions(), false, $attachment);
		}
		Timings::$permissibleCalculationTimer->stopTiming();
	}
	public function clearPermissions(){
		$permManager = PermissionManager::getInstance();
		$permManager->unsubscribeFromAllPermissions($this->parent ?? $this);
		$permManager->unsubscribeFromDefaultPerms(false, $this->parent ?? $this);
		$permManager->unsubscribeFromDefaultPerms(true, $this->parent ?? $this);
		$this->permissions = [];
	}
	private function calculateChildPermissions(array $children, bool $invert, ?PermissionAttachment $attachment){
		$permManager = PermissionManager::getInstance();
		foreach($children as $name => $v){
			$perm = $permManager->getPermission($name);
			$value = ($v xor $invert);
			$this->permissions[$name] = new PermissionAttachmentInfo($this->parent ?? $this, $name, $attachment, $value);
			$permManager->subscribeToPermission($name, $this->parent ?? $this);
			if($perm instanceof Permission){
				$this->calculateChildPermissions($perm->getChildren(), !$value, $attachment);
			}
		}
	}
	public function getEffectivePermissions() : array{
		return $this->permissions;
	}}