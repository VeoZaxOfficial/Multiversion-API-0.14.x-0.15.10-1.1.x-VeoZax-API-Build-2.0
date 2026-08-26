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
use function chr;use function count;use function ord;use function pack;use function sort;use function substr;use function unpack;use const SORT_NUMERIC;
abstract class AcknowledgePacket extends Packet{
	private const RECORD_TYPE_RANGE = 0;
	private const RECORD_TYPE_SINGLE = 1;
	public $packets = [];
	protected function encodePayload() : void{
		$payload = "";
		sort($this->packets, SORT_NUMERIC);
		$count = count($this->packets);
		$records = 0;
		if($count > 0){
			$pointer = 1;
			$start = $this->packets[0];
			$last = $this->packets[0];
			while($pointer < $count){
				$current = $this->packets[$pointer++];
				$diff = $current - $last;
				if($diff === 1){
					$last = $current;
				}elseif($diff > 1){ 
					if($start === $last){
						$payload .= chr(self::RECORD_TYPE_SINGLE);
						$payload .= (substr(pack("V", $start), 0, -1));
						$start = $last = $current;
					}else{
						$payload .= chr(self::RECORD_TYPE_RANGE);
						$payload .= (substr(pack("V", $start), 0, -1));
						$payload .= (substr(pack("V", $last), 0, -1));
						$start = $last = $current;
					}
					++$records;
				}
			}
			if($start === $last){
				$payload .= chr(self::RECORD_TYPE_SINGLE);
				$payload .= (substr(pack("V", $start), 0, -1));
			}else{
				$payload .= chr(self::RECORD_TYPE_RANGE);
				$payload .= (substr(pack("V", $start), 0, -1));
				$payload .= (substr(pack("V", $last), 0, -1));
			}
			++$records;
		}
		($this->buffer .= (pack("n", $records)));
		$this->buffer .= $payload;
	}
	protected function decodePayload() : void{
		$count = ((unpack("n", $this->get(2))[1]));
		$this->packets = [];
		$cnt = 0;
		for($i = 0; $i < $count and !$this->feof() and $cnt < 4096; ++$i){
			if((ord($this->get(1))) === self::RECORD_TYPE_RANGE){
				$start = ((unpack("V", $this->get(3) . "\x00")[1]));
				$end = ((unpack("V", $this->get(3) . "\x00")[1]));
				if(($end - $start) > 512){
					$end = $start + 512;
				}
				for($c = $start; $c <= $end; ++$c){
					$this->packets[$cnt++] = $c;
				}
			}else{
				$this->packets[$cnt++] = ((unpack("V", $this->get(3) . "\x00")[1]));
			}
		}
	}
	public function clean(){
		$this->packets = [];
		return parent::clean();
	}}