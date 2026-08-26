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
use function error_reporting;use function restore_error_handler;use function set_error_handler;use const E_NOTICE;use const E_WARNING;
final class ErrorToExceptionHandler{
	private function __construct(){
	}
	private static $lastSilencedError = null;
	public static function handle(int $severity, string $message, string $file, int $line) : bool{
		if((error_reporting() & $severity) !== 0){
			throw new \ErrorException($message, 0, $severity, $file, $line);
		}
		self::$lastSilencedError = new ErrorRecord($severity, $message, $file, $line);
		return true; 
	}
	public static function getLastSilencedError() : ErrorRecord{
		if(self::$lastSilencedError === null){
			throw new \LogicException("No error has been generated");
		}
		return self::$lastSilencedError;
	}
	public static function clearLastSilencedError() : void{
		self::$lastSilencedError = null;
	}
	public static function getAndClearLastSilencedError() : ErrorRecord{
		$result = self::getLastSilencedError();
		self::clearLastSilencedError();
		return $result;
	}
	public static function set(int $levels = E_WARNING | E_NOTICE) : void{
		set_error_handler([self::class, 'handle'], $levels);
	}
	private static function throwAll() : \Closure{
		return function(int $severity, string $message, string $file, int $line): bool{
			throw new \ErrorException($message, 0, $severity, $file, $line);
		};
	}
	public static function trap(\Closure $closure, int $levels = E_WARNING | E_NOTICE){
		set_error_handler(self::throwAll(), $levels);
		try{
			return $closure();
		}finally{
			restore_error_handler();
		}
	}
	public static function trapAndRemoveFalse(\Closure $closure, int $levels = E_WARNING | E_NOTICE){
		set_error_handler(self::throwAll(), $levels);
		try{
			$result = $closure();
			if($result === false){
				throw new \LogicException("Block must not return false when no error occurred. Use trap() if the block may return false.");
			}
			return $result;
		}finally{
			restore_error_handler();
		}
	}}