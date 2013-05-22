<?php

class Core_Permissions_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "users");
		$this->model=new BLModel("sys/permission", "id");
	}

	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function addAction() {
		parent::addAction("core/permissions/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/permissions/admin");
	}
}
