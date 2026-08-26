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
use pocketmine\network\mcpe\protocol\PacketsIds\PacketMagicNumbers;
final class ProtocolInfo implements PacketMagicNumbers{
	public const CURRENT_PROTOCOL = ProtocolInfo::PROTOCOL_81;
	public const MAX_PROTOCOL = ProtocolInfo::PROTOCOL_113;
	public const ACCEPTED_PROTOCOLS = [
	    ProtocolInfo::PROTOCOL_70,
	    ProtocolInfo::PROTOCOL_60,
	    ProtocolInfo::PROTOCOL_46,
	    ProtocolInfo::PROTOCOL_45,
	    ProtocolInfo::PROTOCOL_44,
	    ProtocolInfo::PROTOCOL_43,
	    ProtocolInfo::PROTOCOL_42,
	    ProtocolInfo::PROTOCOL_41,
	    ProtocolInfo::PROTOCOL_81,
	    ProtocolInfo::PROTOCOL_82,
	    ProtocolInfo::PROTOCOL_83,
	    ProtocolInfo::PROTOCOL_84,
		ProtocolInfo::PROTOCOL_100,
		ProtocolInfo::PROTOCOL_101,
		ProtocolInfo::PROTOCOL_102,
		ProtocolInfo::PROTOCOL_105,
		ProtocolInfo::PROTOCOL_106,
		ProtocolInfo::PROTOCOL_107,
		ProtocolInfo::PROTOCOL_110,
		ProtocolInfo::PROTOCOL_111,
		ProtocolInfo::PROTOCOL_112,
		ProtocolInfo::PROTOCOL_113,
	];
    public const PROTOCOL_41 = 41; 
    public const PROTOCOL_42 = 42; 
    public const PROTOCOL_43 = 43; 
    public const PROTOCOL_44 = 44; 
    public const PROTOCOL_45 = 45; 
    public const PROTOCOL_46 = 46; 
    public const PROTOCOL_60 = 60; 
    public const PROTOCOL_70 = 70; 
    public const PROTOCOL_81 = 81; 
    public const PROTOCOL_82 = 82; 
    public const PROTOCOL_83 = 83; 
    public const PROTOCOL_84 = 84; 
    public const PROTOCOL_90 = 90; 
    public const PROTOCOL_91 = 91; 
    public const PROTOCOL_92 = 92; 
    public const PROTOCOL_100 = 100; 
	public const PROTOCOL_101 = 101; 
	public const PROTOCOL_102 = 102; 
	public const PROTOCOL_105 = 105; 
	public const PROTOCOL_106 = 106; 
	public const PROTOCOL_107 = 107; 
	public const PROTOCOL_110 = 110; 
	public const PROTOCOL_111 = 111; 
	public const PROTOCOL_112 = 112; 
	public const PROTOCOL_113 = 113;
	public const MINECRAFT_VERSION = '0.14.0 - 0.15.10, 1.1.0 - 1.1.7';
	public const MINECRAFT_VERSION_NETWORK = '1.1.5';
	public const VERSION_LABELS = [
		110 => "1.1.x",
		81 => "0.15.x",
		41 => "0.14.x",
	];
	public static function getVersionLabel(int $protocol) : string{
		foreach(self::VERSION_LABELS as $minProtocol => $label){
			if($protocol >= $minProtocol){
				return $label;
			}
		}
		return "?";
	}
}