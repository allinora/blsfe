<?php

class Core_Jm_RolelinkController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model = new BLModel("sys/jm/job/role/link", "id", "job_id");
	}

	function indexAction($job_id) {
		$this->redirect("core/jm/job/show/" . $job_id);
	}

	function showAction($id) {
		$res = $this->model->get($id);
		$this->set("aData", $res);
	}
	
	function formatters(){
		// blsfe_helper_modelList($model, $action=null, $idField, $searchField, $field_name, $id){
		$formatters=array();
		include_once(BLSFE_ROOT . "/helpers/modelList.php");

		if  (isset($_REQUEST['role_id'])) {
			$this->res['role_id'] = $_REQUEST['role_id'];
		}
		if  (isset($_REQUEST['job_id'])) {
			$this->res['job_id'] = $_REQUEST['job_id'];
		}
		$formatters["role_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/role", null , "id", null, 'role_id', $this->res['role_id']);
		};
		$formatters["job_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/job", null , "id", null, 'job_id', $this->res['job_id']);
		};
		
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/jobs/show/" . $_REQUEST['job_id']);
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/jobs");
	}
}
