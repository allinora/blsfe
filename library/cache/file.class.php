<?php
class Cache_File extends Cache {

	function __construct(){
		blsfe_load_class("BLLog");
		$this->logger=new BLLog();
	}
	
	function read($key) {
		if (!defined("CACHE_PATH")){
			return;
		}
		if (defined("DEVELOPMENT_ENVIRONMENT")){
			// Do not read cache in dev
			return;
		}
		$cache_path=CACHE_PATH . DS .  $key;
		if (file_exists($cache_path)){
			$this->logger->log("Cache:read:$key");
			return unserialize(file_get_contents($cache_path));
		}
	}
	
	function write($key, $value){
		if (!defined("CACHE_PATH")){
			return;
		}
		$cache_path=CACHE_PATH . DS .  $key;
		//print "CachePath is $cache_path<br>";
		$cache_dir=dirname($cache_path);
		//print "Cache_dir=$cache_dir<br>";
		if (!is_dir($cache_dir)){
			mkdir($cache_dir, 0777, true);
		}
		if (is_dir($cache_path)){
			// Make a warning
			return;
			
		}
		file_put_contents($cache_path, serialize($value));
		$this->logger->log("Cache:write:$key");
	}
}
