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
namespace pocketmine\network\mcpe\protocol;
use pocketmine\network\mcpe\NetworkSession;use function count;
class UnlockedRecipesPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UNLOCKED_RECIPES_PACKET;
	private bool $newRecipes;
	private int $type;
	private array $recipes;
	public const TYPE_EMPTY = 0;
	public const TYPE_INITIALLY_UNLOCKED = 1;
	public const TYPE_NEWLY_UNLOCKED = 2;
	public const TYPE_REMOVE = 3;
	public const TYPE_REMOVE_ALL = 4;
	public static function create(bool $newRecipes, int $type, array $recipes) : self{
		$result = new self;
		$result->newRecipes = $newRecipes;
		$result->type = $type;
		$result->recipes = $recipes;
		return $result;
	}
	public function isNewRecipes() : bool{ return $this->newRecipes; }
	public function getType() : int{ return $this->type; }
	public function getRecipes() : array{ return $this->recipes; }
	protected function decodePayload() : void{
		
			$this->newRecipes = $this->getBool();
		
		$this->recipes = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; $i++){
			$this->recipes[] = $this->getString();
		}
	}
	protected function encodePayload() : void{
		
		    $this->putBool($this->newRecipes);
		
		$this->putUnsignedVarInt(count($this->recipes));
		foreach($this->recipes as $recipe){
			$this->putString($recipe);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleUnlockedRecipes($this);
	}}