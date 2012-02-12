<?php
class Core_Companies_ImageController extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
	}

	function showAction($company_id, $id,$x=0,$y=0,$name=null) {
		
		
		$this->render=0;
		
		$dest_file=CMS_IMAGES_DIRECTORY . "/company/"  . $company_id . DS . $id . "/" . $name;
		
		if (!file_exists($dest_file) || is_dir($dest_file)){
			die("Not found");
		}
	
		blsfe_load_class("BLImage");
		$blimage=new BLImage();
		$blimage->displayImage($dest_file, $x, $y);
	}
}
