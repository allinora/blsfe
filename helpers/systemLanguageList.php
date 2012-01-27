<?php

function blsfe_helper_systemLanguageList($lang=null){
    $model=new BLModel("/sys/language", "id");
    $aData=$model->getall(1);
	$text="<select name='language' id='language'>";
	foreach($aData as $language){
		$text.="<option value='" . $language["lang"] . "'";
		if ($lang==$language["lang"]){
			$text.=" selected ";
		}
		$text.=">" . $language["name"] . "</option>";
	}
	$text.="</select>";
	return $text;
}

