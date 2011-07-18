<?php
class Template {
	
	protected $variables = array();
	protected $_controller;
	protected $_action;
	
	function __construct($controller,$action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}

	/** Set Variables **/

	function set($name,$value) {
		$this->variables[$name] = $value;
	}
	function __call($name, $arguments){
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
			call_user_func($name, $arguments);
		}
		
	}
	/** Display Template **/
    function render($noWrapper = 0) {
        extract($this->variables);
		ob_start();
                if (file_exists(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php')) {
                        include (ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');
				}
		$content=ob_get_contents();
		ob_end_clean();

		if ($noWrapper){
			print $content;
			return;
		}
		include (ROOT . DS . 'application' . DS . 'views' . DS . 'wrapper.php');
		return;
    }
}
