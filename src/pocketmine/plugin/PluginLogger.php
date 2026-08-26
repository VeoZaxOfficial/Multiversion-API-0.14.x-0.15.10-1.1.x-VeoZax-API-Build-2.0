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
namespace pocketmine\plugin;
use AttachableLogger;use LogLevel;use pocketmine\Server;use Throwable;use Closure;use function spl_object_id;
class PluginLogger implements AttachableLogger{
	private $pluginName;
	private $attachments = [];
	public function addAttachment(Closure $attachment){
		$this->attachments[spl_object_id($attachment)] = $attachment;
	}
	public function removeAttachment(Closure $attachment){
		unset($this->attachments[spl_object_id($attachment)]);
	}
	public function removeAttachments(){
		$this->attachments = [];
	}
	public function getAttachments(){
		return $this->attachments;
	}
	public function __construct(Plugin $context){
		$prefix = $context->getDescription()->getPrefix();
		$this->pluginName = $prefix != null ? "[$prefix] " : "[" . $context->getDescription()->getName() . "] ";
	}
	public function emergency($message){
		$this->log(LogLevel::EMERGENCY, $message);
	}
	public function alert($message){
		$this->log(LogLevel::ALERT, $message);
	}
	public function critical($message){
		$this->log(LogLevel::CRITICAL, $message);
	}
	public function error($message){
		$this->log(LogLevel::ERROR, $message);
	}
	public function warning($message){
		$this->log(LogLevel::WARNING, $message);
	}
	public function notice($message){
		$this->log(LogLevel::NOTICE, $message);
	}
	public function info($message){
		$this->log(LogLevel::INFO, $message);
	}
	public function debug($message){
		$this->log(LogLevel::DEBUG, $message);
	}
	public function logException(Throwable $e, $trace = null){
		Server::getInstance()->getLogger()->logException($e, $trace);
	}
	public function log($level, $message){
		Server::getInstance()->getLogger()->log($level, $this->pluginName . $message);
		foreach($this->attachments as $attachment){
			$attachment->log($level, $message);
		}
	}}