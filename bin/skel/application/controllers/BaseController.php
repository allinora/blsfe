<?php

class BaseController extends App_Controller {

	private function checkSession(){
		// Setup the session etc here
        if ($_SESSION["loginFB"]){
			parent::facebooklogin();
		}
		if ($_REQUEST["op"]=="logout"){
			parent::logout();
		}
		if ($_REQUEST["op"]=="login"){
			parent::login();
		}
	}
	function beforeAction () {
		parent::beforeAction();
		$this->addPackage("core");
		$this->checkSession();
	}

	function indexAction() {
	}

	function afterAction() {
		parent::afterAction();
	}
}
