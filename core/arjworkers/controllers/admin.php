<?php

class Core_Arjworkers_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab=$this->setTabName(__CLASS__);
		//$this->set("tab", $this->tab);
		$this->model=new BLModel("arj/worker", "id", "worker_group_id");
	}
	function indexAction() {
		$res=$this->model->getall(1);
		$this->set("aData", $res);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["worker_group_id"]["function"]=function(){ 
			include_once(BLSFE_ROOT . "/helpers/modelList.php");
			return blsfe_helper_modelList("arj/worker/group", null , "id", null, 'worker_group_id', $id);
		};
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
