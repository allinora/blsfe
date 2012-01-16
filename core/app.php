<?php

class App_Controller extends BLController {

	function beforeAction(){
		// Set the reference to cache
		$this->cache=$cache;
	}
	function facebooklogin(){
		if ($_SESSION["user"]){
			// Already logged in
			return;
		}
		$userAccountParams=array();
		$userAccountParams["passwd"]=$_SESSION["fb_user_data"]->id;
		$userAccountParams["email"]=$_SESSION["fb_user_data"]->email;
		$userAccountParams["facebook_id"]=$_SESSION["fb_user_data"]->id;
		$userAccountParams["firstname"]=$_SESSION["fb_user_data"]->first_name;
		$userAccountParams["lastname"]=$_SESSION["fb_user_data"]->last_name;
		$userAccountParams["gender"]=$_SESSION["fb_user_data"]->gender;
		$userAccountParams["language"]=$_SESSION["fb_user_data"]->locale;
		$userAccountParams["birthdate"]=$_SESSION["fb_user_data"]->birthday;
		//$userAccountParams["active"]=$_SESSION["fb_user_data"]->verified;
		
        $picture_url="https://graph.facebook.com/me/picture?type=large&" . $_SESSION["fb_access_token"];
		$userAccountParams["picture"]=base64_encode(file_get_contents($picture_url));

		//print "<pre>" . print_r($userAccountParams, true) . "</pre>";
        $userModel=new BLModel("sys/auth/user", "id");
        $user=$userModel->facebooklogin($userAccountParams);
		if ($user["user_id"]){
			$_SESSION["user"]=$user;
		}
		
	}
	
	function afterAction(){
		$this->set("jslibs", $this->jsLibs);
	}
}
