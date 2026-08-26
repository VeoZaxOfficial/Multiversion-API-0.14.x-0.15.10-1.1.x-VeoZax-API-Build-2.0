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
final class ParameterType extends Type{
    const CONTRAVARIANT = 0x01 << 8;
    const COVARIANT = 0x02 << 8;
    const VARIADIC = 0x04 << 8;
    const OPTIONAL = 0x08 << 8;
    private $parameterName;
    public $isVariadic;
    public $isOptional;
    public static function createFromReflectionParameter($reflection, $flags = 0)
    {
        $parameterName = $reflection->getName();
        if ($reflection->isPassedByReference()) {
            $flags |= self::REFERENCE;
        }
        if ($reflection->isVariadic()) {
            $flags |= self::VARIADIC;
        }
        if ($reflection->isOptional()) {
            $flags |= self::OPTIONAL;
        }
        $typeName = null;
        $typeReflection = $reflection->getType();
        if ($typeReflection !== null) {
            $typeName = (string)$typeReflection;
            if ($typeReflection->allowsNull()) {
                $flags |= self::NULLABLE;
            }
        }
        return new self($parameterName, $typeName, $flags);
    }
    public function __construct($parameterName, $typeName = null, $flags = self::CONTRAVARIANT)
    {
        $flags = (int)$flags;
        parent::__construct($typeName, $flags, $flags & self::COVARIANT, $flags & self::CONTRAVARIANT);
        $this->parameterName = (string)$parameterName;
        $this->isOptional = (bool)($flags & self::OPTIONAL);
        $this->isVariadic = (bool)($flags & self::VARIADIC);
    }
    public function __toString()
    {
        $string = '';
        if ($this->typeName !== null) {
            if ($this->isNullable) {
                $string .= '?';
            }
            $string .= $this->typeName . ' ';
        }
        if ($this->isByReference) {
            $string .= '&';
        }
        if ($this->isVariadic) {
            $string .= '...';
        }
        return $string . '$' . $this->parameterName;
    }}