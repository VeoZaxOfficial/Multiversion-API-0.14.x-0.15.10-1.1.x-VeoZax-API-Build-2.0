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
namespace DaveRandom\CallbackValidator\Test;
use DaveRandom\CallbackValidator\BuiltInTypes;use DaveRandom\CallbackValidator\Type;use PHPUnit\Framework\TestCase;
class TypeTest extends TestCase{
    private function createTypeInstance($type, $flags, $allowsCovariance, $allowsContravariance): Type
    {
        return new class($type, $flags, $allowsCovariance, $allowsContravariance) extends Type {
            public function __construct($type, $flags, $allowsCovariance, $allowsContravariance) {
                parent::__construct($type, $flags, $allowsCovariance, $allowsContravariance);
            }
        };
    }
    public function testNullType()
    {
        $type = $this->createTypeInstance(null, 0, false, false);
        $this->assertSame(null, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testNullableFlag()
    {
        $type = $this->createTypeInstance(null, Type::NULLABLE, false, false);
        $this->assertSame(null, $type->typeName);
        $this->assertTrue($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testReferenceFlag()
    {
        $type = $this->createTypeInstance(null, Type::REFERENCE, false, false);
        $this->assertSame(null, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertTrue($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testWeakFlag()
    {
        $type = $this->createTypeInstance(null, Type::WEAK, false, false);
        $this->assertSame(null, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertTrue($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testMultipleFlags()
    {
        $type = $this->createTypeInstance(null, Type::NULLABLE | Type::REFERENCE | Type::WEAK, false, false);
        $this->assertSame(null, $type->typeName);
        $this->assertTrue($type->isNullable);
        $this->assertTrue($type->isByReference);
        $this->assertTrue($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testAllowsCovarianceArg()
    {
        $type = $this->createTypeInstance(null, 0, true, false);
        $this->assertSame(null, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertTrue($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testAllowsContravarianceArg()
    {
        $type = $this->createTypeInstance(null, 0, false, true);
        $this->assertSame(null, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertTrue($type->allowsContravariance);
    }
    public function testStringTypeName()
    {
        $type = $this->createTypeInstance(Type::class, 0, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testNonStringTypeName()
    {
        $type = $this->createTypeInstance(1, 0, false, false);
        $this->assertSame('1', $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isByReference);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->allowsCovariance);
        $this->assertFalse($type->allowsContravariance);
    }
    public function testByRefMustBeIdentical()
    {
        $type = $this->createTypeInstance(Type::class, 0, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertFalse($type->isByReference);
        $this->assertTrue($type->isSatisfiedBy(Type::class, false, false));
        $this->assertFalse($type->isSatisfiedBy(Type::class, false, true));
        $type = $this->createTypeInstance(Type::class, Type::REFERENCE, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertTrue($type->isByReference);
        $this->assertTrue($type->isSatisfiedBy(Type::class, false, true));
        $this->assertFalse($type->isSatisfiedBy(Type::class, false, false));
    }
    public function testIdenticalTypesAlwaysMatch()
    {
        $type = $this->createTypeInstance(Type::class, 0, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertTrue($type->isSatisfiedBy(Type::class, false, false));
        $type = $this->createTypeInstance(Type::class, Type::NULLABLE, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertTrue($type->isNullable);
        $this->assertTrue($type->isSatisfiedBy(Type::class, true, false));
    }
    public function testValidCovariantTypeMatches()
    {
        $type = $this->createTypeInstance(Type::class, Type::NULLABLE, true, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertTrue($type->isNullable);
        $this->assertTrue($type->isSatisfiedBy(Type::class, false, false));
    }
    public function testInvalidCovariantTypeDoesNotMatch()
    {
        $type = $this->createTypeInstance(Type::class, 0, true, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isSatisfiedBy(Type::class, true, false));
    }
    public function testValidCovariantTypeMatchesDoesNotMatchWhenCovarianceIsNotAllowed()
    {
        $type = $this->createTypeInstance(Type::class, Type::NULLABLE, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertTrue($type->isNullable);
        $this->assertFalse($type->isSatisfiedBy(Type::class, false, false));
    }
    public function testCovarianceRespectsWeakMode()
    {
        $type = $this->createTypeInstance(BuiltInTypes::STRING, 0, true, false);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
        $type = $this->createTypeInstance(BuiltInTypes::STRING, Type::WEAK, true, false);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertTrue($type->isWeak);
        $this->assertTrue($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
    }
    public function testValidContravariantTypeMatches()
    {
        $type = $this->createTypeInstance(Type::class, 0, false, true);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertTrue($type->isSatisfiedBy(Type::class, true, false));
    }
    public function testInvalidContravariantTypeDoesNotMatch()
    {
        $type = $this->createTypeInstance(Type::class, Type::NULLABLE, false, true);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertTrue($type->isNullable);
        $this->assertFalse($type->isSatisfiedBy(Type::class, false, false));
    }
    public function testValidContravariantTypeDoesNotMatchWhenContravarianceIsNotAllowed()
    {
        $type = $this->createTypeInstance(Type::class, 0, false, false);
        $this->assertSame(Type::class, $type->typeName);
        $this->assertFalse($type->isNullable);
        $this->assertFalse($type->isSatisfiedBy(Type::class, true, false));
    }
    public function testContravarianceRespectsWeakMode()
    {
        $type = $this->createTypeInstance(BuiltInTypes::STRING, 0, false, true);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
        $type = $this->createTypeInstance(BuiltInTypes::STRING, Type::WEAK, false, true);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertTrue($type->isWeak);
        $this->assertTrue($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
    }
    public function testInvarianceRespectsWeakMode()
    {
        $type = $this->createTypeInstance(BuiltInTypes::STRING, 0, false, false);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertFalse($type->isWeak);
        $this->assertFalse($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
        $type = $this->createTypeInstance(BuiltInTypes::STRING, Type::WEAK, false, false);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertTrue($type->isWeak);
        $this->assertTrue($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
        $type = $this->createTypeInstance(BuiltInTypes::STRING, Type::WEAK | Type::NULLABLE, false, false);
        $this->assertSame(BuiltInTypes::STRING, $type->typeName);
        $this->assertTrue($type->isWeak);
        $this->assertFalse($type->isSatisfiedBy(BuiltInTypes::INT, false, false));
    }}