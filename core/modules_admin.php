<?php

// This is not for the core but for the modules that are created within the app
// This is just the helper to do some setup
// The module Controller calles should extend this controller


include_once(dirname(__FILE__)  . "/controller.php");
class Modules_Admin_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		// Set the reference to cache
		$this->cache=$cache;
		$this->set("modules_dir", ROOT . DS . "modules");
		if (!$_SESSION["authdata"]){
			$this->redirect("core", "login");
		}
	}
	
	
	function afterAction(){
		$this->set("jslibs", $this->jsLibs);
	}
}
