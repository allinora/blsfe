<?php

class Core_Users_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "users");
		$this->model=new BLModel("sys/auth/user", "id");
		
		// Define standard method replacements
		$this->model->methodReplacements["get"]="getUser";

		$this->model->methodReplacements["set"]="updateUser";
	}

	function indexAction() {
		$res=$this->model->getUsersList();
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["passwd"]["hidden"]="true";
		$formatters["email"]["formtype"]="hidden_with_value";
		$formatters["active"]["formtype"]="yesno_radio";
		return $formatters;
	}

	function listAction(){
		$this->model->listMethod="getUsersList";
		//parent::listAction();
		
	}
	function addAction() {
		parent::addAction("core/users/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/users/admin");
	}
}
