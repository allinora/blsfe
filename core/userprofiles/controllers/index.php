<?php

class Core_UserProfiles_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "users");
	}
	function indexAction() {
		$this->render=0;
		$this->redirect("core", "userprofiles/admin");
	}
}
