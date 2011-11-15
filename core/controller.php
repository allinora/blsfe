<?php

class Core_Controller extends BLController {

	function beforeAction(){
		global $default, $cache;

		// Set the reference to cache
		$this->cache=$cache;
		
		$_wrapper_directory=BLSFE_ROOT . "/core/admin/views";
		$this->setWrapperDir($_wrapper_directory);
		$this->set("blsfe_template_dir", $_wrapper_directory);
		
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
	
	function formatters(){
		return array();
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
			$res=$this->model->get($id);
			$form=new BLForm($this->model->model(), $res);
			$this->preFormatters($form, $res, $formatters);
			$form->setupTray();
			$this->postFormatters($form, $res, $formatters);
			$this->set("form", $form->render());
		}
	}
	
	function preFormatters(&$form, $res=array()){
		$formatters=$this->formatters();
		foreach($formatters as $id=>$f){
			if ($f["label"]){
				$form->setLabel($id, $f["label"]);
			}
			if ($f["css"]){
				$form->setCSS($id, $f["css"]);
			}
		}
	}
	function postFormatters(&$form, $res=array()){
		$formatters=$this->formatters();
		foreach($formatters as $id=>$f){
			if ($f["helper"]){
				$helper=$f["helper"];
				$form->replaceTray($id, $this->helper->$helper($res[$id]));
			}
			if ($f["tray_class"]){
				$form->setTrayClass($id, $f["tray_class"]);
			}
		}
	}
	
	function afterAction(){
	}
}
