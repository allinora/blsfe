<?php

class Core_Jm_JobsController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model=new BLModel("sys/jm/job", "id", "");
	}

	function indexAction() {
		$res = $this->model->getall(1);
		$this->set("aData", $res);
	}

	function showAction($id) {
		$res = $this->model->get($id);
		$this->set("aData", $res);
	}
	
	function formatters(){
		$formatters=array();
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/jobs");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/jobs");
	}
}
