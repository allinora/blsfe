<?php
class Cache_Apc extends Cache {

	function __construct(){
		blsfe_load_class("BLLog");
		$this->logger = new BLLog();
	}
	
	function read($key, $force = 0) {
		if (defined("DEVELOPMENT_ENVIRONMENT") && DEVELOPMENT_ENVIRONMENT &&!$force){
			// Do not read cache in dev
			return false;
		}

		if (defined("CACHE_DISABLED") && CACHE_DISABLED){
			// Do not read cache in certain backend environments such as extranet / admin tools
			return false;
		}
		
		if (!function_exists('apc_exists')) {
			return false;
		}
		if (apc_exists($key)) {
			$this->logger->log("Cache:read:$key");
			return apc_fetch($key);
		}
		return false;
	}
	
	function write($key, $value){
		if (defined("CACHE_DISABLED") && CACHE_DISABLED){
			// Do not read cache in certain backend environments such as extranet / admin tools
			return;
		}
		if (!function_exists('apc_store')) {
			return false;
		}
		apc_store($key, $value);
		$this->logger->log("Cache:write:$key");
	}
}
