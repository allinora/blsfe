<?php

class Core_UserProfiles_AvatarController extends Core_Controller {

	function beforeAction(){
		parent::beforeAction();
		$this->set("tab", "users");
	}
	function imageAction($id,$x=0,$y=0,$name=null) {
		$this->render=0;
		$profileModel=new BLModel("sys/user/profile", "user_id");
		if(defined("USER_AVATARS_DIRECTORY")){
			$avatars_directory=USER_AVATARS_DIRECTORY;
		} else {
			$avatars_directory=$_SERVER["DOCUMENT_ROOT"] . "/uploads/users/avatars";
		}
		
		$profile=$profileModel->get($id);
		if (is_array($profile) && $profile["avatar"]){
			$avatar_file=$avatars_directory . DS . $profile["avatar"];
			if (!file_exists($avatar_file)){
				die("Not found");
			}
			
			blsfe_load_class("BLFileinfo");
			$fileInfo=new BLFileinfo();
		
			$mimetype=$fileInfo->ext2mimetype($profile["avatar"]);
			header("Content-type: $mimetype");
			
			blsfe_load_class("BLImage");
			$blimage=new BLImage();
			$data=$blimage->resizeFile($avatar_file, $x, $y);
			die($data);
		}
	}
}
