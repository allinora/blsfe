<?php

class Admin_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		if (!$_SESSION["authdata"]){
			$this->redirect("core", "login");
		}
	}
	
	function afterAction(){
		parent::afterAction();
	}
}
