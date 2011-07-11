<?php

class IndexController extends BLController {

	function beforeAction () {
	}

	
	function index() {
		$this->set('string',"Hello World");
	}

	function afterAction() {

	}


}
