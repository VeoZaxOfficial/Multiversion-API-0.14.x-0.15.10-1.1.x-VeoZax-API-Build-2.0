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

namespace Ahc\Json;
class Comment{
    protected $index   = -1;
    protected $inStr   = false;
    protected $comment = 0;
    public function strip($json)
    {
        if (!\preg_match('%\/(\/|\*)%', $json)) {
            return $json;
        }
        $this->reset();
        return $this->doStrip($json);
    }
    protected function reset()
    {
        $this->index   = -1;
        $this->inStr   = false;
        $this->comment = 0;
    }
    protected function doStrip($json)
    {
        $return = '';
        while (isset($json[++$this->index])) {
            list($prev, $char, $next) = $this->getSegments($json);
            if ($this->inStringOrCommentEnd($prev, $char, $char . $next)) {
                $return .= $char;
                continue;
            }
            $wasSingle = 1 === $this->comment;
            if ($this->hasCommentEnded($char, $char . $next) && $wasSingle) {
                $return = \rtrim($return) . $char;
            }
            $this->index += $char . $next === '*/' ? 1 : 0;
        }
        return $return;
    }
    protected function getSegments($json)
    {
        return [
            isset($json[$this->index - 1]) ? $json[$this->index - 1] : '',
            $json[$this->index],
            isset($json[$this->index + 1]) ? $json[$this->index + 1] : '',
        ];
    }
    protected function inStringOrCommentEnd($prev, $char, $charnext)
    {
        return $this->inString($char, $prev) || $this->inCommentEnd($charnext);
    }
    protected function inString($char, $prev)
    {
        if (0 === $this->comment && $char === '"' && $prev !== '\\') {
            $this->inStr = !$this->inStr;
        }
        return $this->inStr;
    }
    protected function inCommentEnd($charnext)
    {
        if (!$this->inStr && 0 === $this->comment) {
            $this->comment = $charnext === '//' ? 1 : ($charnext === '/*' ? 2 : 0);
        }
        return 0 === $this->comment;
    }
    protected function hasCommentEnded($char, $charnext)
    {
        $singleEnded = $this->comment === 1 && $char == "\n";
        $multiEnded  = $this->comment === 2 && $charnext == '*/';
        if ($singleEnded || $multiEnded) {
            $this->comment = 0;
            return true;
        }
        return false;
    }
    public function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $decoded = \json_decode($this->strip($json), $assoc, $depth, $options);
        if (\JSON_ERROR_NONE !== $err = \json_last_error()) {
            $msg = 'JSON decode failed';
            if (\function_exists('json_last_error_msg')) {
                $msg .= ': ' . \json_last_error_msg();
            }
            throw new \RuntimeException($msg, $err);
        }
        return $decoded;
    }
    public static function parse($json, $assoc = false, $depth = 512, $options = 0)
    {
        static $parser;
        if (!$parser) {
            $parser = new static;
        }
        return $parser->decode($json, $assoc, $depth, $options);
    }}