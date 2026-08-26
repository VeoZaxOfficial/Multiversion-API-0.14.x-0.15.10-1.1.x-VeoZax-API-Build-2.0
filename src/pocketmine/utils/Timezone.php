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
namespace pocketmine\utils;
use function abs;use function date_default_timezone_set;use function date_parse;use function exec;use function file_exists;use function file_get_contents;use function implode;use function ini_get;use function ini_set;use function is_link;use function json_decode;use function parse_ini_file;use function preg_match;use function readlink;use function str_replace;use function strpos;use function substr;use function timezone_abbreviations_list;use function timezone_name_from_abbr;use function trim;
abstract class Timezone{
	public static function get() : string{
		return ini_get('date.timezone');
	}
	public static function init() : array{
		$messages = [];
		do{
			$timezone = ini_get("date.timezone");
			if($timezone !== ""){
				if(strpos($timezone, "/") === false){
					$default_timezone = timezone_name_from_abbr($timezone);
					if($default_timezone !== false){
						ini_set("date.timezone", $default_timezone);
						date_default_timezone_set($default_timezone);
						break;
					}else{
						$messages[] = "Timezone \"$timezone\" could not be parsed as a valid timezone from php.ini, falling back to auto-detection";
					}
				}else{
					date_default_timezone_set($timezone);
					break;
				}
			}
			if(($timezone = self::detectSystemTimezone()) and date_default_timezone_set($timezone)){
				ini_set("date.timezone", $timezone);
				break;
			}
			if($response = Internet::getURL("http://ip-api.com/json") 
				and $ip_geolocation_data = json_decode($response, true)
				and $ip_geolocation_data['status'] !== 'fail'
				and date_default_timezone_set($ip_geolocation_data['timezone'])
			){
				ini_set("date.timezone", $ip_geolocation_data['timezone']);
				break;
			}
			ini_set("date.timezone", "UTC");
			date_default_timezone_set("UTC");
			$messages[] = "Timezone could not be automatically determined or was set to an invalid value. An incorrect timezone will result in incorrect timestamps on console logs. It has been set to \"UTC\" by default. You can change it on the php.ini file.";
		}while(false);
		return $messages;
	}
	public static function detectSystemTimezone(){
		switch(Utils::getOS()){
			case 'win':
				$regex = '/(UTC)(\+*\-*\d*\d*\:*\d*\d*)/';
				exec("wmic timezone get Caption", $output);
				$string = trim(implode("\n", $output));
				preg_match($regex, $string, $matches);
				if(!isset($matches[2])){
					return false;
				}
				$offset = $matches[2];
				if($offset == ""){
					return "UTC";
				}
				return self::parseOffset($offset);
			case 'linux':
				if(file_exists('/etc/timezone')){
					$data = file_get_contents('/etc/timezone');
					if($data){
						return trim($data);
					}
				}
				if(file_exists('/etc/sysconfig/clock')){
					$data = parse_ini_file('/etc/sysconfig/clock');
					if(!empty($data['ZONE'])){
						return trim($data['ZONE']);
					}
				}
				$offset = trim(exec('date +%:z'));
				if($offset == "+00:00"){
					return "UTC";
				}
				return self::parseOffset($offset);
			case 'mac':
				if(is_link('/etc/localtime')){
					$filename = readlink('/etc/localtime');
					if(strpos($filename, '/usr/share/zoneinfo/') === 0){
						$timezone = substr($filename, 20);
						return trim($timezone);
					}
				}
				return false;
			default:
				return false;
		}
	}
	private static function parseOffset($offset){
		if(strpos($offset, '-') !== false){
			$negative_offset = true;
			$offset = str_replace('-', '', $offset);
		}else{
			if(strpos($offset, '+') !== false){
				$negative_offset = false;
				$offset = str_replace('+', '', $offset);
			}else{
				return false;
			}
		}
		$parsed = date_parse($offset);
		$offset = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
		if($negative_offset == true){
			$offset = -abs($offset);
		}
		foreach(timezone_abbreviations_list() as $zones){
			foreach($zones as $timezone){
				if($timezone['offset'] == $offset){
					return $timezone['timezone_id'];
				}
			}
		}
		return false;
	}}