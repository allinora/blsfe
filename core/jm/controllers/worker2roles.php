<?php

class Core_Jm_Worker2RolesController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model = new BLModel("sys/jm/worker/role/link", "id", "role_id");
	}

	function indexAction($role_id) {
		$this->redirect("core/jm/roles/show/" . $role_id);
	}

	function formatters(){
		// blsfe_helper_modelList($model, $action=null, $idField, $searchField, $field_name, $id){
		$formatters=array();
		include_once(BLSFE_ROOT . "/helpers/modelList.php");

		if  (isset($_REQUEST['role_id'])) {
			$this->res['role_id'] = $_REQUEST['role_id'];
		}
		if  (isset($_REQUEST['worker_id'])) {
			$this->res['worker_id'] = $_REQUEST['worker_id'];
		}
		$formatters["role_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/role", null , "id", null, 'role_id', $this->res['role_id']);
		};
		$formatters["role_id"]["hidden"] = true;
		
		$formatters["worker_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/jm/worker", null , "id", null, 'worker_id', $this->res['worker_id']);
		};
		
		return $formatters;
	}

	function addAction($redirect=null) {
		$this->setBasicData($_REQUEST['role_id']);
		parent::addAction("core/". $this->tab. "/roles/show/" . $_REQUEST['role_id']);
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		$this->setBasicData($_REQUEST['role_id']);
		parent::editAction($id, "core/". $this->tab. "/roles");
	}

	private function setBasicData($id) {
		if ($id>0){
			$_thisModel = new BLModel("sys/jm/role", "id");
			$res = $_thisModel->get($id);
			$this->set("aData", $res);
		}
	}
}
