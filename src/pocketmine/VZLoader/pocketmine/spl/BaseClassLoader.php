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


class BaseClassLoader extends Threaded implements ClassLoader{
	private $parent;
	private $lookup;
	private $classes;
	public function __construct(ClassLoader $parent = null){
		$this->parent = $parent;
		$this->lookup = new Threaded;
		$this->classes = new Threaded;
	}
	public function addPath($path, $prepend = false){
		foreach($this->lookup as $p){
			if($p === $path){
				return;
			}
		}
		if($prepend){
			$this->synchronized(function($path){
				$entries = $this->getAndRemoveLookupEntries();
				$this->lookup[] = $path;
				foreach($entries as $entry){
					$this->lookup[] = $entry;
				}
			}, $path);
		}else{
			$this->lookup[] = $path;
		}
	}
	protected function getAndRemoveLookupEntries(){
		$entries = [];
		while($this->lookup->count() > 0){
			$entries[] = $this->lookup->shift();
		}
		return $entries;
	}
	public function removePath($path){
		foreach($this->lookup as $i => $p){
			if($p === $path){
				unset($this->lookup[$i]);
			}
		}
	}
	public function getClasses(){
		$classes = [];
		foreach($this->classes as $class){
			$classes[] = $class;
		}
		return $classes;
	}
	public function getParent(){
		return $this->parent;
	}
	public function register($prepend = false){
		spl_autoload_register([$this, "loadClass"], true, $prepend);
	}
	public function loadClass($name){
		$path = $this->findClass($name);
		if($path !== null){
			include($path);
			if(!class_exists($name, false) and !interface_exists($name, false) and !trait_exists($name, false)){
				return false;
			}
			if(method_exists($name, "onClassLoaded") and (new ReflectionClass($name))->getMethod("onClassLoaded")->isStatic()){
				$name::onClassLoaded();
			}
			$this->classes[] = $name;
			return true;
		}
		return false;
	}
	public function findClass($name){
		$baseName = str_replace("\\", DIRECTORY_SEPARATOR, $name);
		foreach($this->lookup as $path){
			if(PHP_INT_SIZE === 8 and file_exists($path . DIRECTORY_SEPARATOR . $baseName . "__64bit.php")){
				return $path . DIRECTORY_SEPARATOR . $baseName . "__64bit.php";
			}elseif(PHP_INT_SIZE === 4 and file_exists($path . DIRECTORY_SEPARATOR . $baseName . "__32bit.php")){
				return $path . DIRECTORY_SEPARATOR . $baseName . "__32bit.php";
			}elseif(file_exists($path . DIRECTORY_SEPARATOR . $baseName . ".php")){
				return $path . DIRECTORY_SEPARATOR . $baseName . ".php";
			}
		}
		return null;
	}}