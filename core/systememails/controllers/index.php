<?php

class Core_Systememails_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "pages");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "systememails/admin");
	}
}
