<?php

function blsfe_helper_companyImageUrl($id, $x=0, $y=0, $cached=false){
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

		if ($cached){
			$_url= CMS_CACHE_PREFIX . "/core/companies/image/show/$company_id/$id/$x/$y/" . $image["name"];
		} else {
			$_url="/core/companies/image/show/$company_id/$id/$x/$y/" . $image["name"];
		}
		return $_url;
	}
};

