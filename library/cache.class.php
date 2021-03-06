<?php
class Cache {
	private static $instance;
	
	// Combine factory + Singleton
	public static function factory(){
		if (!defined("CACHE_BACKEND")){
			define('CACHE_BACKEND', 'null');
		}
		
        if (isset(self::$instance)) {
			return self::$instance;
        }
		
		$backend = CACHE_BACKEND;

		if (defined("CACHE_DISABLED") && CACHE_DISABLED){
			$backend = 'null';
		}

		
		$backend_driver_file = dirname(__FILE__) . DS . strtolower(__CLASS__) . DS . strtolower($backend) . '.class.php';
		if (file_exists($backend_driver_file)){
			include_once($backend_driver_file);
			$class_name = "Cache_" . ucfirst(strtolower($backend));
			if (class_exists($class_name)){
				self::$instance = new $class_name;
				return self::$instance;
			} else {
				die($class_name . " class does not exists");
			}
		} else {
			die("Cache driver class file not found");
		}
	}
	
}
