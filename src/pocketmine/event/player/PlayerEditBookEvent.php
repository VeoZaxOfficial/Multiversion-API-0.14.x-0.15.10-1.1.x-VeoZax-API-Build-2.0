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
namespace pocketmine\event\player;
use pocketmine\event\Cancellable;use pocketmine\item\WritableBook;use pocketmine\Player;
class PlayerEditBookEvent extends PlayerEvent implements Cancellable{
	public const ACTION_REPLACE_PAGE = 0;
	public const ACTION_ADD_PAGE = 1;
	public const ACTION_DELETE_PAGE = 2;
	public const ACTION_SWAP_PAGES = 3;
	public const ACTION_SIGN_BOOK = 4;
	private $oldBook;
	private $action;
	private $newBook;
	private $modifiedPages;
	public function __construct(Player $player, WritableBook $oldBook, WritableBook $newBook, int $action, array $modifiedPages){
		$this->player = $player;
		$this->oldBook = $oldBook;
		$this->newBook = $newBook;
		$this->action = $action;
		$this->modifiedPages = $modifiedPages;
	}
	public function getAction() : int{
		return $this->action;
	}
	public function getOldBook() : WritableBook{
		return $this->oldBook;
	}
	public function getNewBook() : WritableBook{
		return $this->newBook;
	}
	public function setNewBook(WritableBook $book) : void{
		$this->newBook = $book;
	}
	public function getModifiedPages() : array{
		return $this->modifiedPages;
	}}