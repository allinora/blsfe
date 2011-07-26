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
		
		include_once(SMARTY_lib_dir);
		$this->smarty = new Smarty();
		
		//useful vars for plugins like media
		$this->smarty->assign("DEVELOPMENT_ENVIRONMENT", DEVELOPMENT_ENVIRONMENT);

		// Supress notices.
		$this->smarty->error_reporting = error_reporting() & ~E_NOTICE; 
		
		$this->smarty->template_dir = SMARTY_template_dir;
		$this->smarty->compile_dir = SMARTY_compile_dir;
		$this->smarty->cache_dir = SMARTY_cache_dir;
		$this->smarty->plugins_dir[] = SMARTY_plugins_dir;
		$this->smarty->left_delimiter = SMARTY_left_delimiter;
		$this->smarty->right_delimiter = SMARTY_right_delimiter;
		
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
	
