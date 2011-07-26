<?php

class ErrorController extends BLController {

	function beforeAction () {
		//uncomment this to show the 404 page error on a standalone page...
		//$this->doNotRenderHeader = true;
	}

	
	function e404() {
		$this->set('string',"Hello World");
		$this->set('ts',time());
	}

	function afterAction() {

	}


}
