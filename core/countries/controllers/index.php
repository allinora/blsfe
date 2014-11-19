<?php

class Core_Countries_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "countries");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "countries/admin");
	}
}
