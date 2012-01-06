<?php

class Core_Docs_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "docs");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "docs/admin");
	}
}
