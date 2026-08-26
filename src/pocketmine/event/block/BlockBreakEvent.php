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
namespace pocketmine\event\block;
use InvalidArgumentException;use pocketmine\block\Block;use pocketmine\event\Cancellable;use pocketmine\item\Item;use pocketmine\Player;
class BlockBreakEvent extends BlockEvent implements Cancellable{
	protected $player;
	protected $item;
	protected $instaBreak = false;
	protected $blockDrops = [];
	protected $xpDrops;
	public function __construct(Player $player, Block $block, Item $item, array $drops, bool $instaBreak = false, int $xpDrops = 0){
		parent::__construct($block);
		$this->item = $item;
		$this->player = $player;
		$this->instaBreak = $instaBreak;
		$this->setDrops($drops);
		$this->xpDrops = $xpDrops;
	}
	public function getPlayer() : Player{
		return $this->player;
	}
	public function getItem() : Item{
		return $this->item;
	}
	public function getInstaBreak() : bool{
		return $this->instaBreak;
	}
	public function setInstaBreak(bool $instaBreak) : void{
		$this->instaBreak = $instaBreak;
	}
	public function getDrops() : array{
		return $this->blockDrops;
	}
	public function setDrops(array $drops) : void{
		$this->setDropsVariadic(...$drops);
	}
	public function setDropsVariadic(Item ...$drops) : void{
		$this->blockDrops = $drops;
	}
	public function getXpDropAmount() : int{
		return $this->xpDrops;
	}
	public function setXpDropAmount(int $amount) : void{
		if($amount < 0){
			throw new InvalidArgumentException("Amount must be at least zero");
		}
		$this->xpDrops = $amount;
	}}