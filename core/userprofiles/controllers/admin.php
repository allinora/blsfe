<?php

class Core_Userprofiles_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "users");
		$this->model=new BLModel("sys/user/profile", "user_id");
		
		// Define standard method replacements
		// $this->model->methodReplacements["get"]="getUser";
	}

	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["user_id"]["hidden"]="true";
		return $formatters;
	}

	function addAction() {
		die("Adding a profile like this is not allowed");
	}
	
	function editAction($id) {
		// Go back to the list of users instead of list of profiles
		parent::editAction($id, "core/users/admin");   
	}
}
