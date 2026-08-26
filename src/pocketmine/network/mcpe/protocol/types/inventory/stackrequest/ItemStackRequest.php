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
namespace pocketmine\network\mcpe\protocol\types\inventory\stackrequest;
use pocketmine\network\mcpe\multiversion\MultiversionEnums;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\NetworkBinaryStream;use function count;
final class ItemStackRequest{
	private $requestId;
	private $actions;
	private $filterStrings;
	private $filterStringCause;
	public function __construct(int $requestId, array $actions, array $filterStrings, int $filterStringCause){
		$this->requestId = $requestId;
		$this->actions = $actions;
		$this->filterStrings = $filterStrings;
		$this->filterStringCause = $filterStringCause;
	}
	public function getRequestId() : int{ return $this->requestId; }
	public function getActions() : array{ return $this->actions; }
	public function getFilterStrings() : array{ return $this->filterStrings; }
    public function getFilterStringCause() : int{ return $this->filterStringCause; }
	private static function readAction(NetworkBinaryStream $in, int $typeId) : ItemStackRequestAction{
		return match($typeId){
			TakeStackRequestAction::ID => TakeStackRequestAction::read($in),
			PlaceStackRequestAction::ID => PlaceStackRequestAction::read($in),
			SwapStackRequestAction::ID => SwapStackRequestAction::read($in),
			DropStackRequestAction::ID => DropStackRequestAction::read($in),
			DestroyStackRequestAction::ID => DestroyStackRequestAction::read($in),
			CraftingConsumeInputStackRequestAction::ID => CraftingConsumeInputStackRequestAction::read($in),
			CraftingCreateSpecificResultStackRequestAction::ID => CraftingCreateSpecificResultStackRequestAction::read($in),
			PlaceIntoBundleStackRequestAction::ID => PlaceIntoBundleStackRequestAction::read($in),
			TakeFromBundleStackRequestAction::ID => TakeFromBundleStackRequestAction::read($in),
			LabTableCombineStackRequestAction::ID => LabTableCombineStackRequestAction::read($in),
			BeaconPaymentStackRequestAction::ID => BeaconPaymentStackRequestAction::read($in),
			MineBlockStackRequestAction::ID => MineBlockStackRequestAction::read($in),
			CraftRecipeStackRequestAction::ID => CraftRecipeStackRequestAction::read($in),
			CraftRecipeAutoStackRequestAction::ID => CraftRecipeAutoStackRequestAction::read($in),
			CreativeCreateStackRequestAction::ID => CreativeCreateStackRequestAction::read($in),
			CraftRecipeOptionalStackRequestAction::ID => CraftRecipeOptionalStackRequestAction::read($in),
			GrindstoneStackRequestAction::ID => GrindstoneStackRequestAction::read($in),
			LoomStackRequestAction::ID => LoomStackRequestAction::read($in),
			DeprecatedCraftingNonImplementedStackRequestAction::ID => DeprecatedCraftingNonImplementedStackRequestAction::read($in),
			DeprecatedCraftingResultsStackRequestAction::ID => DeprecatedCraftingResultsStackRequestAction::read($in),
			default => throw new \UnexpectedValueException("Unhandled item stack request action type $typeId for protocol " . $in->getProtocol()),
		};
	}
	public static function read(NetworkBinaryStream $in) : self{
		$requestId = $in->readItemStackRequestId();
		$actions = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$typeId = $in->getByte();
			$actions[] = self::readAction($in, MultiversionEnums::getItemStackRequestActionType($in->getProtocol(), $typeId));
		}
		
		return new self($requestId, $actions, $filterStrings ?? [], $filterStringCause ?? 0);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->writeItemStackRequestId($this->requestId);
		$out->putUnsignedVarInt(count($this->actions));
		foreach($this->actions as $action){
			$out->putByte(MultiversionEnums::getItemStackRequestActionTypeId($out->getProtocol(), $action->getTypeId()));
			$action->write($out);
		}
		
	}}