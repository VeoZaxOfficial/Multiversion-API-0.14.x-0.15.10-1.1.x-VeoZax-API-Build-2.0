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
namespace pocketmine\network\mcpe\multiversion;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
abstract class ProtocolConvertor{
	public final static function convertProtocol(int $playerProtocol) : int{
		switch($playerProtocol){
			case ProtocolInfo::PROTOCOL_113:
			case ProtocolInfo::PROTOCOL_112:
			case ProtocolInfo::PROTOCOL_111:
			case ProtocolInfo::PROTOCOL_110:
			    return ProtocolInfo::PROTOCOL_110;
			case ProtocolInfo::PROTOCOL_84:
			case ProtocolInfo::PROTOCOL_83:
			case ProtocolInfo::PROTOCOL_82:
			case ProtocolInfo::PROTOCOL_81:
			case ProtocolInfo::PROTOCOL_70:
			case ProtocolInfo::PROTOCOL_60:
			case ProtocolInfo::PROTOCOL_46:
			case ProtocolInfo::PROTOCOL_45:
			case ProtocolInfo::PROTOCOL_44:
			case ProtocolInfo::PROTOCOL_43:
			case ProtocolInfo::PROTOCOL_42:
			case ProtocolInfo::PROTOCOL_41:
				return ProtocolInfo::PROTOCOL_81;
			default:
				return ProtocolInfo::PROTOCOL_81;
		}
	}
    public final static function convertChunkProtocol(int $normalisedProtocol) : int{
        return $normalisedProtocol;
    }
    public final static function convertCraftingProtocol(int $normalisedProtocol) : int{
        return $normalisedProtocol;
    }}