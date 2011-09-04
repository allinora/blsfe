<?php

class IndexController extends BLController {

	function beforeAction () {
		print "I am " .  __FUNCTION__ . " and I run before the main action<br>";
	}

	
	function indexAction() {
		print "I am " .  __FUNCTION__ . " and I am the main action<br>";
		$this->set('string',"Hello World");
	}

	function afterAction() {
		print "I am " .  __FUNCTION__ . " and I run after the main action<br>";

	}


}
