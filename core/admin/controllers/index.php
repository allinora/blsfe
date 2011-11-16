<?php

class Core_Admin_Controller extends Admin_Controller {
	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "adminhome");
	}
	function indexAction() {
	}
}
