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
namespace pocketmine\inventory;
use pocketmine\item\Item;use pocketmine\level\Level;use pocketmine\math\Vector3;use pocketmine\Player;
interface Inventory{
	public const MAX_STACK = 64;
	public function getSize() : int;
	public function getMaxStackSize() : int;
	public function setMaxStackSize(int $size) : void;
	public function getName() : string;
	public function getTitle() : string;
	public function getItem(int $index) : Item;
	public function setItem(int $index, Item $item, bool $send = true) : bool;
	public function addItem(Item ...$slots) : array;
	public function canAddItem(Item $item) : bool;
	public function removeItem(Item ...$slots) : array;
	public function getContents(bool $includeEmpty = false) : array;
	public function setContents(array $items, bool $send = true) : void;
	public function dropContents(Level $level, Vector3 $position) : void;
	public function sendContents($target) : void;
	public function sendSlot(int $index, $target) : void;
	public function contains(Item $item) : bool;
	public function all(Item $item) : array;
	public function first(Item $item, bool $exact = false) : int;
	public function firstEmpty() : int;
	public function isSlotEmpty(int $index) : bool;
	public function remove(Item $item) : void;
	public function clear(int $index, bool $send = true) : bool;
	public function clearAll(bool $send = true) : void;
	public function getViewers() : array;
	public function onOpen(Player $who) : void;
	public function open(Player $who) : bool;
	public function close(Player $who) : void;
	public function onClose(Player $who) : void;
	public function onSlotChange(int $index, Item $before, bool $send) : void;
	public function slotExists(int $slot) : bool;
	public function getEventProcessor() : ?InventoryEventProcessor;
	public function setEventProcessor(?InventoryEventProcessor $eventProcessor) : void;}