<?php

declare(strict_types=1);
namespace pocketmine\command\defaults;
use pocketmine\command\CommandSender;use pocketmine\utils\TextFormat;use pocketmine\VeoZaxBrand;use function count;
class CreatorInfoCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Shows " . VeoZaxBrand::SOFTWARE_NAME . " and its creator's info and links",
			"/creatorinfo"
		);
		$this->setPermission("pocketmine.command.creatorinfo");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		$sender->sendMessage(TextFormat::DARK_GRAY . "-----[" . TextFormat::GRAY . "API Dev Information" . TextFormat::DARK_GRAY . "]-----");
		$sender->sendMessage(TextFormat::GRAY . "Software: " . TextFormat::BLUE . "Veo" . TextFormat::AQUA . "Zax" . TextFormat::RED . "API");
		$sender->sendMessage(TextFormat::GRAY . "Creator: " . TextFormat::YELLOW . VeoZaxBrand::CREATOR_NAME);
		$sender->sendMessage(TextFormat::WHITE . "Created: " . TextFormat::GREEN . "5th" . TextFormat::WHITE . " August 2026," . TextFormat::AQUA . " 5:00 AM IST");
		$sender->sendMessage(TextFormat::GRAY . "Discord:" . TextFormat::BLUE . " " . VeoZaxBrand::LINK_DISCORD);
		$sender->sendMessage(TextFormat::GRAY . "YouTube:" . TextFormat::RED . " " . VeoZaxBrand::LINK_YOUTUBE);
		$sender->sendMessage(TextFormat::GRAY . "GitHub:" . TextFormat::WHITE . " " . VeoZaxBrand::LINK_GITHUB);
		$sender->sendMessage(TextFormat::GRAY . "Facebook:" . TextFormat::AQUA . " " . VeoZaxBrand::LINK_FACEBOOK);
		$sender->sendMessage(TextFormat::GRAY . "Instagram:" . TextFormat::LIGHT_PURPLE . " " . VeoZaxBrand::LINK_INSTAGRAM);
		$sender->sendMessage(TextFormat::GRAY . "Website:" . TextFormat::GREEN . " " . VeoZaxBrand::LINK_WEBSITE);
		return true;
	}}