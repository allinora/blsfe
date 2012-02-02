<?php

include_once(dirname(__FILE__) . "/modelList.php");

function blsfe_helper_spaceList($id){
	$x=blsfe_helper_modelList("sys/space", null, "id", null, 'space_id', $id);
	return $x;
	print "<pre>" . print_r($x, true) . "</pre>";
}

