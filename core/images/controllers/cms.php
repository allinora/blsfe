<?php

class Core_Images_CmsController extends BLController {

	function beforeAction(){
		parent::beforeAction();
		$this->render=0;
	}


	function systemAction($id, $x=0, $y=0){
		$model=new BLModel("sys/image", "id", "image_category_id");
		
		$image=$model->get($id);
		if (!$image["id"]){
			die("Error 404: Record not found :" . CMS_IMAGES_DIRECTORY);
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
		       echo $imagick;
			} else {
				die(readfile($dest_file));
			}
		} else {
			die("Error 404: File not found " . CMS_IMAGES_DIRECTORY);
		}
	}
	
	function companyAction($id, $x=0, $y=0){
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
	
}
