<?php
class Template_Smarty extends Template {
	protected $smarty;
	protected $templateFile = null;
	
	
	function init($controller,$action) {
		$this->_controller = $controller;
		$this->_action = $action;

		//automatically load the right template from the app views folder
		$ctl = ($controller == null || $controller === "") ? "" : ($this->_controller);
		$_template_file=ROOT . DS . 'application' . DS . 'views' . DS . $ctl . DS . $this->_action . '.html';
		if (file_exists($_template_file)){
			$this->templateFile = $_template_file;
		} else {
			// Just kill the processing here, no need to propogate to smarty ..
			throw new Exception("Smarty view file [ $_template_file ]not found");
		}
	}
	
	
	public function __construct () {
		if (!defined("SMARTY_LIBRARY")) {
			throw new Exception("SMARTY_LIBRARY is not defined");
		}
		include_once(SMARTY_LIBRARY);
		$this->smarty = new Smarty();
		
		//useful vars for plugins like media
		$this->smarty->assign("DEVELOPMENT_ENVIRONMENT", DEVELOPMENT_ENVIRONMENT);

		// Define the language
		if (defined("LANG")){
			$this->smarty->assign("lang", LANG);
		}

		// Supress notices.
		$this->smarty->error_reporting = error_reporting() & ~E_NOTICE; 

		// Allow space between delimeters
		$smarty->auto_literal = false;
		
		if (!defined("SMARTY_TEMPLATE_DIR")) {
			throw new Exception("SMARTY_TEMPLATE_DIR is not defined");
		}
		if (!defined("SMARTY_COMPILE_DIR")) {
			throw new Exception("SMARTY_COMPILE_DIR is not defined");
		}

		$this->smarty->template_dir = SMARTY_TEMPLATE_DIR;
		$this->smarty->compile_dir = SMARTY_COMPILE_DIR;
		$this->smarty->cache_dir = SMARTY_CACHE_DIR;
		$this->smarty->plugins_dir[] = SMARTY_LOCAL_PLUGINS_DIR;
		$this->smarty->left_delimiter = SMARTY_LEFT_DELIMETER;
		$this->smarty->right_delimiter = SMARTY_RIGHT_DELIMETER;
		
	}
	
	public function set ($key, $value) {
		$this->smarty->assign($key, $value);
	}
	
	public function render ($noWrapper=0) {
		//echo "XXX Smarty_template.render(templateFile=".$this->templateFile.")<br>\n";
		if ($noWrapper) {
			$this->smarty->display($this->templateFile);
		} else {
			$this->renderInWrapper();
		}
	}
	
	public function fetch () {
		return $this->smarty->fetch($this->templateFile);
	}
	
	private function renderInWrapper () {
		//open the generic application wrapper template
		$C = __CLASS__;
		$st = new $C();
		$st->init(null, "wrapper");
		$st->set("content", $this->fetch());
		$st->render(true);
	}

}
	
