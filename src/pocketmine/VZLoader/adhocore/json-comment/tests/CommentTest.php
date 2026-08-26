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

namespace Ahc\Json\Test;
use Ahc\Json\Comment;use PHPUnit\Framework\TestCase;
class CommentTest extends TestCase{
    public function testStrip($json, $expect)
    {
        $this->assertSame($expect, (new Comment)->strip($json));
    }
    public function testDecode($json)
    {
        $actual = (new Comment)->decode($json, true);
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertNotEmpty($actual);
        $this->assertArrayHasKey('a', $actual);
        $this->assertArrayHasKey('b', $actual);
    }
    public function testDecodeThrows()
    {
        (new Comment)->decode('{"a":1, /* comment */, "b":}', true);
    }
    public function testParse()
    {
        $parsed = Comment::parse('{
            // comment
            "a//b":"/*value*/"
            /* also comment */
        }', true);
        $this->assertNotEmpty($parsed);
        $this->assertInternalType('array', $parsed);
        $this->assertArrayHasKey('a//b', $parsed);
        $this->assertSame('/*value*/', $parsed['a//b']);
    }
    public function theTests()
    {
        return [
            'without comment' => [
                'json'   => '{"a":1,"b":2}',
                'expect' => '{"a":1,"b":2}',
            ],
            'single line comment' => [
                'json'   => '{"a":1,
                // comment
                "b":2,
                // comment
                "c":3}',
                'expect' => '{"a":1,
                "b":2,
                "c":3}',
            ],
            'single line comment at end' => [
                'json'   => '{"a":1,
                "b":2,// comment
                "c":3}',
                'expect' => '{"a":1,
                "b":2,
                "c":3}',
            ],
            'real multiline comment' => [
                'json'   => '{"a":1,
                /*
                 * comment
                 */
                "b":2, "c":3}',
                'expect' => '{"a":1,
                ' . '
                "b":2, "c":3}',
            ],
            'inline multiline comment' => [
                'json'   => '{"a":1,
                /* comment */ "b":2, "c":3}',
                'expect' => '{"a":1,
                 "b":2, "c":3}',
            ],
            'inline multiline comment at end' => [
                'json'   => '{"a":1, "b":2, "c":3/* comment */}',
                'expect' => '{"a":1, "b":2, "c":3}',
            ],
            'comment inside string' => [
                'json'   => '{"a": "a//b", "b":"a/* not really comment */b"}',
                'expect' => '{"a": "a//b", "b":"a/* not really comment */b"}',
            ],
            'escaped string' => [
                'json'   => '{"a": "a//b", "b":"a/* \"not really comment\" */b"}',
                'expect' => '{"a": "a//b", "b":"a/* \"not really comment\" */b"}',
            ],
            'string inside comment' => [
                'json'   => '{"a": "ab", /* also comment */ "b":"a/* not a comment */b" /* "comment string" */ }',
                'expect' => '{"a": "ab",  "b":"a/* not a comment */b"  }',
            ],
        ];
    }}