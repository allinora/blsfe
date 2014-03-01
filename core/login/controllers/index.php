<?php

class Core_Login_Controller extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->model=new BLModel("sys/auth/admin", "id");
		
	}
	function indexAction() {
		$this->set("noMenu", 1);
		$this->doNotRenderHeader=0;
		
		$_wrapper_directory = __DIR__ . "/../views";
		$this->setWrapperDir($_wrapper_directory);
		
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$x=$this->model->login(array("login"=>$_REQUEST["login"], "passwd"=>$_REQUEST["passwd"]));
			if ($x["id"]>0 && $x["active"]>0){
				$_SESSION["user"]=$x;
				$this->redirect("core", "admin");
			}
			/*
			print "<pre>REQUEST: " . print_r($_REQUEST, true) . "</pre>";
			print "<pre>RESPONSE: " . print_r($x, true) . "</pre>";
			print "<pre>SESSION: " . print_r($_SESSION, true) . "</pre>";
			exit;
			*/
		}
		
	}
}
