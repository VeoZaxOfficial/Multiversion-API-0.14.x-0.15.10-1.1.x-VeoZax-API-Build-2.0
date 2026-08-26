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
use pocketmine\command\CommandSender;use pocketmine\command\utils\InvalidCommandSyntaxException;use function array_shift;use function count;use function strtolower;
class AdministratorCommand extends VanillaCommand{
	private const PREFIX = "\xc2\xa78[\xc2\xa79Veo\xc2\xa7bZax\xc2\xa7cAPI\xc2\xa78] ";
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Locks or unlocks the Administrators Protocol",
			"/administrator <lock|unlock>"
		);
		$this->setPermission("pocketmine.command.administrator");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) < 1){
			throw new InvalidCommandSyntaxException();
		}
		$action = strtolower(array_shift($args));
		if($action !== "lock" and $action !== "unlock"){
			throw new InvalidCommandSyntaxException();
		}
		$server = $sender->getServer();
		if(!$server->isServerOwner($sender->getName())){
			$sender->sendMessage(self::PREFIX . "\xc2\xa7cYou cannot use this command. Only Server Owners can use it.");
			return true;
		}
		if($action === "lock"){
			$server->lockAdministrators();
			$sender->sendMessage(self::PREFIX . "\xc2\xa77You are now \xc2\xa7cLocked down\xc2\xa77 the \xc2\xa7cAdministrator Protocol\xc2\xa77 of this server");
		}else{
			$server->unlockAdministrators();
			$sender->sendMessage(self::PREFIX . "\xc2\xa77You are now \xc2\xa7aUnlocked\xc2\xa77 the \xc2\xa7cAdministrator Protocol\xc2\xa77 of this server");
		}
		return true;
	}}