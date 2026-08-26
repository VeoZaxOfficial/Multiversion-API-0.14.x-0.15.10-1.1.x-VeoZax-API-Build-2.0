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
abstract class Type{
    const WEAK = 0x01;
    const NULLABLE  = 0x02;
    const REFERENCE = 0x04;
    public $typeName;
    public $isNullable;
    public $isByReference;
    public $isWeak;
    public $allowsCovariance;
    public $allowsContravariance;
    protected function __construct($typeName, $flags, $allowsCovariance, $allowsContravariance)
    {
        $this->typeName = $typeName !== null
            ? (string)$typeName
            : null;
        $this->isNullable = (bool)($flags & self::NULLABLE);
        $this->isByReference = (bool)($flags & self::REFERENCE);
        $this->isWeak = (bool)($flags & self::WEAK);
        $this->allowsCovariance = (bool)$allowsCovariance;
        $this->allowsContravariance = (bool)$allowsContravariance;
    }
    public function isSatisfiedBy($typeName, $nullable, $byReference)
    {
        if ($byReference xor $this->isByReference) {
            return false;
        }
        if ($typeName === $this->typeName && $nullable === $this->isNullable) {
            return true;
        }
        if ($this->allowsCovariance
            && MatchTester::isMatch($this->typeName, $this->isNullable, $typeName, $nullable, $this->isWeak)) {
            return true;
        }
        if ($this->allowsContravariance
            && MatchTester::isMatch($typeName, $nullable, $this->typeName, $this->isNullable, $this->isWeak)) {
            return true;
        }
        return $this->isWeak
            && $nullable === $this->isNullable
            && MatchTester::isWeakScalarMatch($typeName, $this->typeName);
    }}