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
use GMP;use OverflowException;use UnexpectedValueException;
class BigIntegerGmp extends BigInteger{
    protected $_rh;
    public function __clone()
    {
        $this->_rh = gmp_add($this->_rh, 0);
    }
    protected function _fromString($str)
    {
        $this->_rh = gmp_init($str, 10);
    }
    protected function _fromInteger($integer)
    {
        $this->_rh = gmp_init($integer, 10);
    }
    public function __toString()
    {
        return gmp_strval($this->_rh, 10);
    }
    public function toInteger()
    {
        if ($this->compare(PHP_INT_MAX) > 0 || $this->compare(PHP_INT_MIN) < 0) {
            throw new OverflowException(sprintf('Can not represent %s as integer.', $this));
        }
        return gmp_intval($this->_rh);
    }
    public function isNegative()
    {
        return gmp_sign($this->_rh) === -1;
    }
    protected function _unwrap($number)
    {
        if ($number instanceof self) {
            return $number->_rh;
        }
        return $number;
    }
    public function compare($number)
    {
        return gmp_cmp($this->_rh, $this->_unwrap($number));
    }
    public function add($b)
    {
        $ret = new self();
        $ret->_rh = gmp_add($this->_rh, $this->_unwrap($b));
        return $ret;
    }
    public function subtract($b)
    {
        $ret = new self();
        $ret->_rh = gmp_sub($this->_rh, $this->_unwrap($b));
        return $ret;
    }
    public function multiply($b)
    {
        $ret = new self();
        $ret->_rh = gmp_mul($this->_rh, $this->_unwrap($b));
        return $ret;
    }
    public function modulus($b)
    {
        $ret = new self();
        $ret->_rh = gmp_mod($this->_rh, $this->_unwrap($b));
        return $ret;
    }
    public function toPower($b)
    {
        if ($b instanceof self) {
            if ($b->compare(PHP_INT_MAX) > 0) {
                throw new UnexpectedValueException('Unable to raise to power greater than PHP_INT_MAX.');
            }
            $b = gmp_intval($b->_rh);
        }
        $ret = new self();
        $ret->_rh = gmp_pow($this->_rh, $b);
        return $ret;
    }
    public function shiftRight($bits=8)
    {
        $ret = new self();
        $ret->_rh = gmp_div($this->_rh, gmp_pow(2, $bits));
        return $ret;
    }
    public function shiftLeft($bits=8)
    {
        $ret = new self();
        $ret->_rh = gmp_mul($this->_rh, gmp_pow(2, $bits));
        return $ret;
    }
    public function absoluteValue()
    {
        $ret = new self();
        $ret->_rh = gmp_abs($this->_rh);
        return $ret;
    }}