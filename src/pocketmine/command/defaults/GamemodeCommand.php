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
use pocketmine\command\Command;use pocketmine\command\CommandSender;use pocketmine\command\utils\InvalidCommandSyntaxException;use pocketmine\lang\TranslationContainer;use pocketmine\Player;use pocketmine\Server;use pocketmine\utils\TextFormat;use function count;
class GamemodeCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.gamemode.description",
			"%commands.gamemode.usage"
		);
		$this->setPermission("pocketmine.command.gamemode");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) === 0){
			throw new InvalidCommandSyntaxException();
		}
		$gameMode = Server::getGamemodeFromString($args[0]);
		if($gameMode === -1){
			$sender->sendMessage("Unknown game mode");
			return true;
		}
		if(isset($args[1])){
			$target = $sender->getServer()->getPlayer($args[1]);
			if($target === null){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
				return true;
			}
		}elseif($sender instanceof Player){
			$target = $sender;
		}else{
			throw new InvalidCommandSyntaxException();
		}
		$target->setGamemode($gameMode);
		if($gameMode !== $target->getGamemode()){
			$sender->sendMessage("Game mode change for " . $target->getName() . " failed!");
		}else{
			if($target === $sender){
				$sender->sendMessage($sender->getServer()->getLanguage()->translateString(
					"commands.gamemode.success.self",
					[Server::getGamemodeString($gameMode)]
				));
			}else{
				$target->sendMessage($target->getServer()->getLanguage()->translateString(
					"gameMode.changed",
					[Server::getGamemodeString($gameMode)]
				));
				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.gamemode.success.other", [Server::getGamemodeString($gameMode), $target->getName()]));
			}
		}
		return true;
	}}