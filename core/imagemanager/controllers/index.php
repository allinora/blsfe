<?php

class Core_Imagemanager_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "adminhome");
	}
	function indexAction() {
	}
}
