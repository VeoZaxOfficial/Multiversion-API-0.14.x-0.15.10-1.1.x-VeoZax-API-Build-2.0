<?php

namespace pocketmine;
final class VeoZaxSignature{
	const PROTECTED_FILES = [
		"pocketmine/VersionInfo.php" => "802c0f5557a0958af2ec4650ed65a146712601621d0b79a6efcc8e3c8d880515",
	];
	private static $lastResult = null;
	private static $lastFailureReason = null;
	private function __construct(){} 
	public static function check() : bool{
		if(!defined('pocketmine\NAME') or NAME !== "VeoZaxAPI"){
			self::$lastFailureReason = "product name has been changed (expected \"VeoZaxAPI\")";
			self::$lastResult = false;
			return false;
		}
		$root = \pocketmine\PATH . "src" . DIRECTORY_SEPARATOR;
		foreach(self::PROTECTED_FILES as $relative => $expectedHash){
			$path = $root . $relative;
			if(!is_file($path)){
				self::$lastFailureReason = "missing required file: {$relative}";
				self::$lastResult = false;
				return false;
			}
			$actualHash = @hash_file("sha256", $path);
			if($actualHash === false or $actualHash !== $expectedHash){
				self::$lastFailureReason = "modified file: {$relative}";
				self::$lastResult = false;
				return false;
			}
		}
		self::$lastResult = true;
		self::$lastFailureReason = null;
		return true;
	}
	public static function requireOrDie(){
		if(self::check()){
			return;
		}
		$reason = self::$lastFailureReason ?? "unknown integrity failure";
		fwrite(STDERR, PHP_EOL);
		fwrite(STDERR, "===============================================================" . PHP_EOL);
		fwrite(STDERR, " VeoZaxAPI integrity check failed: {$reason}" . PHP_EOL);
		fwrite(STDERR, " This copy of VeoZaxAPI has been altered or renamed. Restore the" . PHP_EOL);
		fwrite(STDERR, " original VeoZaxAPI files and try again." . PHP_EOL);
		fwrite(STDERR, "===============================================================" . PHP_EOL);
		fwrite(STDERR, PHP_EOL);
		exit(1);
	}
	private static $lastPeriodicCheck = 0.0;
	public static function periodicCheck(float $intervalSeconds = 300.0) : bool{
		$now = microtime(true);
		if(($now - self::$lastPeriodicCheck) < $intervalSeconds){
			return self::$lastResult ?? true;
		}
		self::$lastPeriodicCheck = $now;
		return self::check();
	}
	public static function getLastFailureReason(){
		return self::$lastFailureReason;
	}}