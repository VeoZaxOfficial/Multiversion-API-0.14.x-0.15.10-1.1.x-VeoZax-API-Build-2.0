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
namespace pocketmine\entity;
use pocketmine\Player;use pocketmine\entity\Human;use pocketmine\level\Level;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\FloatTag;use pocketmine\nbt\tag\DoubleTag;use pocketmine\nbt\tag\StringTag;use pocketmine\nbt\tag\ByteArrayTag;use pocketmine\entity\Skin;
class NPC extends Human {
    protected $skin;
    public function __construct(Level $level, float $x, float $y, float $z, float $yaw, float $pitch, Skin $skin) {
        $this->skin = $skin;
        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $x),
                new DoubleTag("", $y),
                new DoubleTag("", $z)
            ]),
            new ListTag("Motion", [
                new DoubleTag("", 0.0),
                new DoubleTag("", 0.0),
                new DoubleTag("", 0.0)
            ]),
            new ListTag("Rotation", [
                new FloatTag("", $yaw),
                new FloatTag("", $pitch)
            ]),
            new CompoundTag("Skin", [
                new ByteArrayTag("Data", $skin->getSkinData()),
                new StringTag("Name", $skin->getSkinId())
            ])
        ]);
        parent::__construct($level, $nbt);
        $this->setSkin($skin);
        $this->sendSkin();
        $this->spawnToAll();
    }
    public function spawnToAll(): void {
        foreach ($this->getLevelNonNull()->getPlayers() as $player) {
            $this->spawnTo($player);
        }
    }
    public function spawnTo(Player $player): void {
        parent::spawnTo($player);
    }
    public function despawnFromAll(): void {
        foreach ($this->getLevelNonNull()->getPlayers() as $player) {
            $this->despawnFrom($player, true);
        }
    }
    public function despawnFrom(Player $player, bool $send = true): void {
        parent::despawnFrom($player, $send);
    }}
?>
