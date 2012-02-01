<?php

class Core_Zlmactions_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "zlmactions");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "zlmactions/admin");
	}
}
