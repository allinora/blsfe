<?php

function blsfe_helper_cmsImage($id, $x=0, $y=0){
	$model=new BLModel("/sys/image", "id");
	$image=$model->get($id);
	if (!$x){
		$x=0;
	}
	if (!$y){
		$y=0;
	}
	return "<img src='/core/images/cms/system/$id/$x/$y/" . $image["name"] . "'>";
	print "<pre>" . print_r($image, true) . "</pre>";
};

