<?php

class Core_Spaces_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "spaces");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "spaces/admin");
	}
}
