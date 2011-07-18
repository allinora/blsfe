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
		
		$this->_template = new $templateClass($controller,$action);

	}
	
	function template () {
		return $this->_template;
	}

	function set($name,$value) {
		$this->_template->set($name,$value);
	}

	function __destruct() {
		if ($this->render) {
			$this->_template->render($this->doNotRenderHeader);
		}
	}
	
	function beforeAction(){
	}

	function afterAction(){
	}
		
}
