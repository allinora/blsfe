<?php


function blsfe_helper_bouncetypeList($status){
	$options=array("SOFT", "HARD", "NONE");
	$text="";
	foreach($options as $op){
		$text.="<input style='margin-right:10px;' type='radio' name='type'  value='$op'";
		if($op==$status){
			$text.=" checked ";
		}
		$text.=" >$op</input>&nbsp;&nbsp;&nbsp;";
	}
	return $text;
}

