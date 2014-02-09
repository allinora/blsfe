<?php

class Core_Jm_Worker2ServicesController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model = new BLModel("sys/jm/service/allocation", "id", "service_id");
	}

	function indexAction($service_id) {
		$this->redirect("core/jm/services/show/" . $service_id);
	}

	function formatters(){
		// blsfe_helper_modelList($model, $action=null, $idField, $searchField, $field_name, $id){
		$formatters=array();
		include_once(BLSFE_ROOT . "/helpers/modelList.php");


		$this->res = array();
		$this->res['worker_id'] = 0;
		$this->res['service_id'] = 0;
		$this->res['auto_start'] = 0;

		if  (isset($_REQUEST['worker_id'])) {
			$this->res['worker_id'] = $_REQUEST['worker_id'];
		}
		if  (isset($_REQUEST['service_id'])) {
			$this->res['service_id'] = $_REQUEST['service_id'];
		}


		$formatters["service_id"]["value"] = $this->res['service_id'];
		$formatters["service_id"]["hidden"] = true;

		$formatters["worker_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/worker", null , "id", null, 'worker_id', $this->res['worker_id']);
		};

		/*
		$formatters["service_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/service", null , "id", null, 'service_id', $this->res['service_id']);
		};
		*/
		
		$formatters["auto_start"]["function"] = function(){ 
			include_once(BLSFE_ROOT . "/helpers/yesno.php");
			return blsfe_helper_yesno('auto_start', $this->res['auto_start']);
		};
		return $formatters;
	}

	function addAction($redirect=null) {
		$this->setBasicData($_REQUEST['service_id']);
		parent::addAction("core/". $this->tab. "/services/show/" . $_REQUEST['service_id']);
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		$this->setBasicData($_REQUEST['service_id']);
		parent::editAction($id, "core/". $this->tab. "/worker2services");
	}
	
	private function setBasicData($id) {
		if ($id>0){
			$_thisModel = new BLModel("sys/jm/service", "id");
			$res = $_thisModel->get($id);
			$this->set("aData", $res);
		}
	}
}
