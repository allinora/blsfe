<?php

class Core_Jm_RolesController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model=new BLModel("sys/jm/role", "id", "");
	}
	function indexAction() {
		$res = $this->model->getall(1);
		print "<pre>" . print_r($res, true) . "</pre>";
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/roles");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/roles");
	}
}
