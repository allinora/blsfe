<?php

class Core_Bouncer_PatternsController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model=new BLModel("sys/mailer/bounce/rule/pattern", "id", "rule_id");
	}

	function indexAction($rule_id) {
		
		$ruleModel = new BLModel("sys/mailer/bounce/rule", "id", "");
		$rule = $ruleModel->get($rule_id);
		$this->set('rule', $rule);
		$res = $this->model->getall($rule_id);
		$this->set("aData", $res);
	}

	function formatters(){
		include_once(BLSFE_ROOT . "/helpers/modelList.php");
		
		if (isset($_REQUEST['id'])){
			$this->res = $this->model->get($_REQUEST['id']);
		}
		
		$formatters=array();
		$formatters["rule_id"]["function"] = function(){ 
			return blsfe_helper_modelList("sys/mailer/bounce/rule", null , "id", null, 'rule_id', $this->res['rule_id']);
		};
		
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/patterns/index/" . $_REQUEST['rule_id']);
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/patterns/index/" . $_REQUEST['rule_id']);
		
	}
}	
