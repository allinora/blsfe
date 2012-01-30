<?php
include_once(dirname(__FILE__) . "/../cmsImage.php");
function smarty_function_cmsImage($params, &$smarty){
	$id=$params["id"];
	$x=$params["width"];
	$y=$params["height"];
	return  blsfe_helper_cmsImage($id,$x,$y, $params);
};

