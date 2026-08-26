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
use pocketmine\errorhandler\ErrorToExceptionHandler;use Symfony\Component\Filesystem\Path;use function copy;use function dirname;use function fclose;use function fflush;use function file_exists;use function file_get_contents;use function file_put_contents;use function flock;use function fopen;use function ftruncate;use function fwrite;use function getmypid;use function is_dir;use function is_file;use function ltrim;use function mkdir;use function preg_match;use function realpath;use function rename;use function rmdir;use function rtrim;use function scandir;use function str_replace;use function str_starts_with;use function stream_get_contents;use function strlen;use function uksort;use function unlink;use const DIRECTORY_SEPARATOR;use const LOCK_EX;use const LOCK_NB;use const LOCK_SH;use const LOCK_UN;use const SCANDIR_SORT_NONE;
final class Filesystem{
	private static array $lockFileHandles = [];
	private static array $cleanedPaths = [
		 "pmsrc" => self::CLEAN_PATH_SRC_PREFIX
	];
	public const CLEAN_PATH_SRC_PREFIX = "pmsrc";
	public const CLEAN_PATH_PLUGINS_PREFIX = "plugins";
	private function __construct(){
	}
	public static function recursiveUnlink(string $dir) : void{
		if(is_dir($dir)){
			$objects = Utils::assumeNotFalse(scandir($dir, SCANDIR_SORT_NONE), "scandir() shouldn't return false when is_dir() returns true");
			foreach($objects as $object){
				if($object !== "." && $object !== ".."){
					$fullObject = Path::join($dir, $object);
					if(is_dir($fullObject)){
						self::recursiveUnlink($fullObject);
					}else{
						unlink($fullObject);
					}
				}
			}
			rmdir($dir);
		}elseif(is_file($dir)){
			unlink($dir);
		}
	}
	public static function recursiveCopy(string $origin, string $destination) : void{
		if(!is_dir($origin)){
			throw new \RuntimeException("$origin does not exist, or is not a directory");
		}
		if(!is_dir($destination)){
			if(file_exists($destination)){
				throw new \RuntimeException("$destination already exists, and is not a directory");
			}
			if(!is_dir(dirname($destination))){
				throw new \RuntimeException("The parent directory of $destination does not exist, or is not a directory");
			}
			try{
				ErrorToExceptionHandler::trap(fn() => mkdir($destination));
			}catch(\ErrorException $e){
				if(!is_dir($destination)){
					throw new \RuntimeException("Failed to create output directory $destination: " . $e->getMessage());
				}
			}
		}
		self::recursiveCopyInternal($origin, $destination);
	}
	private static function recursiveCopyInternal(string $origin, string $destination) : void{
		if(is_dir($origin)){
			if(!is_dir($destination)){
				if(file_exists($destination)){
					throw new \RuntimeException("Path $destination does not exist, or is not a directory");
				}
				mkdir($destination); 
			}
			$objects = Utils::assumeNotFalse(scandir($origin, SCANDIR_SORT_NONE));
			foreach($objects as $object){
				if($object === "." || $object === ".."){
					continue;
				}
				self::recursiveCopyInternal(Path::join($origin, $object), Path::join($destination, $object));
			}
		}else{
			$dirName = dirname($destination);
			if(!is_dir($dirName)){ 
				throw new AssumptionFailedError("The destination folder should have been created in the parent call");
			}
			copy($origin, $destination);
		}
	}
	public static function addCleanedPath(string $path, string $replacement) : void{
		self::$cleanedPaths[$path] = $replacement;
		uksort(self::$cleanedPaths, function(string $str1, string $str2) : int{
			return strlen($str2) <=> strlen($str1); 
		});
	}
	public static function getCleanedPaths() : array{ return self::$cleanedPaths; }
	public static function cleanPath(string $path) : string{
		$result = str_replace([DIRECTORY_SEPARATOR, ".php", "phar://"], ["/", "", ""], $path);
		foreach(Utils::stringifyKeys(self::$cleanedPaths) as $cleanPath => $replacement){
			$cleanPath = rtrim(str_replace([DIRECTORY_SEPARATOR, "phar://"], ["/", ""], $cleanPath), "/");
			if(str_starts_with($result, $cleanPath)){
				$result = ltrim(str_replace($cleanPath, $replacement, $result), "/");
			}
		}
		return $result;
	}
	public static function createLockFile(string $lockFilePath) : ?int{
		try{
			$resource = ErrorToExceptionHandler::trapAndRemoveFalse(fn() => fopen($lockFilePath, "a+b"));
		}catch(\ErrorException $e){
			throw new \InvalidArgumentException("Failed to open lock file: " . $e->getMessage(), 0, $e);
		}
		if(!flock($resource, LOCK_EX | LOCK_NB)){
			flock($resource, LOCK_SH);
			$pid = Utils::assumeNotFalse(stream_get_contents($resource), "This is a known valid file resource, at worst we should receive an empty string");
			if(preg_match('/^\d+$/', $pid) === 1){
				return (int) $pid;
			}
			return -1;
		}
		ftruncate($resource, 0);
		fwrite($resource, (string) getmypid());
		fflush($resource);
		flock($resource, LOCK_SH); 
		self::$lockFileHandles[realpath($lockFilePath)] = $resource; 
		return null;
	}
	public static function releaseLockFile(string $lockFilePath) : void{
		$lockFilePath = realpath($lockFilePath);
		if($lockFilePath === false){
			throw new \InvalidArgumentException("Invalid lock file path");
		}
		if(isset(self::$lockFileHandles[$lockFilePath])){
			flock(self::$lockFileHandles[$lockFilePath], LOCK_UN);
			fclose(self::$lockFileHandles[$lockFilePath]);
			unset(self::$lockFileHandles[$lockFilePath]);
			@unlink($lockFilePath);
		}
	}
	public static function safeFilePutContents(string $fileName, string $contents, int $flags = 0, $context = null) : void{
		$directory = dirname($fileName);
		if(!is_dir($directory)){
			throw new \RuntimeException("Target directory path does not exist or is not a directory");
		}
		if(is_dir($fileName)){
			throw new \RuntimeException("Target file path already exists and is not a file");
		}
		$counter = 0;
		do{
			$temporaryFileName = $fileName . ".$counter.tmp";
			$counter++;
		}while(is_dir($temporaryFileName));
		try{
			ErrorToExceptionHandler::trap(fn() => $context !== null ?
				file_put_contents($temporaryFileName, $contents, $flags, $context) :
				file_put_contents($temporaryFileName, $contents, $flags)
			);
		}catch(\ErrorException $filePutContentsException){
			$context !== null ?
				@unlink($temporaryFileName, $context) :
				@unlink($temporaryFileName);
			throw new \RuntimeException("Failed to write to temporary file $temporaryFileName: " . $filePutContentsException->getMessage(), 0, $filePutContentsException);
		}
		$renameTemporaryFileResult = $context !== null ?
			@rename($temporaryFileName, $fileName, $context) :
			@rename($temporaryFileName, $fileName);
		if(!$renameTemporaryFileResult){
			try{
				ErrorToExceptionHandler::trap(fn() => $context !== null ?
					copy($temporaryFileName, $fileName, $context) :
					copy($temporaryFileName, $fileName)
				);
			}catch(\ErrorException $copyException){
				throw new \RuntimeException("Failed to move temporary file contents into target file: " . $copyException->getMessage(), 0, $copyException);
			}
			@unlink($temporaryFileName);
		}
	}
	public static function fileGetContents(string $fileName, bool $useIncludePath = false, $context = null, int $offset = 0, ?int $length = null) : string{
		try{
			return ErrorToExceptionHandler::trapAndRemoveFalse(fn() => file_get_contents($fileName, $useIncludePath, $context, $offset, $length));
		}catch(\ErrorException $e){
			throw new \RuntimeException("Failed to read file $fileName: " . $e->getMessage(), 0, $e);
		}
	}}