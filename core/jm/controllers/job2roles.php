<?php

class Core_Jm_Job2RolesController extends Admin_Controller {

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
		$formatters["job_id"]['value'] = $this->res['job_id'];
		$formatters["job_id"]['hidden'] = 1;
		
		return $formatters;
	}

	function addAction($redirect=null) {
		$this->setBasicData($_REQUEST['job_id']);
		parent::addAction("core/". $this->tab. "/jobs/show/" . $_REQUEST['job_id']);
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		$this->setBasicData($_REQUEST['job_id']);
		parent::editAction($id, "core/". $this->tab. "/jobs");
	}
	private function setBasicData($id) {
		if ($id>0){
			$_thisModel = new BLModel("sys/jm/job", "id");
			$res = $_thisModel->get($id);
			$this->set("aData", $res);
		}
	}
}
