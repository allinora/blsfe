<?php

function blsfe_helper_companyData($id, $field){
	$model=new BLModel("/sys/company", "id");
	$aData=$model->get($id);
		if ($aData[$field]){
			return $aData[$field];
		}
};

