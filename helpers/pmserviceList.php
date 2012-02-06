<?php

include_once(dirname(__FILE__) . "/modelList.php");

function blsfe_helper_pmserviceList($id){
	$x=blsfe_helper_modelList("sys/pm/project/service", null, "id", null, 'service_id', $id);
	return $x;
	print "<pre>" . print_r($x, true) . "</pre>";
}

