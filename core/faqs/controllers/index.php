<?php

class Core_Faqs_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "faqs");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "faqs/admin");
	}
}
