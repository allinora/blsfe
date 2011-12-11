<?php

class Core_Subcategories_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "subcategories");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "subcategories/admin");
	}
}
