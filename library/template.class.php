<?php
class Template {
	private static $instance;
	protected $variables = array();
	protected $_controller;
	protected $_action;
	
	
	// Combine factory + Singleton
	function factory(){
		if (!defined("TEMPLATE_BACKEND")){
			die("TEMPLATE backend is not defined");
		}
		
        if (isset(self::$instance)) {
			return self::$instance;
        }
		$backend=TEMPLATE_BACKEND;
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
			die("Template driver class file [ $backend_driver_file ] not found");
		}
	}
	
	function get($name) {
	}
	function set($name, $value) {
	}
	function render($noWrapper=0) {
	}	
	
	function __call($name, $xx){
		if (!defined("ROOT")){
			return;
		}
		$helpers_directory=ROOT . DS . "application" . DS . "helpers";
		$helper_file=$helpers_directory . DS . $name . ".php";
		//print "Loading $helper_file<br>";
		if (file_exists ($helper_file)){
			include_once($helper_file);
		}
		if (function_exists($name)){
			call_user_func_array($name,$xx);
		}
	}
}

