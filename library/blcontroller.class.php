<?php

include_once(dirname(__FILE__) . "/bllog.class.php");
include_once(dirname(__FILE__) . "/model.class.php");
include_once(dirname(__FILE__) . "/apimodel.class.php");
include_once(dirname(__FILE__) . "/blmodel.class.php");
include_once(dirname(__FILE__) . "/blform.class.php");
include_once(dirname(__FILE__) . "/blhelper.class.php");
include_once(dirname(__FILE__) . "/template.class.php");
include_once(dirname(__FILE__) . "/cache.class.php");
class BLController {
	
	protected $_controller;
	protected $_action;
	protected $_template;
	protected $helper;
	protected $pacakges=array();

	public $doNotRenderHeader;
	public $render;

	function __construct($controller, $action) {
		
		$this->_controller = ucfirst($controller);
		$this->_action = $action;
		
		$this->doNotRenderHeader = 0;
		$this->render = 1;
		
		$this->cache   = Cache::factory();
		$templateClass= "Template";
		$this->_template =  Template::factory();
		$this->_template->init($controller,$action);
		

		if (defined('APP_NAME') && defined('LANG')){
			$po_name = strtolower(APP_NAME);
			$po_name = strtr($po_name, '-', '_');
			include_once(dirname(__FILE__) . "/bltranslate.class.php");
	        $this->translator = new BLTranslate(LANG, $po_name);
		}
		
		$this->set("controller", $controller);
		$this->set("action", $action);
		$this->set("BLSFE_ROOT", BLSFE_ROOT);
		$this->set("BLSFE_TEMPLATES", BLSFE_ROOT . "/templates");
		
		$this->helper = new BLHelper();
		$this->logger = new BLLog();

	}

	public function translate($string){
		if (!isset($this->translator)){
			throw new Exception("Could not translate as translator object was not initilized correctly");
		}
		return $this->translator->translate($string);
	}
	
	public function useWrapper($b) {
		$this->doNotRenderHeader = ($b) ? false : true;
	}
	
	function setTemplateFile($file){
		$this->_template->setTemplateFile($file);
	}
	function setWrapper($x){
		$this->_template->setWrapper($x);
	}

