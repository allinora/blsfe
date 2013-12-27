<?php

class Core_Jm_AllocationsController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->model=new BLModel("sys/jm/service/allocation", "id", "service_id");
	}

	function indexAction($service_id) {
		$res = $this->model->getall($service_id);
		$this->set("aData", $res);
	}

	function showAction($id) {
		$res = $this->model->get($id);
		$this->set("aData", $res);
	}
	
	function formatters(){
		// blsfe_helper_modelList($model, $action=null, $idField, $searchField, $field_name, $id){
		$formatters=array();
		include_once(BLSFE_ROOT . "/helpers/modelList.php");

		$this->res = $this->model->get($_REQUEST['id']);

		$formatters["worker_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/worker", null , "id", null, 'worker_id', $this->res['worker_id']);
		};
		$formatters["service_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/service", null , "id", null, 'service_id', $this->res['service_id']);
		};
		
		$formatters["auto_start"]["function"] = function(){ 
			include_once(BLSFE_ROOT . "/helpers/yesno.php");
			return blsfe_helper_yesno('auto_start', $this->res['auto_start']);
		};
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/allocations");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/allocations");
	}
}
