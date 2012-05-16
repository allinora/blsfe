<?php

class Core_Eventguests_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("sys/event/guest", "id", "event_id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		return $formatters;
	}

	function addAction() {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
