<?php

function blsfe_helper_companyImageUrl($id, $x=0, $y=0){
	$model=new BLModel("/sys/company/image", "id");
	$image=$model->get($id);
	$company_id=$image["company_id"];

	if (is_array($image) && $image["name"]){
		
		if (!$x){
			$x=0;
		}
		if (!$y){
			$y=0;
		}
		return "/core/companies/image/show/$company_id/$id/$x/$y/" . $image["name"];
	}
};

