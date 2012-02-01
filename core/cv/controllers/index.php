<?php

class Core_Cv_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "cv");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "cv/admin");
	}
}
