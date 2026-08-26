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
final class CallbackType{
    private $returnType;
    private $parameters;
    private static function reflectCallable($target)
    {
        if ($target instanceof \Closure) {
            return new \ReflectionFunction($target);
        }
        if (\is_array($target) && isset($target[0], $target[1])) {
            return new \ReflectionMethod($target[0], $target[1]);
        }
        if (\is_object($target) && \method_exists($target, '__invoke')) {
            return new \ReflectionMethod($target, '__invoke');
        }
        $target = (string)$target;
        return \strpos($target, '::') !== false
            ? new \ReflectionMethod($target)
            : new \ReflectionFunction($target);
    }
    public static function createFromCallable($callable, $flags = ParameterType::CONTRAVARIANT | ReturnType::COVARIANT)
    {
        try {
            $reflection = self::reflectCallable($callable);
        } catch (\ReflectionException $e) {
            throw new InvalidCallbackException('Failed to reflect the supplied callable', 0, $e);
        }
        $returnType = ReturnType::createFromReflectionFunctionAbstract($reflection, $flags);
        $parameters = [];
        foreach ($reflection->getParameters() as $parameterReflection) {
            $parameters[] = ParameterType::createFromReflectionParameter($parameterReflection, $flags);
        }
        return new CallbackType($returnType, ...$parameters);
    }
    public function __construct(ReturnType $returnType, ParameterType ...$parameters)
    {
        $this->returnType = $returnType;
        $this->parameters = $parameters;
    }
    public function isSatisfiedBy($callable)
    {
        try {
            $candidate = self::reflectCallable($callable);
        } catch (\ReflectionException $e) {
            throw new InvalidCallbackException('Failed to reflect the supplied callable', 0, $e);
        }
        $byRef = $candidate->returnsReference();
        $returnType = $candidate->getReturnType();
        if ($returnType !== null) {
            $typeName = (string)$returnType;
            $nullable = $returnType->allowsNull();
        } else {
            $typeName = null;
            $nullable = false;
        }
        if (!$this->returnType->isSatisfiedBy($typeName, $nullable, $byRef)) {
            return false;
        }
        $last = null;
        foreach ($candidate->getParameters() as $position => $parameter) {
            $byRef = $parameter->isPassedByReference();
            if ($parameter->hasType()) {
                $type = $parameter->getType();
                $typeName = (string)$type;
                $nullable = $type->allowsNull();
            } else {
                $typeName = null;
                $nullable = false;
            }
            if (isset($this->parameters[$position])) {
                if (!$this->parameters[$position]->isSatisfiedBy($typeName, $nullable, $byRef)) {
                    return false;
                }
                $last = $this->parameters[$position];
                continue;
            }
            if (!$parameter->isOptional() && !$parameter->isVariadic()) {
                return false;
            }
            if ($last !== null && $last->isVariadic && !$last->isSatisfiedBy($typeName, $nullable, $byRef)) {
                return false;
            }
        }
        return true;
    }
    public function __toString()
    {
        $string = 'function ';
        if ($this->returnType->isByReference) {
            $string .= '& ';
        }
        $string .= '( ';
        for ($i = $o = 0, $l = count($this->parameters) - 1; $i < $l; $i++) {
            $string .= $this->parameters[$i];
            if (!$o && !($this->parameters[$i + 1]->isOptional)) {
                $string .= ', ';
                continue;
            }
            $string .= ' [, ';
            $o++;
        }
        if (isset($this->parameters[$l])) {
            $string .= $this->parameters[$i] . ' ';
        }
        if ($o) {
            $string .= str_repeat(']', $o) . ' ';
        }
        $string .= ')';
        if ($this->returnType->typeName !== null) {
            $string .= ' : ' . $this->returnType;
        }
        return $string;
    }}