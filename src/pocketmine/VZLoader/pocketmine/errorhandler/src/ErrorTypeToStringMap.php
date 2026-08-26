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
namespace pocketmine\errorhandler;
final class ErrorTypeToStringMap{
	private const ERROR_STRINGS = [
		0 => "EXCEPTION",
		E_ERROR => "E_ERROR",
		E_WARNING => "E_WARNING",
		E_PARSE => "E_PARSE",
		E_NOTICE => "E_NOTICE",
		E_CORE_ERROR => "E_CORE_ERROR",
		E_CORE_WARNING => "E_CORE_WARNING",
		E_COMPILE_ERROR => "E_COMPILE_ERROR",
		E_COMPILE_WARNING => "E_COMPILE_WARNING",
		E_USER_ERROR => "E_USER_ERROR",
		E_USER_WARNING => "E_USER_WARNING",
		E_USER_NOTICE => "E_USER_NOTICE",
		E_STRICT => "E_STRICT",
		E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
		E_DEPRECATED => "E_DEPRECATED",
		E_USER_DEPRECATED => "E_USER_DEPRECATED"
	];
	private function __construct(){
	}
	public static function get(int $errorType) : string{
		if(!isset(self::ERROR_STRINGS[$errorType])){
			throw new \InvalidArgumentException("Invalid error type $errorType");
		}
		return self::ERROR_STRINGS[$errorType];
	}}