<?php
class Session {
	private static $instance;
	
	// Combine factory + Singleton
	public static function factory(){
		if (!defined("SESSION_BACKEND")){
			// Let the system handle the sessions
			return;
		}
		
        if (isset(self::$instance)) {
			return self::$instance;
        }
		$backend=SESSION_BACKEND;
		$backend_driver_file=dirname(__FILE__) . DS . strtolower(__CLASS__) . DS . strtolower($backend) . '.class.php';
		if (file_exists($backend_driver_file)){
			include_once($backend_driver_file);
			$class_name= __CLASS__ . '_' . ucfirst(strtolower($backend));
			if (class_exists($class_name)){
				self::$instance=new $class_name;
				return self::$instance;
			} else {
				die($class_name . " class does not exists");
			}
		} else {
			die("Session driver class file [ $backend_driver_file ] not found");
		}
	}
}
