<?php

class App_Controller extends BLController {

	function beforeAction(){
		global $cache;
		// Set the reference to cache
		$this->cache=$cache;
		// Allow getting just the output without the wrapper
		if ($_REQUEST["nowrapper"]==1){
			$this->doNotRenderHeader=1;
		}
	}
	
	function updateUserNotifications(){
		if (!$_SESSION["user"]["user_id"]>0){
			return;
		}
		$model=new BLModel("sys/user/notification", "id", "user_id");
		$subscriptions=$model->getall($_SESSION["user"]["user_id"]);
		$notification=array();
		foreach($subscriptions as $s){
			$notifications[$s["notif_name"]][$s["notif_value"]]=1;
		}
		$_SESSION["user"]["notifications"]=$notifications;
		
		//print "<pre>" . print_r($_SESSION["user"], true) . "</pre>";
	}
	
	function addUserNotification($notif_name, $notif_value){
		if (!$_SESSION["user"]["user_id"]>0){
			return;
		}
		if ($_SESSION["user"]["notifications"][$notif_name][$notif_value]==1){
			return;
		}
		
		$model=new BLModel("sys/user/notification", "id", "user_id");
		$data=array();
		$data["notif_name"]=$notif_name;
		$data["notif_value"]=$notif_value;
		$data["user_id"]=$_SESSION["user"]["user_id"];
		$x=$model->add($data);
		//print "<pre>" . print_r($x, true) . "</pre>";
		$this->updateUserNotifications();
	}
	
	
	
	function login(){
        $userModel=new BLModel("sys/auth/user", "id");
		// Check for the syntax validity of the email and see if the password is provided.
		
        $auth=$userModel->login($_REQUEST);
		if ($auth["user_id"]>0){
			$_SESSION["user"]=$auth;
			$this->updateUserNotifications();
			// $this->redirect("/");
		}  else {
			$_SESSION["authfailures"]++;
			$this->set("errorMessage", "Authentication failure (" . $_SESSION["authfailures"] . ")");
		}
		
	}
	function facebooklogin(){
		if ($_REQUEST["op"]=="logout"){
			return;
		}
		
		
		if ($_SESSION["user"]){
			// Already logged in
			return;
		}
		// print "Doing facebook login";
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
        $user=$userModel->facebooklogin($userAccountParams, "POST");
		if ($user["user_id"]){
			$_SESSION["user"]=$user;
			$this->updateUserNotifications();
		} else {
			print "<pre>" . print_r($user, true) . "</pre>";exit;
		}
		
	}


	function logout(){
		$_SESSION=array();
		unset($_SESSION);
		//$this->redirect("/");
	}

	function afterAction(){
		parent::afterAction();
		$this->set("jslibs", $this->jsLibs);
	}
}
