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
namespace raklib\server;
use raklib\protocol\IncompatibleProtocolVersion;use raklib\protocol\OfflineMessage;use raklib\protocol\OpenConnectionReply1;use raklib\protocol\OpenConnectionReply2;use raklib\protocol\OpenConnectionRequest1;use raklib\protocol\OpenConnectionRequest2;use raklib\protocol\UnconnectedPing;use raklib\protocol\UnconnectedPong;use raklib\utils\InternetAddress;use function min;use function reset;use function in_array;use function implode;
class OfflineMessageHandler{
	private $sessionManager;
	public function __construct(SessionManager $manager){
		$this->sessionManager = $manager;
	}
	public function handle(OfflineMessage $packet, InternetAddress $address) : bool{
		switch($packet::$ID){
			case UnconnectedPing::$ID:
				$pk = new UnconnectedPong();
				$pk->serverID = $this->sessionManager->getID();
				$pk->pingID = $packet->pingID;
				$pk->serverName = $this->sessionManager->getName();
				$this->sessionManager->sendPacket($pk, $address);
				return true;
			case OpenConnectionRequest1::$ID:
				$serverProtocols = (array) $this->sessionManager->getProtocolVersions();
				if(!in_array($packet->protocol, $serverProtocols, true)){
					$pk = new IncompatibleProtocolVersion();
					$pk->protocolVersion = reset($serverProtocols);
					$pk->serverId = $this->sessionManager->getID();
					$this->sessionManager->sendPacket($pk, $address);
					$this->sessionManager->getLogger()->notice("Refused connection from $address due to incompatible RakNet protocol version (expected " . implode(" ", $serverProtocols) . ", got $packet->protocol)");
				}else{
					$pk = new OpenConnectionReply1();
					$pk->mtuSize = $packet->mtuSize + 28; 
					$pk->serverID = $this->sessionManager->getID();
					$this->sessionManager->sendPacket($pk, $address);
					$this->sessionManager->storeProtocol($packet->protocol, $address);
				}
				return true;
			case OpenConnectionRequest2::$ID:
				if($packet->serverAddress->port === $this->sessionManager->getPort() or !$this->sessionManager->portChecking){
					if($packet->mtuSize < Session::MIN_MTU_SIZE){
						$this->sessionManager->getLogger()->debug("Not creating session for $address due to bad MTU size $packet->mtuSize");
						return true;
					}
					$mtuSize = min($packet->mtuSize, $this->sessionManager->getMaxMtuSize()); 
					$pk = new OpenConnectionReply2();
					$pk->mtuSize = $mtuSize;
					$pk->serverID = $this->sessionManager->getID();
					$pk->clientAddress = $address;
					$this->sessionManager->sendPacket($pk, $address);
					$this->sessionManager->createSession($address, $packet->clientID, $mtuSize);
				}else{
					$this->sessionManager->getLogger()->debug("Not creating session for $address due to mismatched port, expected " . $this->sessionManager->getPort() . ", got " . $packet->serverAddress->port);
				}
				return true;
		}
		return false;
	}
}