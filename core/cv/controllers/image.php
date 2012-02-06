<?php

class Core_Cv_ImageController extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->model=new BLModel("sys/user/cv/project", "id", "user_id");
		$this->imagesModel=new BLModel("sys/user/cv/project/image", "id", "project_id");
	}
	
	function showAction($id,$x=0,$y=0,$name=null) {
		$this->render=0;
		if(defined("CV_PROJECT_IMAGES_DIRECTORY")){
			$image_directory=CV_PROJECT_IMAGES_DIRECTORY;
		} else {
			$image_directory=$_SERVER["DOCUMENT_ROOT"] . "/uploads/cv";
		}
		
		$image=$this->imagesModel->get($id);
		if (is_array($image)){
			$image_file=$image_directory . DS . $image["project_id"] . DS .  $image["name"];
			if (!file_exists($image_file)){
				die("Not found");
			}
			
			blsfe_load_class("BLFileinfo");
			$fileInfo=new BLFileinfo();
		
			$mimetype=$fileInfo->ext2mimetype($image["name"]);
			header("Content-type: $mimetype");
			
			blsfe_load_class("BLImage");
			$blimage=new BLImage();
			$data=$blimage->resizeFile($image_file, $x, $y);
			die($data);
		}
	}
	
}
