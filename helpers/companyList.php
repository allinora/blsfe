<?php

function blsfe_helper_companyList($company_id){
    $model=new BLModel("/srm/company", "id");
    $companies=$model->getall(1);
	$text="<select name='company_id' id='company_id'>";
	foreach($companies as $company){
		$text.="<option value='" . $company["id"] . "'";
		if ($company_id==$company["id"]){
			$text.=" selected ";
		}
		$text.=">" . $company["name"] . "</option>";
	}
	$text.="</select>";
	return $text;
}

