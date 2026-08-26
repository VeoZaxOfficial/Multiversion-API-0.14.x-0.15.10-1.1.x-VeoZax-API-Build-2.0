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
use pocketmine\command\Command;use pocketmine\command\CommandSender;use pocketmine\command\utils\InvalidCommandSyntaxException;use pocketmine\Player;use pocketmine\utils\TextFormat;use function array_shift;use function count;use function strtolower;
class AdministratorsCommand extends VanillaCommand{
	private const PREFIX = "\xc2\xa78[\xc2\xa79Veo\xc2\xa7bZax\xc2\xa7cAPI\xc2\xa78] ";
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Adds or removes a player from the administrators list",
			"/administrators <add|remove> <player> <password>"
		);
		$this->setPermission("pocketmine.command.administrators");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender->getServer()->isAdministrator($sender->getName()) and !$sender->getServer()->isServerOwner($sender->getName())){
			$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
			return true;
		}
		if(count($args) < 3){
			throw new InvalidCommandSyntaxException();
		}
		$action = strtolower(array_shift($args));
		if($action !== "add" and $action !== "remove"){
			throw new InvalidCommandSyntaxException();
		}
		$name = array_shift($args);
		$password = array_shift($args);
		if(!Player::isValidUserName($name)){
			throw new InvalidCommandSyntaxException();
		}
		$server = $sender->getServer();
		if($server->isAdministratorsLocked()){
			$sender->sendMessage(self::PREFIX . "\xc2\xa77The \xc2\xa7cAdministrators Protocol \xc2\xa77was Locked by an \xc2\xa7bOwner\xc2\xa77 of this server. You cannot \xc2\xa7eRemove\xc2\xa78/\xc2\xa7eAdd\xc2\xa77 anyone into \xc2\xa7cAdministrators\xc2\xa77 list until the \xc2\xa7bOwner \xc2\xa77unlock the \xc2\xa7cAdministrator Protocol.");
			return true;
		}
		if($password !== $server->getAdministratorsPassword()){
			$sender->sendMessage(self::PREFIX . "\xc2\xa77The password you entered was \xc2\xa7cIncorrect. \xc2\xa77Due to that Reason, Your Request to this command has been \xc2\xa7cRejected.");
			return true;
		}
		$player = $server->getOfflinePlayer($name);
		if($action === "add"){
			if($server->isAdministrator($player->getName())){
				$sender->sendMessage(self::PREFIX . "\xc2\xa77That player is already an \xc2\xa7cAdministrator \xc2\xa77of this server.");
				return true;
			}
			$server->addAdministrator($player->getName());
			Command::broadcastCommandMessage($sender, self::PREFIX . "\xc2\xa7b" . $player->getName() . " \xc2\xa77is now an \xc2\xa7cAdministrator \xc2\xa77of this server.");
			if($player instanceof Player){
				$player->sendMessage(self::PREFIX . "\xc2\xa7aCongrats! \xc2\xa77You are now an \xc2\xa7cAdministrator \xc2\xa77of this server!");
			}
		}else{
			if(!$server->isAdministrator($player->getName())){
				$sender->sendMessage(self::PREFIX . "\xc2\xa77That player is not an \xc2\xa7cAdministrator \xc2\xa77of this server.");
				return true;
			}
			$server->removeAdministrator($player->getName());
			Command::broadcastCommandMessage($sender, self::PREFIX . "\xc2\xa7b" . $player->getName() . " \xc2\xa77is no longer an \xc2\xa7cAdministrator \xc2\xa77of this server.");
			if($player instanceof Player){
				$player->sendMessage(self::PREFIX . "\xc2\xa7cSorry, \xc2\xa77you have been removed from the \xc2\xa7cAdministrators \xc2\xa77of this server.");
			}
		}
		return true;
	}}