<?php

class Core_Images_AdminController extends Admin_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "images");
		$this->model=new BLModel("sys/image", "id", "image_category_id");
		$this->model->cache(false); // Do not cache the result of this model
		$this->set("noMenu", true);
		$this->set("imageManagerRole", "admin");
		$this->addPackage("plupload");
		
	}
	function indexAction() {
		$this->render=0;
		$categoriesModel=new BLModel("sys/image/category", "id", "null");
		$categoriesModel->cache(false); // Do not cache the result of this cal
		$categories=$categoriesModel->getAll(1);
		if (is_array($categories) && count($categories)){
			$default_category=array_shift($categories);
			$this->redirect("core", "images/admin/list/" . $default_category["id"] . "/?" . $_SERVER["QUERY_STRING"]);
		} else {
			$this->redirect("core", "images/admin/addfolder/DEFAULT/?". $_SERVER["QUERY_STRING"]);
		}
		
	}
	
	function addfolderAction($name){
		$this->render=0;
		$categoriesModel=new BLModel("sys/image/category", "id");
		$name=trim(urldecode($name));
		$x=$categoriesModel->add(array("name"=>$name));
		if ($x>0){
			$this->redirect("core", "images/admin/list/$x");
		} else {
			$this->errors[]="Error creating folder with this name";
			$this->redirect("core", "images/admin/list/1");
			print "<pre>" . print_r($x, true) . "</pre>";
		}
	}

	function listAction($category_id) {
		$categoriesModel=new BLModel("sys/image/category", "id");
		$categoriesModel->cache(false); // Do not cache the result of this cal
		$categories=$categoriesModel->getAll(1);
		$this->set("categories", $categories);
		$this->set("category_id", $category_id);
		
		//print "<pre>" . print_r($categories, true) . "</pre>";
		
		$images=$this->model->getall($category_id);
		$this->set("aData", $images);
        
	}
	
	function editAction($id){
		$image=$this->model->get($id);
		if (!$image["id"]){
			die("Error 404: Record not found");
		}
		$this->set("image", $image);
		
		
	}
	function deleteAction($id){
		$this->render=0;
		$image=$this->model->get($id);
		if ($image["id"]){
			$ret=$this->model->delete($id);
			//die("Error 404: Record not found");
			$this->redirect("core", "images/admin/list/" . $image["image_category_id"]);
		}
		//print "<pre>" . print_r($ret, true) . "</pre>";
	}
	function showAction($id, $x=0, $y=0){
		$this->render=0;
		//$key=$this->_model . DS . __CLASS__ . DS . __FUNCTION__ . DS .  "$id-$x-$y";
		
		$image=$this->model->get($id);
		if (!$image["id"]){
			die("Error 404: Record not found");
		}
		$dest_file=CMS_IMAGES_DIRECTORY . "/" . $image["id"] . "/" . $image["name"];
		if (file_exists($dest_file)){
			blsfe_load_class("BLFileinfo");
			$fileInfo=new BLFileinfo();
			$mimetype=$fileInfo->ext2mimetype($image["name"]);
			header("Content-type: $mimetype");
			if (($x || $y) && class_exists("Imagick")) {
		       $imagick = new Imagick($dest_file);
		       $imagick->thumbnailImage($x,$y);
		       $imagick->cropImage($x,$y,0,0);
			   //$this->cache->write($key, array('data' => $imagick, 'mimetype'=>$mimetype));
		       echo $imagick;
			} else {
				die(readfile($dest_file));
			}
		} else {
			die("Error 404: File not found");
		}
	}

	function uploadAction($category_id=1) {
        $this->doNotRenderHeader=0;
		$this->set("noMenu", true);
		$this->set("category_id", $category_id);
	}
	function addfileAction($filename, $category_id=1){
		$this->render=0;
		$_params=array();
		$_params["name"]=$filename;
		$_params["image_category_id"]=$category_id;
		$x=$this->model->add($_params);
		if ($x){
			$file_directory=CMS_IMAGES_DIRECTORY . "/$x";
			if (!is_dir($file_directory)){
				mkdir($file_directory, 0777, true);
			}
			$spool_file=CMS_IMAGES_DIRECTORY . "/spool/" . $filename;
			$dest_file=CMS_IMAGES_DIRECTORY . "/$x/$filename";
			rename($spool_file, $dest_file);
		}
	}
	
    function uploadfileAction(){
			// Note: See if there should be some checks with the session etc..
			// The spool directory should be unique for each session/user...
            $this->render=false;
            blsfe_load_class("BLUpload");
            $bluploader=new BLUpload();
			$bluploader->setTargetDirectory(CMS_IMAGES_DIRECTORY . "/spool/");
			$ret=$bluploader->startUpload();
			$this->logger->log($ret);
            print json_encode($ret);
    }

}
