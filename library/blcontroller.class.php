<?php

include_once(dirname(__FILE__) . "/blmodel.class.php");
include_once(dirname(__FILE__) . "/blform.class.php");
include_once(dirname(__FILE__) . "/blhelper.class.php");
include_once(dirname(__FILE__) . "/template.class.php");
class BLController {
	
	protected $_controller;
	protected $_action;
	protected $_template;
	protected $helper;

	public $doNotRenderHeader;
	public $render;

	function __construct($controller, $action) {
		
		$this->_controller = ucfirst($controller);
		$this->_action = $action;
		
		$this->doNotRenderHeader = 0;
		$this->render = 1;
		
		$templateClass= "Template";
		$this->_template =  Template::factory();
		$this->_template->init($controller,$action);
		
		$this->helper = new BLHelper();

	}
	
	/* 
	Forward from an action to another action in the same or another controller
	Examples:
	1. Forward to another action in the same controller
	   $this->forward("anotheraction");
	
	2. Forward to an action in another controller
	   $this->forward("somecontroller", "action");
	
	*/
	function forward(){
		$this->render=0;
	    $numargs = func_num_args();
		$arguments=func_get_args();
		//print $numargs;
		if ($numargs){
			if ($numargs>1){
				performAction(array_shift($arguments), array_shift($arguments), array_shift($arguments), 1);
			} else {
				performAction($this->_controller, array_shift($arguments),array_shift($arguments),10);
			}
		}
	}
	
	function redirect($controller, $action, $params=array()){
		$url="/" . LANG . "/$controller/$action";
		if (is_array($params) && count($params)){
			$url.="/?" . http_build_query($params);
		}
		if (headers_sent()){
			print "Redirecting to $url<br>";
			print "Headers already sent. Must redirect via javascript<br>";
		} else {
			header("Location: $url");exit;
		}
		
		exit;
		
		
	}
	
	public function run(&$controller, $action, $q) {
		$controller->$action($q);
		
	}
	function action() {
		return $this->_action;
	}
	
	function controller() {
		return $this->_controller;
	}
	
	function template () {
		return $this->_template;
	}

	function set($name,$value) {
		$this->_template->set($name,$value);
	}
	function get($name){
		$this->_template->get($name);
	}
	
	function display () {
		$this->_template->render($this->doNotRenderHeader);
	}
	
	/*
	function __destruct() {
		if ($this->render) {
			$this->_template->render($this->doNotRenderHeader);
		}
	}*/
	
	function beforeAction(){
	}

	function afterAction(){
	}
		
}
