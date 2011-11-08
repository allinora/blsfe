<?php

class Core_Translations_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "po");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "translations/admin");
	}
}
