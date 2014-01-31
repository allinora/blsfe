<?php

class Core_Bouncer_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->tab = $this->setTabName(__CLASS__);
		$this->setSubTabName(__CLASS__);
		$this->model = new BLModel("sys/mailer/bounce/rule", "id", "");
	}
	function indexAction() {
		$aData= $this->model->getall(1);
		//print "<pre>" . print_r($aData, true) . "</pre>";
		$this->set('aData', $aData);
	}
	

	function formatters(){
		$formatters=array();
		$formatters["type"]["helper"]="blsfe_helper_bouncetypelist";
		return $formatters;
	}

	function addAction($redirect=null) {
		parent::addAction("core/". $this->tab. "/admin");
	}
	
	function editAction($id, $redirect=null, $formatters=array()) {
		parent::editAction($id, "core/". $this->tab. "/admin");
	}
}
