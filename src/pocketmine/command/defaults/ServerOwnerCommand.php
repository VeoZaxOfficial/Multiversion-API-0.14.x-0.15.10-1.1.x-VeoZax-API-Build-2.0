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
namespace pocketmine\command\defaults;
use pocketmine\command\CommandSender;use pocketmine\command\utils\InvalidCommandSyntaxException;use pocketmine\Player;use pocketmine\utils\TextFormat;use function array_shift;use function count;use function strtolower;
class ServerOwnerCommand extends VanillaCommand{
	private const PREFIX = "\xc2\xa78[\xc2\xa79Veo\xc2\xa7bZax\xc2\xa7cAPI\xc2\xa78] ";
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Adds, removes, locks or unlocks server owners",
			"/server-owner <add|remove> <player> <password>\n/server-owner <lock|unlock>"
		);
		$this->setPermission("pocketmine.command.server-owner");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		$server = $sender->getServer();
		if(!$server->isServerOwner($sender->getName())){
			$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
			return true;
		}
		if(count($args) < 1){
			throw new InvalidCommandSyntaxException();
		}
		$action = strtolower(array_shift($args));
		switch($action){
			case "lock":
			case "unlock":
				$this->handleLock($sender, $server, $action);
				return true;
			case "add":
			case "remove":
				$this->handleAddRemove($sender, $server, $action, $args);
				return true;
			default:
				throw new InvalidCommandSyntaxException();
		}
	}
	private function handleLock(CommandSender $sender, \pocketmine\Server $server, string $action) : void{
		if(!$server->isMainOwner($sender->getName())){
			$sender->sendMessage(self::PREFIX . "\xc2\xa7cYou are cannot use this command. Only Main server can use it.");
			return;
		}
		if($action === "lock"){
			$server->lockOwners();
			$sender->sendMessage(self::PREFIX . "\xc2\xa77You are now \xc2\xa7cLocked down\xc2\xa77 the \xc2\xa7cOwners Protocol\xc2\xa77 of this server");
		}else{
			$server->unlockOwners();
			$sender->sendMessage(self::PREFIX . "\xc2\xa77You are now \xc2\xa7aUnlocked\xc2\xa77 the \xc2\xa7cOwners Protocol\xc2\xa77 of this server");
		}
	}
	private function handleAddRemove(CommandSender $sender, \pocketmine\Server $server, string $action, array $args) : void{
		if(count($args) < 2){
			throw new InvalidCommandSyntaxException();
		}
		$name = array_shift($args);
		$password = array_shift($args);
		if(!Player::isValidUserName($name)){
			throw new InvalidCommandSyntaxException();
		}
		if($server->isOwnersLocked()){
			$sender->sendMessage(self::PREFIX . "\xc2\xa77The \xc2\xa7cOwners Protocol \xc2\xa77was Locked by the \xc2\xa7bMain Owner\xc2\xa77 of this server. You cannot \xc2\xa7eRemove\xc2\xa78/\xc2\xa7eAdd\xc2\xa77 anyone into \xc2\xa7cOwners\xc2\xa77 list until the \xc2\xa7bMain Owner \xc2\xa77unlock the \xc2\xa7cOwners Protocol.");
			return;
		}
		if($password !== $server->getOwnerAccessPassword()){
			$sender->sendMessage(self::PREFIX . "\xc2\xa77The password you entered was \xc2\xa7cIncorrect. \xc2\xa77Due to that Reason, Your Request to this command has been \xc2\xa7cRejected.");
			return;
		}
		$player = $server->getOfflinePlayer($name);
		$targetName = $player->getName();
		if($server->isMainOwner($targetName)){
			$sender->sendMessage(self::PREFIX . "\xc2\xa7cYou cannot " . ($action === "add" ? "add" : "remove") . " the Main Owner of this server.");
			return;
		}
		if($action === "add"){
			if($server->isSubOwner($targetName)){
				$sender->sendMessage(self::PREFIX . "\xc2\xa77That player is already an Owner of this server.");
				return;
			}
			$server->addSubOwner($targetName);
			if($player instanceof Player){
				$player->sendMessage(self::PREFIX . "\xc2\xa7aCongrats! \xc2\xa77You are now an \xc2\xa7bOwner \xc2\xa77of this Server!");
			}
			$server->broadcastMessage(self::PREFIX . "\xc2\xa7b" . $targetName . " \xc2\xa77is now an \xc2\xa7bOwner \xc2\xa77of this Server!");
		}else{
			if(!$server->isSubOwner($targetName)){
				$sender->sendMessage(self::PREFIX . "\xc2\xa77That player is not an Owner of this server.");
				return;
			}
			$server->removeSubOwner($targetName);
			if($player instanceof Player){
				$player->sendMessage(self::PREFIX . "\xc2\xa7cSorry, \xc2\xa77You has been removed from the \xc2\xa7bOwnership \xc2\xa77of this Server.");
			}
			$server->broadcastMessage(self::PREFIX . "\xc2\xa7b" . $targetName . " \xc2\xa77has been removed from the \xc2\xa7bOwnership \xc2\xa77of this Server.");
		}
	}}