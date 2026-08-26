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


class SplFixedByteArray extends SplFixedArray{
	private $convert;
	public function __construct($size, $convert = false){
		parent::__construct($size);
		$this->convert = (bool) $convert;
	}
	public function chunk($start, $size, $normalize = true){
		$end = $start + $size;
		if($normalize and $this->convert){
			$d = "";
			for($i = $start; $i < $end; ++$i){
				$d .= chr($this[$i]);
			}
		}else{
			$d = [];
			for($i = $start; $i < $end; ++$i){
				$d[] = $this[$i];
			}
		}
		return $d;
	}
	public static function fromString($str, $convert = false){
		$len = strlen($str);
		$ob = new SplFixedByteArray($len, $convert);
		if($convert){
			for($i = 0; $i < $len; ++$i){
				$ob[$i] = ord($str{$i});
			}
		}else{
			for($i = 0; $i < $len; ++$i){
				$ob[$i] = $str{$i};
			}
		}
		return $ob;
	}
	public static function fromStringChunk($str, $size, $start = 0, $convert = false){
		$ob = new SplFixedByteArray($size, $convert);
		if($convert){
			for($i = 0; $i < $size; ++$i){
				$ob[$i] = ord($str{$i + $start});
			}
		}else{
			for($i = 0; $i < $size; ++$i){
				$ob[$i] = $str{$i + $start};
			}
		}
		return $ob;
	}
	public function toString(){
		$result = "";
		if($this->convert){
			for($i = 0; $i < $this->getSize(); ++$i){
				$result .= chr($this[$i]);
			}
		}else{
			for($i = 0; $i < $this->getSize(); ++$i){
				$result .= $this[$i];
			}
		}
		return $result;
	}
	public function __toString(){
		return $this->toString();
	}}