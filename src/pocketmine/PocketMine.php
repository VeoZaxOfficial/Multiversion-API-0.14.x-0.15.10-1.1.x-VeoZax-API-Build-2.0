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
namespace pocketmine {
	use GlobalLogger;
    use Logger;
    use Phar;
	use DateTimeZone;
	use pmmp\thread\Thread as NativeThread;
	use pocketmine\thread\ThreadManager;
	use pocketmine\thread\ThreadSafeClassLoader;
    use pocketmine\utils\MainLogger;
    use pocketmine\utils\Config;
    use pocketmine\utils\ServerKiller;
    use pocketmine\utils\Terminal;
    use pocketmine\utils\Timezone;
    use pocketmine\utils\Utils;
    use pocketmine\utils\VersionString;
    use pocketmine\wizard\SetupWizard;
    require_once __DIR__ . '/VersionInfo.php';
	const MIN_PHP_VERSION = "8.2.0";
	function critical_error($message){
		echo "[ERROR] $message" . PHP_EOL;
	}
	function check_platform_dependencies(){
		if(version_compare(MIN_PHP_VERSION, PHP_VERSION) > 0){
			return [
				"PHP >= " . MIN_PHP_VERSION . " is required, but you have PHP " . PHP_VERSION . "."
			];
		}
		$messages = [];
		if(PHP_INT_SIZE < 8){
			$messages[] = "64-bit systems/PHP are no longer supported. Please upgrade to a 64-bit system, or use a 64-bit PHP binary if this is a 64-bit system.";
		}
		if(php_sapi_name() !== "cli"){
			$messages[] = "Only PHP CLI is supported.";
		}
		$extensions = [
			"chunkutils2" => "PocketMine ChunkUtils v2",
			"bcmath" => "BC Math",
			"curl" => "cURL",
			"ctype" => "ctype",
			"date" => "Date",
			"hash" => "Hash",
			"json" => "JSON",
			"mbstring" => "Multibyte String",
			"openssl" => "OpenSSL",
			"pcre" => "PCRE",
			"phar" => "Phar",
			"pmmpthread" => "pmmpthread",
			"reflection" => "Reflection",
			"sockets" => "Sockets",
			"spl" => "SPL",
			"yaml" => "YAML",
			"zip" => "Zip",
			"zlib" => "Zlib"
		];
		foreach($extensions as $ext => $name){
			if(!extension_loaded($ext)){
				$messages[] = "Unable to find the $name ($ext) extension.";
			}
		}
		if(extension_loaded("pmmpthread")){
			$pmmpthread_version = phpversion("pmmpthread");
			if(substr_count($pmmpthread_version, ".") < 2){
				$pmmpthread_version = "0.$pmmpthread_version";
			}
			if(version_compare($pmmpthread_version, "6.0.4") < 0 || version_compare($pmmpthread_version, "7.0.0") > 0){
				$messages[] = "pmmpthread ^6.0.7 is required, while you have $pmmpthread_version.";
			}
		}
		if(extension_loaded("leveldb")){
			$leveldb_version = phpversion("leveldb");
			if(version_compare($leveldb_version, "0.2.1") < 0){
				$messages[] = "php-leveldb >= 0.2.1 is required, while you have $leveldb_version.";
			}
		}
		$chunkutils2_version = phpversion("chunkutils2");
		$wantedVersionLock = "0.3";
		$wantedVersionMin = "$wantedVersionLock.0";
		if($chunkutils2_version !== false && (
				version_compare($chunkutils2_version, $wantedVersionMin) < 0 ||
				preg_match("/^" . preg_quote($wantedVersionLock, "/") . "\.\d+(?:-dev)?$/", $chunkutils2_version) === 0 
			)){
			$messages[] = "chunkutils2 ^$wantedVersionMin is required, while you have $chunkutils2_version.";
		}
		if(extension_loaded("pocketmine")){
			$messages[] = "The native PocketMine extension is no longer supported.";
		}
		return $messages;
	}
	function emit_performance_warnings(Logger $logger){
		if(extension_loaded("xdebug")){
			$logger->warning("Xdebug extension is enabled. This has a major impact on performance.");
		}
		if(((int) ini_get('zend.assertions')) !== -1){
			$logger->warning("Debugging assertions are enabled. This may degrade performance. To disable them, set `zend.assertions = -1` in php.ini.");
		}
		if(Phar::running(true) === ""){
			$logger->warning("Non-packaged installation detected. This will degrade autoloading speed and make startup times longer.");
		}
	}
	function set_ini_entries(){
		ini_set("allow_url_fopen", '1');
		ini_set("display_errors", '1');
		ini_set("display_startup_errors", '1');
		ini_set("default_charset", "utf-8");
		ini_set('assert.exception', '1');
	}
	function server(){
		if(!empty($messages = check_platform_dependencies())){
			echo PHP_EOL;
			$binary = version_compare(PHP_VERSION, "5.4") >= 0 ? PHP_BINARY : "unknown";
			critical_error("Selected PHP binary ($binary) does not satisfy some requirements.");
			foreach($messages as $m){
				echo " - $m" . PHP_EOL;
			}
			critical_error("Please recompile PHP with the needed configuration, or refer to the installation instructions at http://pmmp.rtfd.io/en/rtfd/installation.html.");
			echo PHP_EOL;
			exit(1);
		}
		unset($messages);
		error_reporting(-1);
		set_ini_entries();
		if(Phar::running(true) !== ""){
			define('pocketmine\PATH', Phar::running(true) . "/");
		}else{
			define('pocketmine\PATH', dirname(__FILE__, 3) . DIRECTORY_SEPARATOR);
		}
		require_once PATH . 'src/pocketmine/VeoZaxSignature.php';
		\pocketmine\VeoZaxSignature::requireOrDie();
		$opts = getopt("", ["bootstrap:"]);
		if(isset($opts["bootstrap"])){
			$bootstrap = realpath($opts["bootstrap"]) ?: $opts["bootstrap"];
		}else{
			$bootstrap = PATH . 'src' . DIRECTORY_SEPARATOR . 'pocketmine' . DIRECTORY_SEPARATOR . 'VZLoader' . DIRECTORY_SEPARATOR . 'autoload.php';
		}
		define('pocketmine\COMPOSER_AUTOLOADER_PATH', $bootstrap);
		if(COMPOSER_AUTOLOADER_PATH !== false and is_file(COMPOSER_AUTOLOADER_PATH)){
			require_once(COMPOSER_AUTOLOADER_PATH);
		}else{
			critical_error("Composer autoloader not found at " . $bootstrap);
			critical_error("Please install/update Composer dependencies or use provided builds.");
			exit(1);
		}
		set_error_handler([Utils::class, 'errorExceptionHandler']);
		$version = new VersionString(BASE_VERSION, IS_DEVELOPMENT_BUILD, BUILD_NUMBER);
		define('pocketmine\VERSION', "0.2+VZ");
		$gitHash = str_repeat("00", 20);
		if(Phar::running(true) === ""){
			if(Utils::execute("git rev-parse HEAD", $out) === 0 and $out !== false and strlen($out = trim($out)) === 40){
				$gitHash = trim($out);
				if(Utils::execute("git diff --quiet") === 1 or Utils::execute("git diff --cached --quiet") === 1){ 
					$gitHash .= "-dirty";
				}
			}
		}else{
			$phar = new Phar(Phar::running(false));
			$meta = $phar->getMetadata();
			if(isset($meta["git"])){
				$gitHash = $meta["git"];
			}
		}
		define('pocketmine\GIT_COMMIT', $gitHash);
		define('pocketmine\RESOURCE_PATH', PATH . 'src' . DIRECTORY_SEPARATOR . 'pocketmine' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);
		$opts = getopt("", ["data:", "plugins:", "no-wizard", "enable-ansi", "disable-ansi"]);
		define('pocketmine\DATA', isset($opts["data"]) ? $opts["data"] . DIRECTORY_SEPARATOR : realpath(getcwd()) . DIRECTORY_SEPARATOR);
		define('pocketmine\PLUGIN_PATH', isset($opts["plugins"]) ? $opts["plugins"] . DIRECTORY_SEPARATOR : realpath(getcwd()) . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR);
		if(!file_exists(DATA)){
			mkdir(DATA, 0777, true);
		}
		define('pocketmine\LOCK_FILE', fopen(DATA . 'server.lock', "a+b"));
		if(!flock(LOCK_FILE, LOCK_EX | LOCK_NB)){
			flock(LOCK_FILE, LOCK_SH);
			$pid = stream_get_contents(LOCK_FILE);
			critical_error("Another " . NAME . " instance (PID $pid) is already using this folder (" . realpath(DATA) . ").");
			critical_error("Please stop the other server first before running a new one.");
			exit(1);
		}
		ftruncate(LOCK_FILE, 0);
		fwrite(LOCK_FILE, (string) getmypid());
		fflush(LOCK_FILE);
		flock(LOCK_FILE, LOCK_SH); 
		$tzError = Timezone::init();
		if(isset($opts["enable-ansi"])){
			Terminal::init(true);
		}elseif(isset($opts["disable-ansi"])){
			Terminal::init(false);
		}else{
			Terminal::init();
		}
		$logsPath = DATA . 'logs' . DIRECTORY_SEPARATOR;
		if (!is_dir($logsPath)) {
			if (!mkdir($logsPath)) {
				critical_error("an error occurred when creating the logs directory");
				exit(1);
			}
		}
		if(!file_exists(DATA . "VeoZax.yml")){
			@file_put_contents(DATA . "VeoZax.yml", file_get_contents(RESOURCE_PATH . "VeoZax.yml"));
		}
		$veoZaxConfig = new Config(DATA . "VeoZax.yml", Config::YAML, ["server-log" => true]);
		$serverLogEnabled = (bool) $veoZaxConfig->get("server-log", true);
		$logger = new MainLogger($logsPath . "server" . date("d-y-m") . ".log", new DateTimeZone('UTC'), false, $serverLogEnabled);
		GlobalLogger::set($logger);
		foreach($tzError as $e){
			$logger->warning($e);
		}
		unset($tzError);
		emit_performance_warnings($logger);
		$exitCode = 0;
		do{
			if(!file_exists(DATA . "server.properties") and !isset($opts["no-wizard"])){
				$installer = new SetupWizard();
				if(!$installer->run()){
					$exitCode = -1;
					break;
				}
			}
			define('pocketmine\START_TIME', microtime(true));
			ThreadManager::init();
			$autoloader = new ThreadSafeClassLoader();
			$autoloader->register(false);
			new Server($autoloader, $logger, DATA, PLUGIN_PATH);
			$logger->info("Stopping other threads");
			$killer = new ServerKiller(8);
			$killer->start(NativeThread::INHERIT_NONE);
			usleep(10000); 
			if(ThreadManager::getInstance()->stopAll() > 0){
				$logger->debug("Some threads could not be stopped, performing a force-kill");
				Utils::kill(getmypid());
			}
		}while(false);
		$logger->shutdown();
		echo Terminal::$FORMAT_RESET . PHP_EOL;
		exit($exitCode);
	}
	if(!defined('pocketmine\_PHPSTAN_ANALYSIS')){
		server();
	}}