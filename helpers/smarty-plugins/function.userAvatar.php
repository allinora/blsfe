<?php
include_once(dirname(__FILE__) . "/../userAvatar.php");
function smarty_function_userAvatar($params, &$smarty){
	$id=$params["user_id"];
	$x=$params["width"];
	$y=$params["height"];
	return  blsfe_helper_userAvatar($id,$x,$y, $params);
};

