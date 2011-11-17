<?php

include_once(dirname(__FILE__)  ."/controller.php");
class FwassetsController extends Core_Controller {

	function beforeAction(){
		$this->render=0;
	}
	
	function __call ($x,$y){
		$action=$x;
		$action=preg_replace("@Action@", "", $action);
		array_unshift($y, $action);
		$path=join("/", $y);
		$file=BLSFE_ROOT . "/assets/" . $path;
		if (file_exists($file)){
			blsfe_load_class("BLFileinfo");
			$fileInfo=new BLFileinfo();
			$mimetype=$fileInfo->ext2mimetype($file);
			header("Content-type: $mimetype");
			readfile($file);
		}
		// Log a warning here. This is really not very optimized way of loading static data
	}
	
	function afterAction(){
	}
}
