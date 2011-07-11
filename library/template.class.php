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
