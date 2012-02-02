<?php

class Core_Zlmlistings_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "zlmlistings");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "zlmlistings/admin");
	}
}
