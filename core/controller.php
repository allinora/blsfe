<?php

class Core_Controller extends BLController {

	function beforeAction(){
		global $default, $cache;

		// Set the reference to cache
		$this->cache=$cache;
		
		$_wrapper_directory=BLSFE_ROOT . "/core/admin/views";
		$this->setWrapperDir($_wrapper_directory);
		$this->set("blsfe_template_dir", $_wrapper_directory);
		$this->set("blsfe_root", BLSFE_ROOT);
		
		$language_model=new BLModel("sys/language", "id");
		$_languages=$language_model->getall(1);
		$this->backend_languages=array();
		foreach($_languages as $l){
			$this->backend_languages[$l["lang"]]=$l;	
		}
		$this->set("languages", $this->backend_languages);
		
		$this->set("coremodules", $default["modules"]["sys"]);
		$this->set("appmodules", $default["modules"]["app"]);
	}
	
	
	function listAction() {
		$x=$this->model->getall(1);
		$this->set("aData", $x);
	}
	function addAction($redirect) {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$x=$this->model->add($_POST);
			$this->redirect($redirect);
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET"){
			$form=new BLForm($this->model->model());
			$this->preFormatters($form);
			$form->setupTray();
			$this->postFormatters($form);
			$this->set("form", $form->render());
		}
	}

	function editAction($id, $redirect, $formatters=array()) {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$x=$this->model->set($_POST);
			$this->redirect($redirect);
		}

		if ($_SERVER["REQUEST_METHOD"] == "GET"){
			if (!$id){
				die("Go away");
			}
			if ($this->model->methodReplacements["get"]){
				$getter=$this->model->methodReplacements["get"];
				$res=$this->model->$getter(array($this->model->idField() => $id));
			} else {
				// Standard method
				$res=$this->model->get($id);
			}
			$form=new BLForm($this->model->model(), $res);
			$this->preFormatters($form, $res, $formatters);
			$form->setupTray();
			$this->postFormatters($form, $res, $formatters);
			$this->set("form", $form->render());
		}
	}
	
	function preFormatters(&$form, $res=array()){
		// Default postFormatting
		parent::preFormatters($form, $res);
	}

	function postFormatters(&$form, $res=array()){
		// Default postFormatting
		parent::postFormatters($form, $res);
	}

	function afterAction(){
		$this->set("jslibs", $this->jsLibs);
	}
}
