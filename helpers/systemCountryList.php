<?php

function blsfe_helper_systemCountryList($selected_country = null){
    $model = new BLModel("/sys/country", "id");
    $aData = $model->getall(1);
	$text = "<select name='country' id='country'>";
	foreach($aData as $country){
		$text.="<option value='" . $country["code"] . "'";
		if ($selected_country == $country["code"]){
			$text.=" selected ";
		}
		$text.=">" . $country["name"] . "</option>";
	}
	$text.="</select>";
	return $text;
}

