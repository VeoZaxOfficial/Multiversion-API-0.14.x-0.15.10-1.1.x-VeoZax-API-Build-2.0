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

namespace pocketmine\level\generator\populator;
use pocketmine\utils\Random;
abstract class VariableAmountPopulator extends Populator{
    protected $baseAmount;
    protected $randomAmount;
    protected $odd;
    public function __construct(int $baseAmount = 0, int $randomAmount = 0, int $odd = 0){
        $this->baseAmount = $baseAmount;
        $this->randomAmount = $randomAmount;
        $this->odd = $odd;
    }
    public function setOdd(int $odd){
        $this->odd = $odd;
    }
    public function checkOdd(Random $random): bool{
        if ($random->nextRange(0, $this->odd) == 0) {
            return true;
        }
        return false;
    }
    public function getAmount(Random $random){
        return $this->baseAmount + $random->nextRange(0, $this->randomAmount + 1);
    }
    public final function setBaseAmount(int $baseAmount){
        $this->baseAmount = $baseAmount;
    }
    public final function setRandomAmount(int $randomAmount){
        $this->randomAmount = $randomAmount;
    }
    public function getBaseAmount(): int{
        return $this->baseAmount;
    }
    public function getRandomAmount(): int{
        return $this->randomAmount;
    }}