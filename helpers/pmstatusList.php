<?php


function blsfe_helper_pmstatusList($status){
	$options=array("OPEN", "DONE", "LATER", "INVALID");
	$text="";
	foreach($options as $op){
		$text.="<input type='radio' name='status'  value='$op'";
		if($op==$status){
			$text.=" checked ";
		}
		$text.=" >$op</input>";
	}
	return $text;
    


	$text="<select name='status' id='status'>";
	foreach($options as $op){
		$text.="<option value='$op'";
		if($op==$status){
			$text.=" selected ";
		}
		$text.=" >$op</option>";
	}
	return $text;
    
}

