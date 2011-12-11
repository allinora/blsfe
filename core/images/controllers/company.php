<?php

class Core_Images_CompanyController extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		if (!$_SESSION["companyData"]){
			die("Sorry. I could not find any company data");
		};
		$this->company_id=$_SESSION["companyData"]["id"];
		$this->spool_directory=CMS_IMAGES_DIRECTORY . "/company/" .$this->company_id . "/spool";
		$this->dest_directory=CMS_IMAGES_DIRECTORY . "/company/" .$this->company_id;
		
		
		$this->set("tab", "images");
		$this->model=new BLModel("sys/company/image", "id", "image_category_id");
		$this->model->cache(false); // Do not cache the result of this model
		$this->jsLibs["pluploader"]=1;
		$this->set("noMenu", true);
		$this->set("imageManagerRole", "company");
		
	}
	function indexAction() {
		$this->render=0;
		
		$categoriesModel=new BLModel("sys/company/image/category", "id", "company_id");
		$categoriesModel->cache(false); // Do not cache the result of this cal
		$categories=$categoriesModel->getAll($this->company_id);
		
		if (is_array($categories) && count($categories)){
			$default_category=array_shift($categories);
			$this->redirect("core", "images/company/list/" . $default_category["id"] . "/?" . $_SERVER["QUERY_STRING"]);
			
		} else {
			$this->redirect("core", "images/company/addfolder/DEFAULT/?". $_SERVER["QUERY_STRING"]);
			
		}
		
		
		// $this->redirect("core", "images/company/list/1");
	}
	
	function addfolderAction($name){
		$this->render=0;
		$categoriesModel=new BLModel("sys/company/image/category", "id");
		
		$name=trim(urldecode($name));
		$x=$categoriesModel->add(array(
			"name"=>$name,
			"company_id"=>$this->company_id
			));
	
		if ($x>0){
			$this->redirect("core", "images/company/list/$x/?". $_SERVER["QUERY_STRING"]);
		} else {
			$this->errors[]="Error creating folder with this name";
			//$this->redirect("core", "images/company/list/0");
			print "<pre>" . print_r($x, true) . "</pre>";
		}
	}

	function listAction($category_id=0) {
		
		$categoriesModel=new BLModel("sys/company/image/category", "id", "company_id");
		$categoriesModel->cache(false); // Do not cache the result of this cal
		$categories=$categoriesModel->getAll($this->company_id);
		if ($category_id==0 && is_array($categories) && count($categories)){
			$default_category=array_shift($categories);
			$category_id=$default_category["id"];
		}
		
		$this->set("categories", $categories);
		$this->set("category_id", $category_id);
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
	function showAction($id, $x=0, $y=0){
		$this->render=0;
		//$key=$this->_model . DS . __CLASS__ . DS . __FUNCTION__ . DS .  "$id-$x-$y";
		
		$image=$this->model->get($id);
		if (!$image["id"]){
			die("Error 404: Record not found");
		}
		$dest_file=$this->dest_directory . "/" . $image["id"] . "/" . $image["name"];
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
		$this->jsLibs["pluploader"]=1;
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
			$file_directory=$this->dest_directory . "/$x";
			if (!is_dir($file_directory)){
				mkdir($file_directory, 0777, true);
			}
			$spool_file=$this->spool_directory  .  "/" . $filename;
			$dest_file=$this->dest_directory . "/$x/$filename";
			rename($spool_file, $dest_file);
		}
	}
	
    function uploadfileAction(){
			// Note: See if there should be some checks with the session etc..
			// The spool directory should be unique for each session/user...
            $this->render=false;
            blsfe_load_class("BLUpload");
            $bluploader=new BLUpload();
			$bluploader->setTargetDirectory($this->spool_directory);
			$ret=$bluploader->startUpload();
			$this->logger->log($ret);
            print json_encode($ret);
    }

}
