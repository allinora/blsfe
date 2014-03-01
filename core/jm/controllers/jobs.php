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
		$this->set("title", $res['name']);
		$linkModel = new BLModel("sys/jm/job/role/link", "id");
		$aRoles = $linkModel->getRoles(['id' => $id]);
		$this->set("aRoles", $aRoles);
		
	}
	
	function formatters(){
		$formatters=array();
		include_once(BLSFE_ROOT . "/helpers/selectList.php");
		include_once(BLSFE_ROOT . "/helpers/modelList.php");
		
		$formatters["status"]["function"] = function(){ 
			return blsfe_helper_selectList("status", array("new", "idle", "active", "completed", "disabled", "deleted"),  $this->res['status']);
		};
		
		$formatters["dispatch_type"]["function"] = function(){ 
			return blsfe_helper_selectList("dispatch_type", array("any", "single", "all"),  $this->res['dispatch_type']);
		};
		
		$formatters["preferred_worker_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/worker", null , "id", null, 'preferred_worker_id', $this->res['preferred_worker_id']);
		};
		
		$formatters["preferred_role_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/role", null , "id", null, 'preferred_role_id', $this->res['preferred_role_id']);
		};
		
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/jobs");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/jobs");
	}
}
