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
use InvalidArgumentException;use function count;use function preg_match;
class VersionString{
	private $baseVersion;
	private $suffix;
	private $major;
	private $minor;
	private $patch;
	private $build;
	private $development = false;
	public function __construct(string $baseVersion, bool $isDevBuild = false, int $buildNumber = 0){
		$this->baseVersion = $baseVersion;
		$this->development = $isDevBuild;
		$this->build = $buildNumber;
		preg_match('/(\d+)\.(\d+)\.(\d+)(?:-(.*))?$/', $this->baseVersion, $matches);
		if(count($matches) < 4){
			throw new InvalidArgumentException("Invalid base version \"$baseVersion\", should contain at least 3 version digits");
		}
		$this->major = (int) $matches[1];
		$this->minor = (int) $matches[2];
		$this->patch = (int) $matches[3];
		$this->suffix = $matches[4] ?? "";
	}
	public function getNumber() : int{
		return (($this->major << 9) | ($this->minor << 5) | $this->patch);
	}
	public function getBaseVersion() : string{
		return $this->baseVersion;
	}
	public function getFullVersion(bool $build = false) : string{
		$retval = $this->baseVersion;
		if($this->development){
			$retval .= "+dev";
			if($build and $this->build > 0){
				$retval .= "." . $this->build;
			}
		}
		return $retval;
	}
	public function getMajor() : int{
		return $this->major;
	}
	public function getMinor() : int{
		return $this->minor;
	}
	public function getPatch() : int{
		return $this->patch;
	}
	public function getSuffix() : string{
		return $this->suffix;
	}
	public function getBuild() : int{
		return $this->build;
	}
	public function isDev() : bool{
		return $this->development;
	}
	public function __toString() : string{
		return $this->getFullVersion();
	}
	public function compare(VersionString $target, bool $diff = false) : int{
		$number = $this->getNumber();
		$tNumber = $target->getNumber();
		if($diff){
			return $tNumber - $number;
		}
		if($number > $tNumber){
			return -1; 
		}elseif($number < $tNumber){
			return 1; 
		}elseif($target->isDev() and !$this->isDev()){
			return -1; 
		}elseif($target->getBuild() > $this->getBuild()){
			return 1;
		}elseif($target->getBuild() < $this->getBuild()){
			return -1;
		}else{
			return 0; 
		}
	}}