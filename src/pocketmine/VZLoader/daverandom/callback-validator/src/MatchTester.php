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

 declare(strict_types = 1);
namespace DaveRandom\CallbackValidator;
final class MatchTester{
    private function __construct() { }
    private static $builtInTypes = [
        BuiltInTypes::STRING   => true,
        BuiltInTypes::INT      => true,
        BuiltInTypes::FLOAT    => true,
        BuiltInTypes::BOOL     => true,
        BuiltInTypes::ARRAY    => true,
        BuiltInTypes::CALLABLE => true,
        BuiltInTypes::VOID     => true,
        BuiltInTypes::ITERABLE => true,
    ];
    private static $scalarTypes = [
        BuiltInTypes::STRING => true,
        BuiltInTypes::INT    => true,
        BuiltInTypes::FLOAT  => true,
        BuiltInTypes::BOOL   => true,
    ];
    public static function isWeakScalarMatch($superTypeName, $subTypeName)
    {
        if (!isset(self::$scalarTypes[$superTypeName])) {
            return false;
        }
        if (isset(self::$scalarTypes[$subTypeName])) {
            return true;
        }
        if ($superTypeName === BuiltInTypes::STRING && \method_exists($subTypeName, '__toString')) {
            return true;
        }
        return false;
    }
    public static function isMatch($superTypeName, $superTypeNullable, $subTypeName, $subTypeNullable, $weak)
    {
        if ($superTypeName === null) {
            return true;
        }
        if ($subTypeName === null) {
            return false;
        }
        $superTypeName = (string)$superTypeName;
        $subTypeName = (string)$subTypeName;
        if ($subTypeNullable && !$superTypeNullable) {
            return $superTypeName === BuiltInTypes::VOID && $subTypeName === BuiltInTypes::VOID;
        }
        if ($superTypeName === $subTypeName) {
            return true;
        }
        if ($superTypeName === BuiltInTypes::ITERABLE) {
            return $subTypeName === BuiltInTypes::ARRAY
                || $subTypeName === \Traversable::class
                || \is_subclass_of($subTypeName, \Traversable::class);
        }
        if ($superTypeName === BuiltInTypes::CALLABLE) {
            return $subTypeName === \Closure::class
                || \method_exists($subTypeName, '__invoke')
                || \is_subclass_of($subTypeName, \Closure::class);
        }
        if (isset(self::$builtInTypes[$superTypeName])) {
            return $weak && self::isWeakScalarMatch($superTypeName, $subTypeName);
        }
        if (isset(self::$builtInTypes[$subTypeName])) {
            return false;
        }
        return \is_subclass_of($subTypeName, $superTypeName);
    }}