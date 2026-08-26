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
class BigIntegerBcmath extends BigInteger{
    protected $_str;
    public function __clone()
    {
    }
    protected function _fromString($str)
    {
        $this->_str = (string)$str;
    }
    protected function _fromInteger($integer)
    {
        $this->_str = (string)$integer;
    }
    public function __toString()
    {
        return $this->_str;
    }
    public function toInteger()
    {
        if ($this->compare(PHP_INT_MAX) > 0 || $this->compare(PHP_INT_MIN) < 0) {
            throw new \OverflowException(sprintf('Can not represent %s as integer.', $this->_str));
        }
        return (int)$this->_str;
    }
    public function isNegative()
    {
        return bccomp($this->_str, '0', 0) < 0;
    }
    protected function _unwrap($number)
    {
        if ($number instanceof self) {
            return $number->_str;
        }
        return $number;
    }
    public function compare($number)
    {
        return bccomp($this->_str, $this->_unwrap($number), 0);
    }
    public function add($b)
    {
        $ret = new self();
        $ret->_str = bcadd($this->_str, $this->_unwrap($b), 0);
        return $ret;
    }
    public function subtract($b)
    {
        $ret = new self();
        $ret->_str = bcsub($this->_str, $this->_unwrap($b), 0);
        return $ret;
    }
    public function multiply($b)
    {
        $ret = new self();
        $ret->_str = bcmul($this->_str, $this->_unwrap($b), 0);
        return $ret;
    }
    public function modulus($b)
    {
        $ret = new self();
        if ($this->isNegative()) {
            $b = $this->_unwrap($b);
            $ret->_str = bcsub($b, bcmod(bcsub('0', $this->_str, 0), $b), 0);
        }
        else {
            $ret->_str = bcmod($this->_str, $this->_unwrap($b));
        }
        return $ret;
    }
    public function toPower($b)
    {
        $ret = new self();
        $ret->_str = bcpow($this->_str, $this->_unwrap($b), 0);
        return $ret;
    }
    public function shiftRight($bits = 8)
    {
        $ret = new self();
        $ret->_str = bcdiv($this->_str, bcpow('2', $bits));
        return $ret;
    }
    public function shiftLeft($bits = 8) {
        $ret = new self();
        $ret->_str = bcmul($this->_str, bcpow('2', $bits));
        return $ret;
    }
    public function absoluteValue()
    {
        $ret = new self();
        if (-1 === bccomp($this->_str, '0', 0)) {
            $ret->_str = bcsub('0', $this->_str, 0);
        }
        else {
            $ret->_str = $this->_str;
        }
        return $ret;
    }}