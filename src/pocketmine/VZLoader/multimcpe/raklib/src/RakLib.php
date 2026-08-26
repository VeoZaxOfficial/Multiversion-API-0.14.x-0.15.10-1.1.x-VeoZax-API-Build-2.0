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
namespace raklib;
use function defined;use function extension_loaded;use function phpversion;use function substr_count;use function version_compare;use const PHP_EOL;use const PHP_VERSION;
$errors = 0;if(version_compare(RakLib::MIN_PHP_VERSION, PHP_VERSION) > 0){
	echo "[CRITICAL] Use PHP >= " . RakLib::MIN_PHP_VERSION . PHP_EOL;
	++$errors;}
$exts = [
	"bcmath" => "BC Math",
	"sockets" => "Sockets"];
foreach($exts as $ext => $name){
	if(!extension_loaded($ext)){
		echo "[CRITICAL] Unable to find the $name ($ext) extension." . PHP_EOL;
		++$errors;
	}}
if(!defined('AF_INET6')){
	echo "[CRITICAL] This build of PHP does not support IPv6. IPv6 support is required.";
	++$errors;}
if($errors > 0){
	exit(1); }unset($errors, $exts);
abstract class RakLib{
	public const VERSION = "0.12.0";
	public const MIN_PHP_VERSION = "8.2.0";
	public const DEFAULT_PROTOCOL_VERSION = 6;
	public const MAGIC = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";
	public const PRIORITY_NORMAL = 0;
	public const PRIORITY_IMMEDIATE = 1;
	public const FLAG_NEED_ACK = 0b00001000;
	public const PACKET_ENCAPSULATED = 0x01;
	public const PACKET_OPEN_SESSION = 0x02;
	public const PACKET_CLOSE_SESSION = 0x03;
	public const PACKET_INVALID_SESSION = 0x04;
	public const PACKET_SEND_QUEUE = 0x05;
	public const PACKET_ACK_NOTIFICATION = 0x06;
	public const PACKET_SET_OPTION = 0x07;
	public const PACKET_RAW = 0x08;
	public const PACKET_BLOCK_ADDRESS = 0x09;
	public const PACKET_UNBLOCK_ADDRESS = 0x10;
	public const PACKET_REPORT_PING = 0x11;
	public const PACKET_SHUTDOWN = 0x7e;
	public const PACKET_EMERGENCY_SHUTDOWN = 0x7f;
	public static $SYSTEM_ADDRESS_COUNT = 20;}