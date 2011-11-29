<?php

// This is not for the core but for the modules that are created within the app
// This is just the helper to do some setup
// The module Controller calles should extend this controller


class Modules_Controller extends BLController {

	function beforeAction(){
		// Set the reference to cache
		$this->cache=$cache;
		$this->set("modules_dir", ROOT . DS . "modules");
	}
	
	
	function afterAction(){
		$this->set("jslibs", $this->jsLibs);
	}
}
