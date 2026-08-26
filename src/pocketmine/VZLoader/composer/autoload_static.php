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

namespace Composer\Autoload;
class ComposerStaticInit7484194506a5afb13f6892b2384c13b8{
    public static $files = array (
        '9c3c6f7f4a17396c3ff535f7d7c38ad4' => __DIR__ . '/../../../..' . '/src/pocketmine/GlobalConstants.php',
        '89d5de50ff2daa656af29fba38fbd9af' => __DIR__ . '/../../../..' . '/src/pocketmine/VersionInfo.php',
    );
    public static $prefixLengthsPsr4 = array (
        'r' => 
        array (
            'raklib\\' => 7,
        ),
        'p' => 
        array (
            'pocketmine\\utils\\' => 17,
            'pocketmine\\snooze\\' => 18,
            'pocketmine\\nbt\\' => 15,
            'pocketmine\\math\\' => 16,
            'pocketmine\\errorhandler\\' => 24,
            'pocketmine\\' => 11,
        ),
        'F' => 
        array (
            'FG\\' => 3,
        ),
        'D' => 
        array (
            'DaveRandom\\CallbackValidator\\' => 29,
        ),
        'A' => 
        array (
            'Ahc\\Json\\' => 9,
        ),
    );
    public static $prefixDirsPsr4 = array (
        'raklib\\' => 
        array (
            0 => __DIR__ . '/..' . '/multimcpe/raklib/src',
        ),
        'pocketmine\\utils\\' => 
        array (
            0 => __DIR__ . '/..' . '/multimcpe/binaryutils/src',
        ),
        'pocketmine\\snooze\\' => 
        array (
            0 => __DIR__ . '/..' . '/multimcpe/snooze/src',
        ),
        'pocketmine\\nbt\\' => 
        array (
            0 => __DIR__ . '/..' . '/multimcpe/nbt/src',
        ),
        'pocketmine\\math\\' => 
        array (
            0 => __DIR__ . '/..' . '/multimcpe/math/src',
        ),
        'pocketmine\\errorhandler\\' => 
        array (
            0 => __DIR__ . '/..' . '/pocketmine/errorhandler/src',
        ),
        'pocketmine\\' => 
        array (
            0 => __DIR__ . '/../../../..' . '/src/pocketmine',
        ),
        'FG\\' => 
        array (
            0 => __DIR__ . '/..' . '/fgrosse/phpasn1/lib',
        ),
        'DaveRandom\\CallbackValidator\\' => 
        array (
            0 => __DIR__ . '/..' . '/daverandom/callback-validator/src',
        ),
        'Ahc\\Json\\' => 
        array (
            0 => __DIR__ . '/..' . '/adhocore/json-comment/src',
        ),
    );
    public static $classMap = array (
        'AttachableLogger' => __DIR__ . '/..' . '/multimcpe/spl/AttachableLogger.php',
        'BufferedLogger' => __DIR__ . '/..' . '/multimcpe/spl/BufferedLogger.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'GlobalLogger' => __DIR__ . '/..' . '/multimcpe/spl/GlobalLogger.php',
        'InvalidStateException' => __DIR__ . '/..' . '/multimcpe/spl/InvalidStateException.php',
        'LogLevel' => __DIR__ . '/..' . '/multimcpe/spl/LogLevel.php',
        'Logger' => __DIR__ . '/..' . '/multimcpe/spl/Logger.php',
        'PrefixedLogger' => __DIR__ . '/..' . '/multimcpe/spl/PrefixedLogger.php',
        'SimpleLogger' => __DIR__ . '/..' . '/multimcpe/spl/SimpleLogger.php',
    );
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7484194506a5afb13f6892b2384c13b8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7484194506a5afb13f6892b2384c13b8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7484194506a5afb13f6892b2384c13b8::$classMap;
        }, null, ClassLoader::class);
    }}