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
use pocketmine\command\Command;use pocketmine\command\CommandSender;use pocketmine\command\utils\InvalidCommandSyntaxException;use pocketmine\lang\TranslationContainer;use pocketmine\level\Level;use function count;
class DifficultyCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.difficulty.description",
			"%commands.difficulty.usage"
		);
		$this->setPermission("pocketmine.command.difficulty");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) !== 1){
			throw new InvalidCommandSyntaxException();
		}
		$difficulty = Level::getDifficultyFromString($args[0]);
		if($sender->getServer()->isHardcore()){
			$difficulty = Level::DIFFICULTY_HARD;
		}
		if($difficulty !== -1){
			$sender->getServer()->setConfigInt("difficulty", $difficulty);
			foreach($sender->getServer()->getLevels() as $level){
				$level->setDifficulty($difficulty);
			}
			Command::broadcastCommandMessage($sender, new TranslationContainer("commands.difficulty.success", [$difficulty]));
		}else{
			throw new InvalidCommandSyntaxException();
		}
		return true;
	}}