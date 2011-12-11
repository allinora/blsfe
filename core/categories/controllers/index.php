<?php

class Core_Categories_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "categories");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "categories/admin");
	}
}
