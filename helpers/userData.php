<?php

function blsfe_helper_userData($user_id, $field){
	$userModel=new BLModel("/sys/auth/user", "id");
	$profileModel=new BLModel("/sys/user/profile", "user_id");

	// Should not do a userModel->get(user_id); Its not allowed
	
	$userData=$profileModel->get($user_id);
	switch ($field){
		case "firstnameLastname":
		return $userData["firstname"] . " "  . $userData["lastname"];
		break;
		default:
		if ($userData[$field]){
			return $userData[$field];
		}
		break;
	}
};

