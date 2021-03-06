<?php

class Core_Permissions_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		$this->model=new BLModel("sys/permission", "id");
	}

	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function addAction($redirect=null) {
		parent::addAction("core/permissions/admin");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/permissions/admin");
	}
}
