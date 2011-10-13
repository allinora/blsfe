
<?php
class Template_Php extends Template {
	protected $helper;
	function __construct() {
		$this->helper=new BLHelper();
	}
	function init($controller,$action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}
	
	function set($name,$value) {
		$this->variables[$name] = $value;
	}
	
	
	function fetch($template){
        extract($this->variables);
		ob_start();
		$template=ROOT . DS . 'application' . DS . 'views' . DS . $template . '.php';


        if (file_exists($template)) {
          include ($template);
		}
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
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
