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

namespace FG\Utility;
abstract class BigInteger{
    private static $_prefer;
    public static function setPrefer($prefer = null)
    {
        self::$_prefer = $prefer;
    }
    public static function create($val)
    {
        if (self::$_prefer) {
            switch (self::$_prefer) {
                case 'gmp':
                    $ret = new BigIntegerGmp();
                    break;
                case 'bcmath':
                    $ret = new BigIntegerBcmath();
                    break;
                default:
                    throw new \UnexpectedValueException('Unknown number implementation: ' . self::$_prefer);
            }
        }
        else {
            if (function_exists('gmp_add')) {
                $ret = new BigIntegerGmp();
            }
            elseif (function_exists('bcadd')) {
                $ret = new BigIntegerBcmath();
            } else {
                throw new \RuntimeException('Requires GMP or bcmath extension.');
            }
        }
        if (is_int($val)) {
            $ret->_fromInteger($val);
        }
        else {
            $val = (string)$val;
            if (!preg_match('/^-?[0-9]+$/', $val)) {
                throw new \InvalidArgumentException('Expects a string representation of an integer.');
            }
            $ret->_fromString($val);
        }
        return $ret;
    }
    protected function __construct()
    {
    }
    abstract public function __clone();
    abstract protected function _fromString($str);
    abstract protected function _fromInteger($integer);
    abstract public function __toString();
    abstract public function toInteger();
    abstract public function isNegative();
    abstract public function compare($number);
    abstract public function add($b);
    abstract public function subtract($b);
    abstract public function multiply($b);
    abstract public function modulus($b);
    abstract public function toPower($b);
    abstract public function shiftRight($bits = 8);
    abstract public function shiftLeft($bits = 8);
    abstract public function absoluteValue();}