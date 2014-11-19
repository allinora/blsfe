<?php
include_once(dirname(__FILE__) . "/../systemCountryList.php");
function smarty_function_systemCountryList($params, &$smarty){
        $selected = $params["selected"];
        $data = blsfe_helper_systemCountryList($selected);
        return $data;
};