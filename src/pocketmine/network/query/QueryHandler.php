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
namespace pocketmine\network\query;
use pocketmine\network\AdvancedSourceInterface;use pocketmine\Server;use function base64_encode;use function chr;use function hash;use function ord;use function pack;use function random_bytes;use function strlen;use function substr;use function unpack;
class QueryHandler{
	private $server;
	private $lastToken;
	private $token;
	public const HANDSHAKE = 9;
	public const STATISTICS = 0;
	public function __construct(){
		$this->server = Server::getInstance();
		$this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.server.query.start"));
		$addr = $this->server->getIp();
		$port = $this->server->getPort();
		$this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.server.query.info", [$port]));
		$this->regenerateToken();
		$this->lastToken = $this->token;
		$this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.server.query.running", [$addr, $port]));
	}
	private function debug(string $message) : void{
		$this->server->getLogger()->debug("[Query] $message");
	}
	public function regenerateInfo(){
	}
	public function regenerateToken(){
		$this->lastToken = $this->token;
		$this->token = random_bytes(16);
	}
	public static function getTokenString(string $token, string $salt) : int{
		return (unpack("N", substr(hash("sha512", $salt . ":" . $token, true), 7, 4))[1] << 32 >> 32);
	}
	public function handle(AdvancedSourceInterface $interface, string $address, int $port, string $packet){
		$offset = 2;
		$packetType = ord($packet[$offset++]);
		$sessionID = (unpack("N", substr($packet, $offset, 4))[1] << 32 >> 32);
		$offset += 4;
		$payload = substr($packet, $offset);
		switch($packetType){
			case self::HANDSHAKE: 
				$reply = chr(self::HANDSHAKE);
				$reply .= (pack("N", $sessionID));
				$reply .= self::getTokenString($this->token, $address) . "\x00";
				$interface->sendRawPacket($address, $port, $reply);
				break;
			case self::STATISTICS: 
				$token = (unpack("N", substr($payload, 0, 4))[1] << 32 >> 32);
				if($token !== ($t1 = self::getTokenString($this->token, $address)) and $token !== ($t2 = self::getTokenString($this->lastToken, $address))){
					$this->debug("Bad token $token from $address $port, expected $t1 or $t2");
					break;
				}
				$reply = chr(self::STATISTICS);
				$reply .= (pack("N", $sessionID));
				if(strlen($payload) === 8){
					$reply .= $this->server->getQueryInformation()->getLongQuery();
				}else{
					$reply .= $this->server->getQueryInformation()->getShortQuery();
				}
				$interface->sendRawPacket($address, $port, $reply);
				break;
			default:
				$this->debug("Unhandled packet from $address $port: " . base64_encode($packet));
				break;
		}
	}}