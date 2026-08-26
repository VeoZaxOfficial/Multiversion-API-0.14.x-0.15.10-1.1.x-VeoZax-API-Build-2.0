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
use Error;use InvalidArgumentException;use OutOfBoundsException;use pocketmine\network\mcpe\CachedEncapsulatedPacket;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\NetworkSession;use pocketmine\utils\BinaryDataException;use pocketmine\utils\Utils;use ReflectionClass;use UnexpectedValueException;use function bin2hex;use function get_class;use function is_object;use function is_string;use function method_exists;
abstract class DataPacket extends NetworkBinaryStream{
	public const NETWORK_ID = 0x0;
    public const PID_MASK = 0x3ff;
    private const SUBCLIENT_ID_MASK = 0x3;
    private const SENDER_SUBCLIENT_ID_SHIFT = 0xa;
    private const RECIPIENT_SUBCLIENT_ID_SHIFT = 0xc;
	public $isEncoded = false;
	public $wasDecoded = false;
	public $__encapsulatedPacket = null;
	public $senderSubId = 0x0;
	public $recipientSubId = 0x0;
	private $packetIdToSend;
	public function pid(){
		return $this::NETWORK_ID;
	}
	public function getName() : string{
		return (new ReflectionClass($this))->getShortName();
	}
    public function checkProtocol() : void{
        if($this->protocol === null){
            throw new InvalidArgumentException('Protocol has not passed. Please use $packet->setProtocol(int $protocol)->... for fix it.');
        }
    }
	public function setPacketIdToSend(int $packetId) : void{
	    $this->packetIdToSend = $packetId;
	}
	public function getPacketIdToSend() : ?int{
	    return $this->packetIdToSend;
	}
	public function canBeBatched() : bool{
		return true;
	}
	public function canBeSentBeforeLogin() : bool{
		return false;
	}
	public function mayHaveUnreadBytes() : bool{
		return false;
	}
	public function decode(){
		$this->rewind();
		$this->checkProtocol();
		$this->decodeHeader();
		$this->decodePayload();
		$this->wasDecoded = true;
	}
	protected function decodeHeader(){
        
            $this->getByte();
            
        
	}
	public function mustBeDecoded() : bool{
		return false;
	}
	protected function decodePayload(){
	}
	public function encode(){
		$this->reset();
		$this->checkProtocol();
		$this->encodeHeader();
		$this->encodePayload();
		$this->isEncoded = true;
	}
	protected function encodeHeader(){
	    $pid = $this->packetIdToSend ?? PacketPool::getPacketIdByMagic($this->pid(), $this->protocol);
        
            $this->putByte($pid);
            
        
	}
	protected function encodePayload(){
	}
	abstract public function handle(NetworkSession $session) : bool;
	public function clean(){
		$this->buffer = "";
		$this->isEncoded = false;
		$this->offset = 0;
		return $this;
	}
	public function __debugInfo(){
		$data = [];
		foreach((array) $this as $k => $v){
			if($k === "buffer" and is_string($v)){
				$data[$k] = bin2hex($v);
			}elseif(is_string($v) or (is_object($v) and method_exists($v, "__toString"))){
				$data[$k] = Utils::printable((string) $v);
			}else{
				$data[$k] = $v;
			}
		}
		return $data;
	}
	public function __get($name){
		throw new Error("Undefined property: " . get_class($this) . "::\$" . $name);
	}
	public function __set($name, $value){
		throw new Error("Undefined property: " . get_class($this) . "::\$" . $name);
	}
}