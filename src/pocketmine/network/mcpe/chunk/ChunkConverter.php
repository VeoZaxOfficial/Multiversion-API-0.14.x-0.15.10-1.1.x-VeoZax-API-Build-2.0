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
namespace pocketmine\network\mcpe\chunk;
use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\level\format\Chunk;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\world\format\PalettedBlockArray;use function str_repeat;use function ord;use function chr;
final class ChunkConverter{
    public static function convertSubChunkFromPaletteXZY(PalettedBlockArray $palettedBlockArray, int $protocol) : array{
		$blockIdArray = "";
		$blockDataArray = "";
		$legacyCache = [];
		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){
			    for($y = 0; $y < 16; ++$y){
				    $fullBlock = $palettedBlockArray->get($x, $y, $z);
				    $cached = $legacyCache[$fullBlock] ?? null;
				    if($cached === null){
					    $block = BlockFactory::get($fullBlock >> Block::INTERNAL_METADATA_BITS, $fullBlock & Block::INTERNAL_METADATA_MASK);
						$block = $block->getBlockProtocol($protocol) ?? $block;
						[$legacyId, $legacyMeta] = [$block->getId(), $block->getDamage()];
						if($legacyId > 255){
							$legacyId = 248; 
							$legacyMeta = 0;
						}
						$cached = [$legacyId, $legacyMeta];
						$legacyCache[$fullBlock] = $cached;
					}
					[$legacyId, $legacyMeta] = $cached;
					$blockIdArray[($x << 8) | ($z << 4) | $y] = chr($legacyId);
					$indexData = ($x << 7) | ($z << 3) | ($y >> 1);
					if(($y & 1) === 0){
							  $blockDataArray[$indexData] = chr((ord($blockDataArray[$indexData] ?? chr(0)) & 0xf0) | ($legacyMeta & 0x0f));
					}else{
	    	            $blockDataArray[$indexData] = chr((($legacyMeta & 0x0f) << 4) | (ord($blockDataArray[$indexData] ?? chr(0)) & 0x0f));
					}
				}
			}
		}
		return [$blockIdArray, $blockDataArray];
    }
    private static array $legacyCacheByProtocol = [];
    public static function convertSubChunkFromPaletteColumn(array $palettedBlocks, int $protocol) : array{
        $idsArr = array_fill(0, 32768, "\x00");
        $dataArr = array_fill(0, 16384, "\x00");
        if(!isset(self::$legacyCacheByProtocol[$protocol])){
            self::$legacyCacheByProtocol[$protocol] = [];
        }
        $legacyCache =& self::$legacyCacheByProtocol[$protocol];
        $yOffset = 0;
        foreach($palettedBlocks as $palettedBlockArray){
            for($x = 0; $x < 16; ++$x){
                for($z = 0; $z < 16; ++$z){
                    for($y = 0; $y < 16; ++$y){
                        $yy = ($yOffset << 4) | $y;
                        $idx = ($x << 11) | ($z << 7) | $yy;
                        $dataIdx = $idx >> 1;
				        $fullBlock = $palettedBlockArray->get($x, $y, $z);
				        $cached = $legacyCache[$fullBlock] ?? null;
				        if($cached === null){
					    	$block = BlockFactory::get($fullBlock >> Block::INTERNAL_METADATA_BITS, $fullBlock & Block::INTERNAL_METADATA_MASK);
					    	$block = $block->getBlockProtocol($protocol) ?? $block;
					    	[$legacyId, $legacyMeta] = [$block->getId(), $block->getDamage()];
					    	if($legacyId > 255){
						    	$legacyId = 248; 
						    	$legacyMeta = 0;
					    	}
					    	$cached = [chr($legacyId), $legacyMeta];
					    	$legacyCache[$fullBlock] = $cached;
				        }
				        [$idChr, $legacyMeta] = $cached;
                        $idsArr[$idx] = $idChr;
                        $current = ord($dataArr[$dataIdx]);
                        if(($yy & 1) === 0){
                            $current = ($current & 0xf0) | $legacyMeta;
                        }else{
                            $current = ($current & 0x0f) | ($legacyMeta << 4);
                        }
                        $dataArr[$dataIdx] = chr($current);
                    }
                }
            }
            $yOffset++;
        }
        return [implode('', $idsArr), implode('', $dataArr)];
    }
    public static function buildLegacyChunkPayload(Chunk $chunk) : string{
        $palettedBlocks = [];
        for($y = 0; $y < 8; ++$y){
            $subChunk = $chunk->getSubChunk($y);
            $layers = $subChunk->getBlockLayers();
            $palettedBlocks[] = $layers[0] ?? new PalettedBlockArray(Block::AIR);
        }
        [$idArray, $dataArray] = self::convertSubChunkFromPaletteColumn($palettedBlocks, ProtocolInfo::PROTOCOL_81);
        $skySlots = array_fill(0, 2048, "\xff\xff\xff\xff\xff\xff\xff\xff");
        $lightSlots = array_fill(0, 2048, "\x00\x00\x00\x00\x00\x00\x00\x00");
        for($y = 0; $y < 8; ++$y){
            $subChunk = $chunk->getSubChunk($y);
            if($subChunk->isEmpty(false)){
                continue;
            }
            $subSky = $subChunk->getBlockSkyLightArray();
            $subLight = $subChunk->getBlockLightArray();
            for($bx = 0; $bx < 16; ++$bx){
                $slotXBase = $bx << 7;
                $srcColBase = $bx << 7;
                for($bz = 0; $bz < 16; ++$bz){
                    $srcOffset = $srcColBase | ($bz << 3);
                    $slot = $slotXBase | ($bz << 3) | $y;
                    $skySlots[$slot] = substr($subSky, $srcOffset, 8);
                    $lightSlots[$slot] = substr($subLight, $srcOffset, 8);
                }
            }
        }
        $skyLightArray = implode('', $skySlots);
        $lightArray = implode('', $lightSlots);
        $chunkData = $idArray . $dataArray . $skyLightArray . $lightArray;
        $heightMap = $chunk->getHeightMapArray();
        $heightMapBytes = array_map(fn($v) => max(0, min(255, $v)), $heightMap);
        $chunkData .= pack("C*", ...$heightMapBytes);
        $chunkData .= $chunk->convertChunkBiomeColorsFromBiomeIds();
        $chunkData .= pack("V", 0); 
        return $chunkData;
    }
    public static function unreorderNibbleArray(string $reordered, string $commonValue = "\x00") : string {
        $result = str_repeat($commonValue, 2048);
        if($reordered !== $result){
            $i = 0;
            for($x = 0; $x < 8; ++$x){
                for($z = 0; $z < 16; ++$z){
                    $zx = (($z << 3) | $x);
                    for($y = 0; $y < 8; ++$y){
                        $j = (($y << 8) | $zx);
                        $j80 = ($j | 0x80);
                        if($reordered[$i] === $commonValue && $reordered[$i | 0x80] === $commonValue){
                        }else{
                            $byte1 = ord($reordered[$i]);
                            $byte2 = ord($reordered[$i | 0x80]);
                            $result[$j]   = chr($byte1 & 0x0f | (($byte2 & 0x0f) << 4));
                            $result[$j80] = chr(($byte1 >> 4) | ($byte2 & 0xf0));
                        }
                        $i++;
                    }
                }
                $i += 128;
            }
        }
        return $result;
    }}