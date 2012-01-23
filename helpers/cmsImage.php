<?php

function blsfe_helper_cmsImage($id, $x=0, $y=0){
	$model=new BLModel("/sys/image", "id");
	$image=$model->get($id);
	
	if (is_array($image) && $image["name"]){
		
		if (!$x){
			$x=0;
		}
		if (!$y){
			$y=0;
		}
		return "<img src='/core/images/cms/system/$id/$x/$y/" . $image["name"] . "'>";
	}
	return "<img src='/fwassets/images/1x1.png' width='$x' height='$y'>";
	
	// print "<pre>" . print_r($image, true) . "</pre>";
};