	function setWrapperDir($x){
		if (!is_dir($x)){
			$x=ROOT . DS . "application" . DS . "views" . DS . $x;
		}
		$this->_template->setWrapperDir($x);
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
				$this->performAction(array_shift($arguments), array_shift($arguments), array_shift($arguments), 1);
			} else {
				$this->performAction($this->_controller, array_shift($arguments),array_shift($arguments),10);
			}
		}
	}
	/** Secondary Call Function **/
	function performAction($controller,$action,$queryString = null,$render = 0) {
		$controllerFile=ROOT . "/application/controllers/$controller" . "controller.php";
		//print "Trying to load $controllerFile<br>";
		if (file_exists($controllerFile)){
			//print "File exists<br>";
			include_once($controllerFile);
		} else {
			throw new Exception("Controller file: $controllerFile does not exists");
		}
		$controllerName = ucfirst($controller).'Controller';
		$actionName=$action . "Action"; // This is done to avoid clash with reserved function names like list();
		if (class_exists($controllerName)){
			
		} else {
			throw new Exception("ControllerClass: $controllerName does not exists");
		}

		//print "Trying to load $controllerName<br>"; 
		$dispatch = new $controllerName($controller,$action);
		$dispatch->render = $render;
		$dispatch->$actionName($queryString);

		if ($dispatch->render >0) {
				$dispatch->display();
		}

	}
	
	function redirect($controller, $action=null, $params=array()){
		$url="/" . LANG . "/$controller/$action";
		if (is_array($params) && count($params)){
			$url.="/?" . http_build_query($params);
		}
		if (headers_sent() || $params["js"] == true){
			print "Redirecting to $url<br>";
			print "Headers already sent. Must redirect via javascript<br>";
			print "<script>self.location='$url';</script>";
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
		if ($this->render) {
			$this->_template->render($this->doNotRenderHeader);
		}
	}
	function getContents() {
		return $this->_template->getContents($this->doNotRenderHeader);
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
		$this->set("packages", $this->packages);
	}
	
	function formatters(){
		return array();
	}
	function preFormatters(&$form, $res=array()){
		$formatters=$this->formatters();
		foreach($formatters as $id=>$f){
			if ($f["label"]){
				$form->setLabel($id, $f["label"]);
			}
			if ($f["value"]){
				$form->setValue($id, $f["value"]);
			}
			if ($f["css"]){
				$form->setCSS($id, $f["css"]);
			}
			if ($f["class"]){
				$form->setClass($id, $f["class"]);
			}
			if ($f["formtype"]){
				$form->setFormtype($id, $f["formtype"]);
			}
		}
	}
	function postFormatters(&$form, $res=array()){
		$formatters=$this->formatters();
		foreach($formatters as $id=>$f){
			if ($f["helper"]){
				$helper=$f["helper"];
				$form->replaceTray($id, $this->helper->$helper($res[$id]));
			}
			
			if ($f["function"]){
				$_f=$f["function"];
				$form->replaceTray($id, $_f());
			}
			if ($f["tray_class"]){
				$form->setTrayClass($id, $f["tray_class"]);
			}
			if ($f["hidden"]){
				$form->hideFromTray($id);
			}
		}
	}
	function addPackage($name){
		global $packagesConfig;
		if (isset($this->packages[$name]) && $this->packages[$name]){
			return;
		}
		if (!is_array($packagesConfig)){
			$blsfePackageConfigFile=BLSFE_ROOT . "/library/packages.php";
			if (file_exists($blsfePackageConfigFile)){
				include_once($blsfePackageConfigFile);
			}

			// Allow overridding packages in appspace
			$packageConfigFile=ROOT . "/config/packages.php";
			if (file_exists($packageConfigFile)){
				include_once($packageConfigFile);
			}
		}
		if($packagesConfig[$name]){
			$this->packages[$name]=$packagesConfig[$name];
		}
	}
	function apiCall($endpoint, $params=array()){
		if(!defined('API_BASE')){
			throw new Exception("API_BASE is not defined. Cannot call");
		}
		// Parse the url so that we can check all components
		$base_url_components = parse_url(API_BASE);

		// Concat the base path with the endpoint
		$path=$base_url_components["path"] . '/' . $endpoint;

		// Remove multiple slashes from the path which now includes the endpoint
		$path=preg_replace("@/{2,}@", '/', $path);
		
		// Rebuild the clean url
		$url=$base_url_components['scheme'] . '://' . $base_url_components['host'] . ':' . $base_url_components['port'] . $path;

		// print "Calling $url<br>";
		
		// Always add token if it exists in the session
		if ($this->getSession("token")){
			$params["token"] = $this->getSession("token");
		}
		if (isset($params['method']) && $params['method'] == 'POST'){
			return unserialize(trim($this->http_post_request($url, $params)));
		} else {
			return unserialize(trim($this->http_get_request($url, $params)));
		}
	}


	function http_post_request($url,$params,$http_params=array()) {
		return $this->http_request($url,'POST', $params, $http_params);
	}

	function http_get_request($url,$params,$http_params=array()) {
		return $this->http_request($url,'GET', $params, $http_params);
	}
	

	function http_request($url, $method, $params, $http_params=array()) {

		// Http stream options
		// See http://www.php.net/manual/en/context.http.php
	    $options = array( 
	          'http' => array( 
	            'method' => $method, 
	            'header' => "Accept-language: en\r\n"
              ) 
        ); 

		if (isset($http_params["timeout"])){
			// This can be used to set a long timeout when called from the CLI based daemon
			$options["http"]["timeout"] = $http_params["timeout"];
		}

		$url.='?';
		foreach($params as $var=>$val){
			$url.=$var . '=' . urlencode($val) . '&';
		}
	    $context = stream_context_create($options); 
		//print "<pre>" . print_r($url, true) . "</pre>";
	    $response = file_get_contents($url, false, $context);
		if (!$response) {
			return false;
		}
		
		$result = trim($response);
		return $result;
	}

	function getSession($key=null){
		if (!$key){
			if (isset($_SESSION)){
				return $_SESSION;
			}
		}
		
		if (isset($_SESSION)){
			if(isset($_SESSION[$key])){
				return $_SESSION[$key];
			}
		}
	}
	
	function setSession($key, $val){
		$_SESSION[$key]=$val;
	}
	
	function clearSession(){
		(unset)$_SESSION["token"];
		$_SESSION=array();
	}
	
	function getParam($x, $default=null){
		if(isset($_REQUEST[$x])){
			return $_REQUEST[$x];
		}
		return $default;
	}
	function sendError($type, $title, $text){
		$_content = "";
		$_content .= '<html><head><link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css"/></head>';
		$_content .= '<body>';
		$_content .= '<div class="jumbotron alert alert-' . $type . '">';
		$_content .= "<h3>$title</h3>";
		$_content .= $text;
		$_content .= "</div></body></html>";
		
		
		if (defined('APP_NAME') && defined('LANG')){
	        $data = preg_replace_callback("@<po>([^<]*)?</po>@", array($this, "translate"), $_content);
	        print $data;
			exit;
		}
		print $_content;exit;
	}
}
