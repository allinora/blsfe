<?php

class Admin_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		//print "<pre>"  . print_r($_SESSION, true) . "</pre>";exit;
		if (!$_SESSION["user"]){
			$this->redirect("core", "login");
		}
		if (!$_SESSION["user"]["admin"]==1){
			die("ACCESS DENIED: You do not have admin access");
		}
		if ($_REQUEST["op"]=="logout"){
			$this->_logout();
		}
	}
	
	function afterAction(){
		parent::afterAction();
	}
	function _logout(){
		$_SESSION=array();
		unset($_SESSION);
		$this->redirect("core", "login");
	}
}
