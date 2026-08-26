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
define("LEVELDB_NO_COMPRESSION", 0);
define("LEVELDB_SNAPPY_COMPRESSION", 1);
define("LEVELDB_ZLIB_COMPRESSION", 2);
define("LEVELDB_ZLIB_RAW_COMPRESSION", 4);
class LevelDB{
	public function __construct($name, array $options = [
		'create_if_missing' => true, 
		'error_if_exists'   => false, 
		'paranoid_checks'   => false,
		'block_cache_size'  => 8 * (2 << 20),
		'write_buffer_size' => 4<<20,
		'block_size'        => 4096,
		'max_open_files'    => 1000,
		'block_restart_interval' => 16,
		'compression'       => LEVELDB_SNAPPY_COMPRESSION,
		'comparator'        => NULL, 
	], array $read_options = [
		'verify_check_sum'  => false, 
		'fill_cache'        => true, 
	], array $write_options = [
		'sync' => false
	]){}
	public function get($key, array $read_options = []){}
	public function set($key, $value, array $write_options = []){}
	public function put($key, $value, array $write_options = []){}
	public function delete($key, array $write_options = []){}
	public function write(LevelDBWriteBatch $batch, array $write_options = []){}
	public function getProperty($name){}
	public function getApproximateSizes($start, $limit){}
	public function compactRange($start, $limit){}
	public function close(){}
	public function getIterator(array $options = []){}
	public function getSnapshot(){}
	static public function destroy($name, array $options = []){}
	static public function repair($name, array $options = []){}}
class LevelDBIterator implements Iterator{
	public function __construct(LevelDB $db, array $read_options = []){}
	public function valid(){}
	public function rewind(){}
	public function last(){}
	public function seek($key){}
	public function next(){}
	public function prev(){}
	public function key(){}
	public function current(){}
	public function getError(){}
	public function destroy(){}
}
class LevelDBWriteBatch{
	public function __construct($name, array $options = [], array $read_options = [], array $write_options = []){}
	public function set($key, $value, array $write_options = []){}
	public function put($key, $value, array $write_options = []){}
	public function delete($key, array $write_options = []){}
	public function clear(){}}
class LevelDBSnapshot{
	public function __construct(LevelDB $db){}
	public function release(){}
}
class LevelDBException extends Exception{
}