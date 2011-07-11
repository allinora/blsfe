<?php
class Cache_File extends Cache {

	function __construct(){
		print "Loading Cache_File class<br>";
	}
	function read($key) {
		if (!defined("CACHE_PATH")){
			return;
		}
		$cache_path=CACHE_PATH . DS .  $key;
		if (file_exists($cache_path)){
			return unserialize(file_get_contents($cache_path));
		}
	}
	
	function write($key, $value){
		if (!defined("CACHE_PATH")){
			return;
		}
		$cache_path=CACHE_PATH . DS .  $key;
		$cache_dir=dirname($cache_path);
		if (!is_dir($cache_dir)){
			mkdir($cache_dir, 0777, true);
		}
		file_put_contents($cache_path, serialize($value));
	}
}
