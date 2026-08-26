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
namespace pocketmine\network\mcpe\protocol;
use InvalidStateException;
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\multiversion\CommandListJson;use pocketmine\network\mcpe\multiversion\MultiversionEnums;use pocketmine\network\mcpe\protocol\types\ChainedSubCommandData;use pocketmine\network\mcpe\protocol\types\ChainedSubCommandValue;use pocketmine\network\mcpe\protocol\types\CommandData;use pocketmine\network\mcpe\protocol\types\CommandEnum;use pocketmine\network\mcpe\protocol\types\CommandEnumConstraint;use pocketmine\network\mcpe\protocol\types\CommandOverload;use pocketmine\network\mcpe\protocol\types\CommandParameter;use pocketmine\utils\BinaryDataException;use UnexpectedValueException;use function chr;use function count;use function dechex;use function json_encode;use function ord;use function pack;use function unpack;
class AvailableCommandsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::AVAILABLE_COMMANDS_PACKET;
	public const ARG_FLAG_VALID = 0x100000;
	public const ARG_TYPE_INT = 0x01;
	public const ARG_TYPE_FLOAT = 0x03;
	public const ARG_TYPE_VALUE = 0x04;
	public const ARG_TYPE_WILDCARD_INT = 0x05;
	public const ARG_TYPE_OPERATOR = 0x06;
	public const ARG_TYPE_COMPARE_OPERATOR = 0x07;
	public const ARG_TYPE_TARGET = 0x08;
	public const ARG_TYPE_WILDCARD_TARGET = 0x0a;
	public const ARG_TYPE_FILEPATH = 0x11;
	public const ARG_TYPE_FULL_INTEGER_RANGE = 0x17;
	public const ARG_TYPE_EQUIPMENT_SLOT = 0x26;
	public const ARG_TYPE_STRING = 0x27;
	public const ARG_TYPE_INT_POSITION = 0x2f;
	public const ARG_TYPE_POSITION = 0x30;
	public const ARG_TYPE_MESSAGE = 0x33;
	public const ARG_TYPE_RAWTEXT = 0x35;
	public const ARG_TYPE_JSON = 0x39;
	public const ARG_TYPE_BLOCK_STATES = 0x43;
	public const ARG_TYPE_COMMAND = 0x46;
	public const ARG_FLAG_ENUM = 0x200000;
	public const ARG_FLAG_POSTFIX = 0x1000000;
	public const HARDCODED_ENUM_NAMES = [
		"CommandName" => true
	];
	public $commandData = [];
	public $hardcodedEnums = [];
	public $softEnums = [];
	public $enumConstraints = [];
	public $unknown = "";
	protected function decodePayload(){
	    
		    $this->commandData = $this->getString();
		    $this->unknown = $this->getString();
		
	}
	protected function getEnum(array $enumValueList) : CommandEnum{
		$retval = new CommandEnum();
		$retval->enumName = $this->getString();
		$listSize = count($enumValueList);
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$index = $this->getEnumValueIndex($listSize);
			if(!isset($enumValueList[$index])){
				throw new UnexpectedValueException("Invalid enum value index $index");
			}
			$retval->enumValues[] = $enumValueList[$index];
		}
		return $retval;
	}
	protected function getSoftEnum() : CommandEnum{
		$retval = new CommandEnum();
		$retval->enumName = $this->getString();
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$retval->enumValues[] = $this->getString();
		}
		return $retval;
	}
	protected function putEnum(CommandEnum $enum, array $enumValueMap) : void{
		$this->putString($enum->enumName);
		$this->putUnsignedVarInt(count($enum->enumValues));
		$listSize = count($enumValueMap);
		foreach($enum->enumValues as $value){
			$index = $enumValueMap[$value] ?? -1;
			if($index === -1){
				throw new InvalidStateException("Enum value '$value' not found");
			}
			$this->putEnumValueIndex($index, $listSize);
		}
	}
	protected function putSoftEnum(CommandEnum $enum) : void{
		$this->putString($enum->enumName);
		$this->putUnsignedVarInt(count($enum->enumValues));
		foreach($enum->enumValues as $value){
			$this->putString($value);
		}
	}
	protected function getEnumValueIndex(int $valueCount) : int{
		if($valueCount < 256){
			return $this->getByte();
		}elseif($valueCount < 65536){
			return $this->getLShort();
		}else{
			return $this->getLInt();
		}
	}
	protected function putEnumValueIndex(int $index, int $valueCount) : void{
		if($valueCount < 256){
            $this->putByte($index);
		}elseif($valueCount < 65536){
            $this->putLShort($index);
		}else{
            $this->putLInt($index);
		}
	}
	protected function getEnumConstraint(array $enums, array $enumValues) : CommandEnumConstraint{
		$valueIndex = $this->getLInt();
		if(!isset($enumValues[$valueIndex])){
			throw new UnexpectedValueException("Enum constraint refers to unknown enum value index $valueIndex");
		}
		$enumIndex = $this->getLInt();
		if(!isset($enums[$enumIndex])){
			throw new UnexpectedValueException("Enum constraint refers to unknown enum index $enumIndex");
		}
		$enum = $enums[$enumIndex];
		$valueOffset = array_search($enumValues[$valueIndex], $enum->enumValues, true);
		if($valueOffset === false){
			throw new UnexpectedValueException("Value \"" . $enumValues[$valueIndex] . "\" does not belong to enum \"$enum->enumName\"");
		}
		$constraintIds = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$constraintIds[] = $this->getByte();
		}
		return new CommandEnumConstraint($enum, $valueOffset, $constraintIds);
	}
	protected function putEnumConstraint(CommandEnumConstraint $constraint, array $enumIndexes, array $enumValueIndexes) : void{
        $this->putLInt($enumValueIndexes[$constraint->getAffectedValue()]);
        $this->putLInt($enumIndexes[$constraint->getEnum()->enumName]);
		$this->putUnsignedVarInt(count($constraint->getConstraints()));
		foreach($constraint->getConstraints() as $v){
            $this->putByte($v);
		}
	}
	protected function getCommandData(array $enums, array $postfixes, array $allChainedSubCommandData) : CommandData{
		$retval = new CommandData();
		$retval->commandName = $this->getString();
		$retval->commandDescription = $this->getString();
		
	    	$retval->flags = $this->getByte();
		
		$retval->permission = $this->getByte();
		$retval->aliases = $enums[$this->getLInt()] ?? null;
		
        $retval->chainedSubCommandData = $chainedSubCommandData ?? [];
		$retval->overloads = [];
		for($overloadIndex = 0, $overloadCount = $this->getUnsignedVarInt(); $overloadIndex < $overloadCount; ++$overloadIndex){
			$parameters = [];
			
			for($paramIndex = 0, $paramCount = $this->getUnsignedVarInt(); $paramIndex < $paramCount; ++$paramIndex){
				$parameter = new CommandParameter();
				$parameter->paramName = $this->getString();
				$parameter->paramType = $this->getLInt();
				$parameter->isOptional = $this->getBool();
				
				if($parameter->paramType & self::ARG_FLAG_ENUM){
					$index = ($parameter->paramType & 0xffff);
					$parameter->enum = $enums[$index] ?? null;
					if($parameter->enum === null){
						throw new UnexpectedValueException("deserializing $retval->commandName parameter $parameter->paramName: expected enum at $index, but got none");
					}
				}elseif($parameter->paramType & self::ARG_FLAG_POSTFIX){
					$index = ($parameter->paramType & 0xffff);
					$parameter->postfix = $postfixes[$index] ?? null;
					if($parameter->postfix === null){
						throw new UnexpectedValueException("deserializing $retval->commandName parameter $parameter->paramName: expected postfix at $index, but got none");
					}
				}elseif(($parameter->paramType & self::ARG_FLAG_VALID) === 0){
					throw new UnexpectedValueException("deserializing $retval->commandName parameter $parameter->paramName: Invalid parameter type 0x" . dechex($parameter->paramType));
				}
				$parameters[$paramIndex] = $parameter;
			}
            $retval->overloads[$overloadIndex] = new CommandOverload($isChaining ?? false, $parameters);
		}
		return $retval;
	}
	protected function putCommandData(CommandData $data, array $enumIndexes, array $postfixIndexes, array $chainedSubCommandDataIndexes) : void{
		$this->putString($data->commandName);
		$this->putString($data->commandDescription);
		
            $this->putByte($data->flags);
		
        $this->putByte($data->permission);
		if($data->aliases !== null){
            $this->putLInt($enumIndexes[$data->aliases->enumName] ?? -1);
		}else{
            $this->putLInt(-1);
		}
		
		$this->putUnsignedVarInt(count($data->overloads));
		foreach($data->overloads as $overload){
			
			$this->putUnsignedVarInt(count($overload->getParameters()));
			foreach($overload->getParameters() as $parameter){
				$this->putString($parameter->paramName);
				if($parameter->enum !== null){
					$type = self::ARG_FLAG_ENUM | self::ARG_FLAG_VALID | ($enumIndexes[$parameter->enum->enumName] ?? -1);
				}elseif($parameter->postfix !== null){
					$key = $postfixIndexes[$parameter->postfix] ?? -1;
					if($key === -1){
						throw new InvalidStateException("Postfix '$parameter->postfix' not in postfixes array");
					}
					$type = self::ARG_FLAG_POSTFIX | $key;
				}else{
					$type = $parameter->paramType;
                    if(($type & self::ARG_FLAG_VALID) !== 0x0){
                        $type &=~ self::ARG_FLAG_VALID;
                    }
                    $type = MultiversionEnums::getCommandArgType($type, $this->getProtocol());
                    $type |= self::ARG_FLAG_VALID;
				}
                $this->putLInt($type);
                $this->putBool($parameter->isOptional);
				
			}
		}
	}
	private function argTypeToString(int $argtype, array $postfixes) : string{
		if($argtype & self::ARG_FLAG_VALID){
			if($argtype & self::ARG_FLAG_ENUM){
				return "stringenum (" . ($argtype & 0xffff) . ")";
			}
			switch($argtype & 0xffff){
				case self::ARG_TYPE_INT:
					return "int";
				case self::ARG_TYPE_FLOAT:
					return "float";
				case self::ARG_TYPE_VALUE:
					return "mixed";
				case self::ARG_TYPE_TARGET:
					return "target";
				case self::ARG_TYPE_STRING:
					return "string";
				case self::ARG_TYPE_POSITION:
					return "xyz";
				case self::ARG_TYPE_MESSAGE:
					return "message";
				case self::ARG_TYPE_RAWTEXT:
					return "text";
				case self::ARG_TYPE_JSON:
					return "json";
				case self::ARG_TYPE_COMMAND:
					return "command";
			}
		}elseif($argtype & self::ARG_FLAG_POSTFIX){
			$postfix = $postfixes[$argtype & 0xffff];
			return "int (postfix $postfix)";
		}else{
			throw new UnexpectedValueException("Unknown arg type 0x" . dechex($argtype));
		}
		return "unknown ($argtype)";
	}
	protected function encodePayload(){
	    
	        $this->putString(json_encode(new CommandListJson($this->commandData)));
	        $this->putString($this->unknown);
	    
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleAvailableCommands($this);
	}}