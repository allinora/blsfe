<?php

class BLController {
	
	protected $_controller;
	protected $_action;
	protected $_template;

	public $doNotRenderHeader;
	public $render;

	function __construct($controller, $action) {
		
		$this->_controller = ucfirst($controller);
		$this->_action = $action;
		
		$this->doNotRenderHeader = 0;
		$this->render = 1;
		
		$templateClass= "Template";
		if (defined('TEMPLATE_CLASS')) {
			$templateClass = TEMPLATE_CLASS;
		}
		$this->_template =  Template::factory();
		$this->_template->init($controller,$action);

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
		//print "<pre>" . print_r($arguments,true) . "</pre>";
		if ($numargs){
			if ($numargs>1){
				return $this->__construct($arguments[0], $arguments[1]);
			} else {
				return $this->__construct($this->_controller, $arguments[0]);
			}
		}
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
