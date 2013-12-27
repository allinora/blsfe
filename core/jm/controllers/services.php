<?php

class Core_Jm_ServicesController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model=new BLModel("sys/jm/service", "id", "");
	}

	function indexAction() {
		$res = $this->model->getall(1);
		$this->set("aData", $res);
	}

	function showAction($id) {
		$res = $this->model->get($id);
		$this->set("aData", $res);
		
		$model=new BLModel("sys/jm/service", "id");
		$allocations = $model->getAllocations(['id' => $id]);
		$this->set("allocations", $allocations);
		
	}
	
	function formatters(){
		$formatters=array();
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/services");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/services");
	}
}
