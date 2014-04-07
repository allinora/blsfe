<?php

class Core_Databases_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "databases");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "databases/admin");
	}
}
