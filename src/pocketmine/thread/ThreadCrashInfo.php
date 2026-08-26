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
namespace pocketmine\thread;
use pmmp\thread\ThreadSafe;use pmmp\thread\ThreadSafeArray;use pocketmine\errorhandler\ErrorTypeToStringMap;use pocketmine\utils\Filesystem;use pocketmine\utils\Utils;use function get_class;use function sprintf;use Throwable;
final class ThreadCrashInfo extends ThreadSafe{
	private ThreadSafeArray $trace;
	public function __construct(
		private string $type,
		private string $message,
		private string $file,
		private int $line,
		array $trace,
		private string $threadName
	){
		$this->trace = ThreadSafeArray::fromArray($trace);
	}
	public static function fromThrowable(Throwable $e, string $threadName) : self{
		return new self(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), Utils::printableTraceWithMetadata($e->getTrace()), $threadName);
	}
	public static function fromLastErrorInfo(array $info, string $threadName) : self{
		try{
			$class = ErrorTypeToStringMap::get($info["type"]);
		}catch(\InvalidArgumentException){
			$class = "Unknown error type (" . $info["type"] . ")";
		}
		return new self($class, $info["message"], $info["file"], $info["line"], Utils::printableTraceWithMetadata(Utils::currentTrace()), $threadName);
	}
	public function getType() : string{ return $this->type; }
	public function getMessage() : string{ return $this->message; }
	public function getFile() : string{ return $this->file; }
	public function getLine() : int{ return $this->line; }
	public function getTrace() : array{
		return (array) $this->trace;
	}
	public function getThreadName() : string{ return $this->threadName; }
	public function makePrettyMessage() : string{
		return sprintf("%s: \"%s\" in \"%s\" on line %d", $this->type ?? "Fatal error", $this->message, Filesystem::cleanPath($this->file), $this->line);
	}}