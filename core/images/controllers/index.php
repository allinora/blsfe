<?php

class Core_Images_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "Images");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "images/admin/");
	}
}
