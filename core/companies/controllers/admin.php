<?php
class Core_Companies_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "companies");
		$this->model=new BLModel("sys/company", "id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	
	function formatters(){
		$formatters = array();
		return $formatters;
	}

	function addAction($redirect) {
		parent::addAction("core/companies/admin");
	}
	
	function editAction($id, $redirect="", $formatters = array()) {
		parent::editAction($id, "core/companies/admin");
	}
}
