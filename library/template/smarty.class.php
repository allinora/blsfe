<?php
class Template_Smarty extends Template {
	protected $smarty;
	protected $templateFile = null;
	
	protected $helper;
	
	private function moduleinit($controller, $action){
		$this->_controller = $controller;
		$this->_action = $action;
		$path_tokens=split("_", strtolower($controller));
		array_shift($path_tokens); // remove the core
		$module=array_shift($path_tokens); // get the module name
		$c=array_shift($path_tokens); // get the controller name
		
		$_template_file=ROOT . DS . 'modules' .DS . $module . DS . "views" . DS . $c . DS .  $this->_action . '.html';
		if (file_exists($_template_file)){
			$this->templateFile = $_template_file;
		} else {
		}
	}

	private function coreinit($controller, $action){
		$this->_controller = $controller;
		$this->_action = $action;
		$path_tokens=explode("_", strtolower($controller));
		array_shift($path_tokens); // remove the core
		$module=array_shift($path_tokens); // get the module name
		$c=array_shift($path_tokens); // get the controller name
		
		
		
		$_template_file = BLSFE_ROOT . DS . 'core' .DS . $module . DS . "views" . DS . $c . DS .  $this->_action . '.html';
		if (file_exists($_template_file)){
			$this->templateFile = $_template_file;
		} else {
			// Just kill the processing here, no need to propogate to smarty ..
			// throw new Exception("Smarty view file [ $_template_file ]not found");
		}
	}

	function init($controller,$action) {

		if (strtolower(substr($controller, 0,7))=="modules"){
			return $this->moduleinit($controller,$action);
			
		}
		if (strtolower(substr($controller, 0,4))=="core"){
			return $this->coreinit($controller,$action);
			
		}
		$this->_controller = $controller;
		$this->_action = $action;

		//automatically load the right template from the app views folder
		$ctl = ($controller == null || $controller === "") ? "" : ($this->_controller);
		$_template_file=ROOT . DS . 'application' . DS . 'views' . DS . $ctl . DS . $this->_action . '.html';
		//print "trying to load $_template_file<br>";
		if (file_exists($_template_file)){
			$this->templateFile = $_template_file;
		} else {
			// Just kill the processing here, no need to propogate to smarty ..
			// throw new Exception("Smarty view file [ $_template_file ]not found");
		}
	}
	
	
	public function __construct () {
		if (!defined("SMARTY_LIBRARY")) {
			define("SMARTY_LIBRARY", BLSFE_ROOT . "/library/3rdparty/smarty/Smarty-3.0.8/libs/Smarty.class.php");
		}
		
		$this->helper=new BLHelper();
		
		if (!defined("SMARTY_TEMPLATE_DIR")) {
			// Default template directory 
			define('SMARTY_TEMPLATE_DIR', ROOT.DS."application".DS."views");
		}
		if (!defined("SMARTY_COMPILE_DIR")) {
			//define('SMARTY_COMPILE_DIR', ROOT.DS."tmp".DS."smarty_compile");
			define('SMARTY_COMPILE_DIR', sys_get_temp_dir()  . "/smartycompile/" . basename(ROOT));
			if (!is_dir(SMARTY_COMPILE_DIR)){
				try {
					mkdir(SMARTY_COMPILE_DIR, 0777, true);
				} catch (\Exception $e) {
					throw new Exception($e->getMessage());
				}
			}
			
		}
		if (!defined("SMARTY_CACHE_DIR")) {
			//define('SMARTY_CACHE_DIR', ROOT.DS."tmp".DS."smarty_cache");
			define('SMARTY_CACHE_DIR', sys_get_temp_dir() . "/smartycache/" . basename(ROOT));
			if (!is_dir(SMARTY_CACHE_DIR)){
				try {
					mkdir(SMARTY_CACHE_DIR, 0777, true);
				} catch (\Exception $e) {
					throw new Exception($e->getMessage());
				}
				
			}
		}
		if(!defined("SMARTY_LEFT_DELIMETER")){
			define('SMARTY_LEFT_DELIMETER', "<{");
		}
		if(!defined("SMARTY_RIGHT_DELIMETER")){
			define('SMARTY_RIGHT_DELIMETER', "}>");
		}
		
		// Framework smarty plugins directory. This will be added to the list of smarty plugins
		define('SMARTY_BLSFE_PLUGINS_DIR', BLSFE_ROOT . DS . "helpers" .DS ."smarty-plugins");

		// YOUR smarty plugins directory. This will be added to the list of smarty plugins
		define('SMARTY_LOCAL_PLUGINS_DIR', ROOT.DS."application" .DS. "helpers".DS."smarty-plugins");
	
		
		include_once(SMARTY_LIBRARY);
		$this->smarty = new Smarty();
		
		//useful vars for plugins like media
		if (isset($_ENV['DEVELOPMENT_ENVIRONMENT'])){
			$this->smarty->assign("DEVELOPMENT_ENVIRONMENT", $_ENV['DEVELOPMENT_ENVIRONMENT']);
		} else {
			$this->smarty->assign("DEVELOPMENT_ENVIRONMENT", FALSE);
		}
		
		

		// Define the language
		if (defined("LANG")){
			$this->smarty->assign("lang", LANG);
		}

		// Supress notices.
		$this->smarty->error_reporting = error_reporting() & ~E_NOTICE; 

		// Allow space between delimeters
		$this->smarty->auto_literal = false;
		
		





		$this->smarty->template_dir 	= SMARTY_TEMPLATE_DIR;
		$this->smarty->compile_dir 		= SMARTY_COMPILE_DIR;
		$this->smarty->cache_dir 		= SMARTY_CACHE_DIR;
		$this->smarty->plugins_dir[] 	= SMARTY_BLSFE_PLUGINS_DIR;
		$this->smarty->plugins_dir[] 	= SMARTY_LOCAL_PLUGINS_DIR;
		$this->smarty->left_delimiter 	= SMARTY_LEFT_DELIMETER;
		$this->smarty->right_delimiter 	= SMARTY_RIGHT_DELIMETER;

		if (isset($_ENV['DEVELOPMENT_ENVIRONMENT']) && $_ENV['DEVELOPMENT_ENVIRONMENT'] === true) {
			$this->smarty->caching = 0;
			$this->smarty->compile_check = true;
		} else {
			$this->smarty->caching = 0;
			$this->smarty->compile_check = false;
		}		
	}
	
