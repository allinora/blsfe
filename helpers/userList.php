<?php

function blsfe_helper_userList($id){
    $model=new BLModel("/sys/user/profile", "user_id");
    $users=$model->getall(1);
	$text="<select name='user_id' id='user_id'>";
	foreach($users as $user){
		$text.="<option value='" . $user["user_id"] . "'";
		if ($id==$user["id"]){
			$text.=" selected ";
		}
		$text.=">" . $user["firstname"]  . " " . $user["lastname"]. "</option>";
	}
	$text.="</select>";
	return $text;
}

