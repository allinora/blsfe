<?php
include_once(dirname(__FILE__) . "/blhelper.class.php");

class Template {
	private static $instance;
	protected $variables = array();
	protected $_controller;
	protected $_action;
	protected $_wrapper="wrapper";
	protected $_wrapperDir;
	
	
	// Combine factory + Singleton
	public static function factory(){
		if (!defined("TEMPLATE_BACKEND")){
			define('TEMPLATE_BACKEND', 'Smarty'); // Default use smarty
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
	function getContents($noWrapper=0) {
	}	
	function setWrapper($wrapper){
		$this->_wrapper=$wrapper;
	}
	function setWrapperDir($x){
		$this->_wrapperDir=$x;
	}
	function __call($name, $params){
		// Make a warning.
		print "Please do not use \$this->_template->function any more. Use \$this->helper->fuction instead<br><hr>";
		// Create the helper instance
		$helper = new BLHelper();
		return $helper->$name($params);
	}
}

