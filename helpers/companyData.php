<?php

function blsfe_helper_companyData($id, $field){
	$model = new BLModel('/sys/company', 'id');
	$aData = $model->get($id);
	
	if ($field == 'country.name'){
		$m = new BLModel('sys/country');
		$country = $m->getByCode(array('code' => $aData['country']));
		if (is_array($country)) {
			return $country['name'];
		}
	}


	if ($aData[$field]){
		return $aData[$field];
	}
};

