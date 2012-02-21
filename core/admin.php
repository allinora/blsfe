<?php

class Admin_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		if (!$_SESSION["authdata"]){
			$this->redirect("core", "login");
		}
		if (!$_SESSION["authdata"]["admin"]==1){
			unset($_SESSION["authdata"]);
			die("ACCESS DENIED: You do not have admin access");
			
		}
	}
	
	function afterAction(){
		parent::afterAction();
	}
}
