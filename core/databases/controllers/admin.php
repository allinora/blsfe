<?php

class Core_Databases_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		$this->model=new BLModel("sys/auth/database/host", "id");
	}

	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function addAction($redirect=null) {
		parent::addAction("core/databases/admin");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/databases/admin");
	}
}
