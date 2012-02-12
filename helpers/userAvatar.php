<?php
include_once(dirname(__FILE__) . "/imageFormatted.php");
include_once(dirname(__FILE__) . "/userAvatarUrl.php");
function blsfe_helper_userAvatar($id, $x=0, $y=0, $params){
	$image_url=blsfe_helper_userAvatarUrl($id, $x, $y, $params);
	return blsfe_helper_imageFormatted($image_url, $params);
};

