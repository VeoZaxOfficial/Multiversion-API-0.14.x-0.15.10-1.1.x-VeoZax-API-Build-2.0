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
namespace pocketmine\network;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;use pocketmine\event\server\NetworkInterfaceUnregisterEvent;use pocketmine\network\mcpe\multiversion\actor\AvailableActorIdentifiersPalette;use pocketmine\network\mcpe\multiversion\biomes\BiomeDefinitionPalette;use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;use pocketmine\network\mcpe\multiversion\MetadataConvertor;use pocketmine\network\mcpe\multiversion\MultiversionEnums;use pocketmine\network\mcpe\protocol\PacketPool;use pocketmine\network\mcpe\protocol\types\SerializedSkin;use pocketmine\Server;use function spl_object_hash;
class Network{
	public static $BATCH_THRESHOLD = 512;
	private $server;
	private $interfaces = [];
	private $advancedInterfaces = [];
	private $upload = 0;
	private $download = 0;
	private $name;
	public function __construct(Server $server){
		PacketPool::init();
		MultiversionEnums::init();
		MetadataConvertor::init();
        ItemPalette::init();
        BiomeDefinitionPalette::init();
        AvailableActorIdentifiersPalette::init();
		SerializedSkin::init();
		$this->server = $server;
	}
	public function addStatistics($upload, $download){
		$this->upload += $upload;
		$this->download += $download;
	}
	public function getUpload(){
		return $this->upload;
	}
	public function getDownload(){
		return $this->download;
	}
	public function resetStatistics(){
		$this->upload = 0;
		$this->download = 0;
	}
	public function getInterfaces() : array{
		return $this->interfaces;
	}
	public function processInterfaces(){
		foreach($this->interfaces as $interface){
			$interface->process();
		}
	}
	public function processInterface(SourceInterface $interface) : void{
		$interface->process();
	}
	public function registerInterface(SourceInterface $interface){
		$ev = new NetworkInterfaceRegisterEvent($interface);
		$ev->call();
		if(!$ev->isCancelled()){
			$interface->start();
			$this->interfaces[$hash = spl_object_hash($interface)] = $interface;
			if($interface instanceof AdvancedSourceInterface){
				$this->advancedInterfaces[$hash] = $interface;
				$interface->setNetwork($this);
			}
			$interface->setName($this->name);
		}
	}
	public function unregisterInterface(SourceInterface $interface){
		(new NetworkInterfaceUnregisterEvent($interface))->call();
		unset($this->interfaces[$hash = spl_object_hash($interface)], $this->advancedInterfaces[$hash]);
	}
	public function setName(string $name){
		$this->name = $name;
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}
	public function getName() : string{
		return $this->name;
	}
	public function updateName(){
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}
	public function getServer() : Server{
		return $this->server;
	}
	public function sendPacket(string $address, int $port, string $payload){
		foreach($this->advancedInterfaces as $interface){
			$interface->sendRawPacket($address, $port, $payload);
		}
	}
	public function blockAddress(string $address, int $timeout = 300){
		foreach($this->advancedInterfaces as $interface){
			$interface->blockAddress($address, $timeout);
		}
	}
	public function unblockAddress(string $address){
		foreach($this->advancedInterfaces as $interface){
			$interface->unblockAddress($address);
		}
	}
}