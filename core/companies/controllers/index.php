<?php

class Core_Companies_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "companies");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "companies/admin");
	}
}
