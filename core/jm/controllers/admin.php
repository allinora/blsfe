<?php

class Core_Jm_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model=new BLModel("sys/jm/role", "id", "");
	}
	function indexAction() {
	}
	

	function formatters(){
		$formatters=array();
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
