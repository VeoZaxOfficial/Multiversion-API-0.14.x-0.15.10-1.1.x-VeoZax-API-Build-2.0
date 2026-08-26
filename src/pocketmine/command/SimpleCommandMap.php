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
use pocketmine\command\defaults\AdministratorCommand;use pocketmine\command\defaults\AdministratorsCommand;use pocketmine\command\defaults\BanCommand;use pocketmine\command\defaults\BanIpCommand;use pocketmine\command\defaults\BanListCommand;use pocketmine\command\defaults\DefaultGamemodeCommand;use pocketmine\command\defaults\DifficultyCommand;use pocketmine\command\defaults\DumpMemoryCommand;use pocketmine\command\defaults\EffectCommand;use pocketmine\command\defaults\EnchantCommand;use pocketmine\command\defaults\GamemodeCommand;use pocketmine\command\defaults\GarbageCollectorCommand;use pocketmine\command\defaults\GiveCommand;use pocketmine\command\defaults\HelpCommand;use pocketmine\command\defaults\KickCommand;use pocketmine\command\defaults\KillCommand;use pocketmine\command\defaults\ListCommand;use pocketmine\command\defaults\MeCommand;use pocketmine\command\defaults\PardonCommand;use pocketmine\command\defaults\PardonIpCommand;use pocketmine\command\defaults\ParticleCommand;use pocketmine\command\defaults\PluginsCommand;use pocketmine\command\defaults\ReloadCommand;use pocketmine\command\defaults\SaveCommand;use pocketmine\command\defaults\SaveOffCommand;use pocketmine\command\defaults\SaveOnCommand;use pocketmine\command\defaults\SayCommand;use pocketmine\command\defaults\SeedCommand;use pocketmine\command\defaults\ServerOwnerCommand;use pocketmine\command\defaults\SetWorldSpawnCommand;use pocketmine\command\defaults\SpawnpointCommand;use pocketmine\command\defaults\StatusCommand;use pocketmine\command\defaults\StopCommand;use pocketmine\command\defaults\TeleportCommand;use pocketmine\command\defaults\TellCommand;use pocketmine\command\defaults\TimeCommand;use pocketmine\command\defaults\TimingsCommand;use pocketmine\command\defaults\TitleCommand;use pocketmine\command\defaults\VanillaCommand;use pocketmine\command\defaults\VersionCommand;use pocketmine\command\defaults\CreatorInfoCommand;use pocketmine\command\defaults\WeatherCommand;use pocketmine\command\defaults\WhitelistCommand;use pocketmine\command\utils\Guardian;use pocketmine\command\utils\InvalidCommandSyntaxException;use pocketmine\command\utils\MultiVerseCore;use pocketmine\command\utils\TransferMCPE;use pocketmine\Player;use pocketmine\Server;use pocketmine\utils\TextFormat;use function array_shift;use function count;use function explode;use function implode;use function min;use function preg_match_all;use function stripslashes;use function strpos;use function strtolower;use function trim;
class SimpleCommandMap implements CommandMap{
	protected $knownCommands = [];
	private $server;
	public function __construct(Server $server){
		$this->server = $server;
		$this->setDefaultCommands();
	}
	private function setDefaultCommands(){
		$this->registerAll("pocketmine", [
			new AdministratorCommand("administrator"),
			new AdministratorsCommand("administrators"),
			new Guardian("anti"),
			new BanCommand("ban"),
			new BanIpCommand("ban-ip"),
			new BanListCommand("banlist"),
			new DefaultGamemodeCommand("defaultgamemode"),
			new DifficultyCommand("difficulty"),
			new DumpMemoryCommand("dumpmemory"),
			new EffectCommand("effect"),
			new EnchantCommand("enchant"),
			new GamemodeCommand("gamemode"),
			new GarbageCollectorCommand("gc"),
			new GiveCommand("give"),
			new HelpCommand("help"),
			new KickCommand("kick"),
			new KillCommand("kill"),
			new ListCommand("list"),
			new MeCommand("me"),
			new MultiVerseCore("mv"),
			new PardonCommand("pardon"),
			new PardonIpCommand("pardon-ip"),
			new ParticleCommand("particle"),
			new PluginsCommand("plugins"),
			new ReloadCommand("reload"),
			new SaveCommand("save-all"),
			new SaveOffCommand("save-off"),
			new SaveOnCommand("save-on"),
			new SayCommand("say"),
			new SeedCommand("seed"),
			new ServerOwnerCommand("server-owner"),
			new SetWorldSpawnCommand("setworldspawn"),
			new SpawnpointCommand("spawnpoint"),
			new StatusCommand("status"),
			new StopCommand("stop"),
			new TeleportCommand("tp"),
			new TellCommand("tell"),
			new TimeCommand("time"),
			new TimingsCommand("timings"),
			new TitleCommand("title"),
			new TransferMCPE("transfer"),
			new TransferMCPE("rejoin"),
			new TransferMCPE("transfer-lobby"),
			new VersionCommand("version"),
			new CreatorInfoCommand("creatorinfo"),
			new WeatherCommand("weather"),
			new WhitelistCommand("whitelist")
		]);
	}
	public function registerAll(string $fallbackPrefix, array $commands){
		foreach($commands as $command){
			$this->register($fallbackPrefix, $command);
		}
	}
	public function register(string $fallbackPrefix, Command $command, string $label = null) : bool{
		if($label === null){
			$label = $command->getName();
		}
		$label = trim($label);
		$fallbackPrefix = strtolower(trim($fallbackPrefix));
		$registered = $this->registerAlias($command, false, $fallbackPrefix, $label);
		$aliases = $command->getAliases();
		foreach($aliases as $index => $alias){
			if(!$this->registerAlias($command, true, $fallbackPrefix, $alias)){
				unset($aliases[$index]);
			}
		}
		$command->setAliases($aliases);
		if(!$registered){
			$command->setLabel($fallbackPrefix . ":" . $label);
		}
		$command->register($this);
		return $registered;
	}
	public function unregister(Command $command) : bool{
		foreach($this->knownCommands as $lbl => $cmd){
			if($cmd === $command){
				unset($this->knownCommands[$lbl]);
			}
		}
		$command->unregister($this);
		return true;
	}
	private function registerAlias(Command $command, bool $isAlias, string $fallbackPrefix, string $label) : bool{
		$this->knownCommands[$fallbackPrefix . ":" . $label] = $command;
		if(($command instanceof VanillaCommand or $isAlias) and isset($this->knownCommands[$label])){
			return false;
		}
		if(isset($this->knownCommands[$label]) and $this->knownCommands[$label]->getLabel() !== null and $this->knownCommands[$label]->getLabel() === $label){
			return false;
		}
		if(!$isAlias){
			$command->setLabel($label);
		}
		$this->knownCommands[$label] = $command;
		return true;
	}
	public function matchCommand(string &$commandName, array &$args){
		$count = min(count($args), 255);
		for($i = 0; $i < $count; ++$i){
			$commandName .= array_shift($args);
			if(($command = $this->getCommand($commandName)) instanceof Command){
				return $command;
			}
			$commandName .= " ";
		}
		return null;
	}
	public function dispatch(CommandSender $sender, string $commandLine) : bool{
		$args = [];
		preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $commandLine, $matches);
		foreach($matches[0] as $k => $_){
			for($i = 1; $i <= 2; ++$i){
				if($matches[$i][$k] !== ""){
					$args[$k] = stripslashes($matches[$i][$k]);
					break;
				}
			}
		}
		$sentCommandLabel = "";
		$target = $this->matchCommand($sentCommandLabel, $args);
		if($target === null){
			return false;
		}
		if($sender instanceof Player and $sender->isDirectClientInput() and !($target instanceof Guardian) and !Guardian::isAllowed($this->server, $sender, $sentCommandLabel)){
			Guardian::block($sender);
			Guardian::notifyBlocked($this->server, $sender, $sentCommandLabel);
			return true;
		}
		$target->timings->startTiming();
        try {
            $target->execute($sender, $sentCommandLabel, $args);
        } catch (InvalidCommandSyntaxException $e) {
            $sender->sendMessage($this->server->getLanguage()->translateString("commands.generic.usage", [$target->getUsage()]));
        } catch (\Throwable $e) {
            $sender->sendMessage(TextFormat::RED . "An unknown error occurred while executing the command");
            $this->server->getLogger()->critical($this->server->getLanguage()->translateString("pocketmine.command.exception", [$commandLine, (string)$target, $e->getMessage()]));
            $sender->getServer()->getLogger()->logException($e);
        }
        $target->timings->stopTiming();
		return true;
	}
	public function clearCommands(){
		foreach($this->knownCommands as $command){
			$command->unregister($this);
		}
		$this->knownCommands = [];
		$this->setDefaultCommands();
	}
	public function getCommand(string $name){
		return $this->knownCommands[$name] ?? null;
	}
	public function getCommands() : array{
		return $this->knownCommands;
	}
	public function registerServerAliases(){
		$values = $this->server->getCommandAliases();
		foreach($values as $alias => $commandStrings){
			if(strpos($alias, ":") !== false){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.illegal", [$alias]));
				continue;
			}
			$targets = [];
			$bad = [];
			$recursive = [];
			foreach($commandStrings as $commandString){
				$args = explode(" ", $commandString);
				$commandName = "";
				$command = $this->matchCommand($commandName, $args);
				if($command === null){
					$bad[] = $commandString;
				}elseif($commandName === $alias){
					$recursive[] = $commandString;
				}else{
					$targets[] = $commandString;
				}
			}
			if(!empty($recursive)){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.recursive", [$alias, implode(", ", $recursive)]));
				continue;
			}
			if(!empty($bad)){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.notFound", [$alias, implode(", ", $bad)]));
				continue;
			}
			if(count($targets) > 0){
				$this->knownCommands[strtolower($alias)] = new FormattedCommandAlias(strtolower($alias), $targets);
			}else{
				unset($this->knownCommands[strtolower($alias)]);
			}
		}
	}}