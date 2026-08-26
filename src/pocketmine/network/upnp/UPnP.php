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
namespace pocketmine\network\upnp;
use COM;use pocketmine\utils\Internet;use pocketmine\utils\Utils;use RuntimeException;use Throwable;use function class_exists;use function is_object;
abstract class UPnP{
	public static function PortForward(int $port) : void{
		if(!Internet::$online){
			throw new RuntimeException("Server is offline");
		}
		if(Utils::getOS() !== "win"){
			throw new RuntimeException("UPnP is only supported on Windows");
		}
		if(!class_exists("COM")){
			throw new RuntimeException("UPnP requires the com_dotnet extension");
		}
		$myLocalIP = Internet::getInternalIP();
		$com = new COM("HNetCfg.NATUPnP");
		if(!is_object($com->StaticPortMappingCollection)){
			throw new RuntimeException("Failed to portforward using UPnP. Ensure that network discovery is enabled in Control Panel.");
		}
		$com->StaticPortMappingCollection->Add($port, "UDP", $port, $myLocalIP, true, "PocketMine-MP");
	}
	public static function RemovePortForward(int $port) : bool{
		if(!Internet::$online){
			return false;
		}
		if(Utils::getOS() != "win" or !class_exists("COM")){
			return false;
		}
		try{
			$com = new COM("HNetCfg.NATUPnP");
			if(!is_object($com->StaticPortMappingCollection)){
				return false;
			}
			$com->StaticPortMappingCollection->Remove($port, "UDP");
		}catch(Throwable $e){
			return false;
		}
		return true;
	}}