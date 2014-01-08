<?php

class Core_Systememails_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "systememails");
		$this->model=new BLModel("/sys/mailer/template/system", "id");
	}
	function indexAction() {
		$pages=$this->model->getall(1);
		$this->set("aData", $pages);
	}

	function addAction($redirect=null) {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$this->model->add($_REQUEST);
			$this->redirect("core", "systememails/admin/");
		}
	}

	function deleteAction($id) {
			$this->render=0;
			$xx= $this->model->delete($id);
			$this->redirect("core", "systememails/admin/");
	}
	
	function editAction($id, $redirect=null, $formatters = array()) {
		$templateData=$this->model->getTemplateWithTranslations(array("id"=>$id));
		//print "<pre>" . print_r($templateData, true) . "</pre>";
		$template=array();
		$template["id"]=$templateData["id"];
		$template["name"]=$templateData["name"];
		foreach($this->backend_languages as $key=>$l){
			if ($templateData["translations"][$key]){
				$template["translations"][$key]=$templateData["translations"][$key];
			} else {
				$template["translations"][$key]=array(
					"template_id"=>$templateData["id"],
					"lang"=>$key
				);
			}
			
		}
		$this->set("aData", $template);
		//print "<pre>" . print_r($template, true) . "</pre>";
	}
	function saveAction(){
		$this->render=0;
		$templateDataModel=new BLModel("sys/mailer/template/system/data", "id", "template_id");
		if ($_REQUEST["id"]>0){
			$x=$templateDataModel->set($_REQUEST);
		} else {
			$x=$templateDataModel->add($_REQUEST);
		}
		//print "<pre>" . print_r($x, true) .- "</pre>";
		//print "<pre>" . print_r($_REQUEST, true) .- "</pre>";
		$this->redirect("core", "systememails/admin/edit/" . $_REQUEST["template_id"]);
	}
}
