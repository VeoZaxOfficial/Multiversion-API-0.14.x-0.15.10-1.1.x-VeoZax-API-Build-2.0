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

namespace FG\ASN1;
use DateInterval;use DateTime;use DateTimeZone;use Exception;
abstract class AbstractTime extends ASNObject{
    protected $value;
    public function __construct($dateTime = null, $dateTimeZone = 'UTC')
    {
        if ($dateTime == null || is_string($dateTime)) {
            $timeZone = new DateTimeZone($dateTimeZone);
            $dateTimeObject = new DateTime($dateTime, $timeZone);
            if ($dateTimeObject == false) {
                $errorMessage = $this->getLastDateTimeErrors();
                $className = Identifier::getName($this->getType());
                throw new Exception(sprintf("Could not create %s from date time string '%s': %s", $className, $dateTime, $errorMessage));
            }
            $dateTime = $dateTimeObject;
        } elseif (!$dateTime instanceof DateTime) {
            throw new Exception('Invalid first argument for some instance of AbstractTime constructor');
        }
        $this->value = $dateTime;
    }
    public function getContent()
    {
        return $this->value;
    }
    protected function getLastDateTimeErrors()
    {
        $messages = '';
        $lastErrors = DateTime::getLastErrors() ?: ['errors' => []];
        foreach ($lastErrors['errors'] as $errorMessage) {
            $messages .= "{$errorMessage}, ";
        }
        return substr($messages, 0, -2);
    }
    public function __toString()
    {
        return $this->value->format("Y-m-d\tH:i:s");
    }
    protected static function extractTimeZoneData(&$binaryData, &$offsetIndex, DateTime $dateTime)
    {
        $sign = $binaryData[$offsetIndex++];
        $timeOffsetHours   = intval(substr($binaryData, $offsetIndex, 2));
        $timeOffsetMinutes = intval(substr($binaryData, $offsetIndex + 2, 2));
        $offsetIndex += 4;
        $interval = new DateInterval("PT{$timeOffsetHours}H{$timeOffsetMinutes}M");
        if ($sign == '+') {
            $dateTime->sub($interval);
        } else {
            $dateTime->add($interval);
        }
        return $dateTime;
    }}