	function setTemplateFile($file){
		$this->templateFile=SMARTY_TEMPLATE_DIR . DS . $file;
	}
	
	public function set ($key, $value) {
		$this->smarty->assign($key, $value);
	}
	
	public function getContents($noWrapper=0){
		//echo "XXX Smarty_template.render(noWrapper=$noWrapper, templateFile=".$this->templateFile.")<br>\n";
		$res="";
		$content=$this->smarty->fetch($this->templateFile);
		if ($noWrapper) {
			$res=$content;
		} else {
			if ($this->_wrapperDir){
				$wrapper=$this->_wrapperDir . "/" . $this->_wrapper  . ".html";
			} else {
				$wrapper=$this->smarty->template_dir . "/" . $this->_wrapper  . ".html";
			}
			$this->smarty->clearCache($wrapper);
			$this->set("content", $content);
			$res=$this->smarty->fetch($wrapper);
			//$this->renderInWrapper();
		}
		return $res;
		
	}
	
	public function render ($noWrapper=0) {
		$res=$this->getContents($noWrapper);
		print $res;
	}
	
	public function fetch () {
		return $this->smarty->fetch($this->templateFile);
	}
	
	private function renderInWrapper () {
		// Not using this any more. Was not propogating smarty vars to the master template
		//open the generic application wrapper template
		/*
		$C = __CLASS__;
		$st = new $C();
		$st->init(null, "wrapper");
		$st->set("content", $this->fetch());
		$st->render(true);
		*/
	}

}
	
