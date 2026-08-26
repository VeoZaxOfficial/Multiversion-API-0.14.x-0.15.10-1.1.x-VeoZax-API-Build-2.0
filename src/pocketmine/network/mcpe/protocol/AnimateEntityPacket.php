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
use pocketmine\network\mcpe\NetworkSession;use UnexpectedValueException;use function count;use function pack;use function unpack;
class AnimateEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ANIMATE_ENTITY_PACKET;
	private $animation;
	private $nextState;
	private $stopExpression;
    private $stopExpressionVersion;
	private $controller;
	private $blendOutTime;
	private $actorRuntimeIds;
	public static function create(string $animation, string $nextState, string $stopExpression, int $stopExpressionVersion, string $controller, float $blendOutTime, array $actorRuntimeIds) : self{
		$result = new self;
		$result->animation = $animation;
		$result->nextState = $nextState;
		$result->stopExpression = $stopExpression;
		$result->stopExpressionVersion = $stopExpressionVersion;
		$result->controller = $controller;
		$result->blendOutTime = $blendOutTime;
		$result->actorRuntimeIds = $actorRuntimeIds;
		return $result;
	}
	public function getAnimation() : string{ return $this->animation; }
	public function getNextState() : string{ return $this->nextState; }
	public function getStopExpression() : string{ return $this->stopExpression; }
    public function getStopExpressionVersion() : int{ return $this->stopExpressionVersion; }
	public function getController() : string{ return $this->controller; }
	public function getBlendOutTime() : float{ return $this->blendOutTime; }
	public function getActorRuntimeIds() : array{ return $this->actorRuntimeIds; }
	protected function decodePayload() : void{
		$this->animation = $this->getString();
		$this->nextState = $this->getString();
		$this->stopExpression = $this->getString();
		
		$this->controller = $this->getString();
		$this->blendOutTime = $this->getLFloat();
		$this->actorRuntimeIds = [];
		$len = $this->getUnsignedVarInt();
		if($len > 128){
			throw new UnexpectedValueException("Too many actor runtime ID in AnimateEntity: $len");
		}
		for($i = 0; $i < $len; ++$i){
			$this->actorRuntimeIds[] = $this->getEntityRuntimeId();
		}
	}
	protected function encodePayload() : void{
		$this->putString($this->animation);
		$this->putString($this->nextState);
		$this->putString($this->stopExpression);
		
		$this->putString($this->controller);
        $this->putLFloat($this->blendOutTime);
		$this->putUnsignedVarInt(count($this->actorRuntimeIds));
		foreach($this->actorRuntimeIds as $id){
			$this->putEntityRuntimeId($id);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleAnimateEntity($this);
	}}