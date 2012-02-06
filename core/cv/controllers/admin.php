<?php

class Core_Cv_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "cv");
		$this->model=new BLModel("sys/user/cv/project", "id", "user_id");
		$this->imagesModel=new BLModel("sys/user/cv/project/image", "id", "project_id");
	}
	
	
	function indexAction() {
		$res=$this->model->getall(1);
		//print "<pre>" . print_r($res, true) . "</pre>";
		$this->set("aData", $res);
	}
	function imagesAction($id) {
		$res=$this->imagesModel->getall($id);
		//print "<pre>" . print_r($res, true) . "</pre>";
		$this->set("aData", $res);

		$project=$this->model->get($id);
		$this->set("project", $project);

	}
	
	function uploadAction($id){
		$this->doNotRenderHeader=1;
		$res=$this->model->get($id);
		$this->set("project", $res);
	}
	
	function uploadfileAction($id){
		$this->render=false;
		if (!$id){
			die(json_encode("please provide an id"));
		}
		
		$project=$this->model->get($id);
		blsfe_load_class("BLUpload");
		$bluploader=new BLUpload();
		
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		$bluploader->setFileName($fileName);
		
		$bluploader->setTargetDirectory(CV_PROJECT_IMAGES_DIRECTORY  . "/" .   $project["id"]);
		$ret=$bluploader->startUpload();
		if ($ret["code"]==200){
			$_imageParams=array();
			$_imageParams["name"]=$ret["filename"];
			$_imageParams["project_id"]=$id;
			$x=$this->imagesModel->add($_imageParams);
			
			print "<pre>" . print_r($x, true) . "</pre>";
				
			
			
		}
		print "<pre>" . print_r($ret, true) . "</pre>";
		print $ret;
	}


	function formatters(){
		$formatters=array();
		$formatters["user_id"]["helper"]="blsfe_helper_userList";
		$formatters["category_id"]["helper"]="categoryList";
		$formatters["description"]["css"]="width: 500px; height: 150px; background-color: #d4d4d4";
		//$formatters["image"]["formtype"]="system_image";
		$formatters["image"]["hidden"]="true";

		return $formatters;
	}

	function addAction() {
		parent::addAction("core/cv/admin");
	}
	
	function editAction($id) {
		parent::editAction($id, "core/cv/admin");
	}
}
