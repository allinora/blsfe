<?php

class Core_Admin_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "adminhome");
	}
	function indexAction() {
	}
}
