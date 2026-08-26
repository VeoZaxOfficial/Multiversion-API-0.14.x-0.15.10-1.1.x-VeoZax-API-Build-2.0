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
namespace raklib\protocol;
use InvalidArgumentException;use pocketmine\utils\BinaryStream;use raklib\utils\InternetAddress;use UnexpectedValueException;use function assert;use function chr;use function count;use function explode;use function inet_ntop;use function inet_pton;use function ord;use function pack;use function strlen;use function unpack;use const AF_INET6;
abstract class Packet extends BinaryStream{
	public static $ID = -1;
	public $sendTime;
	protected function getString() : string{
		return $this->get(((unpack("n", $this->get(2))[1])));
	}
	protected function getAddress() : InternetAddress{
		$version = (ord($this->get(1)));
		if($version === 4){
			$addr = ((~(ord($this->get(1)))) & 0xff) . "." . ((~(ord($this->get(1)))) & 0xff) . "." . ((~(ord($this->get(1)))) & 0xff) . "." . ((~(ord($this->get(1)))) & 0xff);
			$port = ((unpack("n", $this->get(2))[1]));
			return new InternetAddress($addr, $port, $version);
		}elseif($version === 6){
			(unpack("v", $this->get(2))[1]); 
			$port = ((unpack("n", $this->get(2))[1]));
			((unpack("N", $this->get(4))[1] << 32 >> 32)); 
			$addr = inet_ntop($this->get(16));
			((unpack("N", $this->get(4))[1] << 32 >> 32)); 
			return new InternetAddress($addr, $port, $version);
		}else{
			throw new UnexpectedValueException("Unknown IP address version $version");
		}
	}
	protected function putString(string $v) : void{
		($this->buffer .= (pack("n", strlen($v))));
		($this->buffer .= $v);
	}
	protected function putAddress(InternetAddress $address) : void{
		($this->buffer .= chr($address->version));
		if($address->version === 4){
			$parts = explode(".", $address->ip);
			assert(count($parts) === 4, "Wrong number of parts in IPv4 IP, expected 4, got " . count($parts));
			foreach($parts as $b){
				($this->buffer .= chr((~((int) $b)) & 0xff));
			}
			($this->buffer .= (pack("n", $address->port)));
		}elseif($address->version === 6){
			($this->buffer .= (pack("v", AF_INET6)));
			($this->buffer .= (pack("n", $address->port)));
			($this->buffer .= (pack("N", 0)));
			($this->buffer .= inet_pton($address->ip));
			($this->buffer .= (pack("N", 0)));
		}else{
			throw new InvalidArgumentException("IP version $address->version is not supported");
		}
	}
	public function encode() : void{
		$this->reset();
		$this->encodeHeader();
		$this->encodePayload();
	}
	protected function encodeHeader() : void{
		($this->buffer .= chr(static::$ID));
	}
	abstract protected function encodePayload() : void;
	public function decode() : void{
		$this->offset = 0;
		$this->decodeHeader();
		$this->decodePayload();
	}
	protected function decodeHeader() : void{
		(ord($this->get(1))); 
	}
	abstract protected function decodePayload() : void;
	public function clean(){
		$this->buffer = null;
		$this->offset = 0;
		$this->sendTime = null;
		return $this;
	}}