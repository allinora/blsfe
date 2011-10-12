<?php
include_once(dirname(__FILE__) . "/blmodel.class.php");

define('OBJ_DTYPE_INT', 1);
define('OBJ_DTYPE_URL', 2);
define('OBJ_DTYPE_EMAIL', 3);
define('OBJ_DTYPE_ARRAY', 4);
define('OBJ_DTYPE_STRING', 5);
define('OBJ_DTYPE_DATE', 6);
define('OBJ_DTYPE_POSOURCE', 7);
define('OBJ_DTYPE_FLOAT',8);
define('OBJ_DTYPE_OTHER',9);
define('OBJ_DTYPE_CREATEDTIME',10);
define('OBJ_DTYPE_MODIFIEDTIME',11);
define('OBJ_DTYPE_TIMESTAMP', 12);
define('OBJ_DTYPE_DATETIME', 13);
define('OBJ_DTYPE_SHA1', 14);
define('OBJ_DTYPE_CURRDATE',15);


class BLForm extends BLModel {
	var $tray;
	
	function __construct($model, $id) {
		
		$x=new bltransport();
		$shadow=$x->callBusinessLogicService($model . "/reflect");
		$objectProperties = get_object_vars($shadow);
        $vars=$objectProperties["vars"];
        $this->tray($vars);
        //return $this->render($vars);
		
	}
	function getTray(){
		return $this->tray;
	}
	function tray($_vars){
		$this->tray=array();
		foreach ($_vars as $id=> $f) {
			$this->tray[$id]=$this->field($id, $f);
		}
	}

	function render($y){
		$_vars=$y;
		//print "<pre>" . print_r($_vars->lastname, true) . "</pre>";
		foreach ($_vars as $id=> $f) {
			$this->field($id, $f);
		}
	}

	function field($id, $field){
		
		//print "<pre>" . print_r($z, true) . "</pre>";
		switch($field["data_type"]){
			case OBJ_DTYPE_STRING:	
			if ($field['maxlength'] > 200){
				return $this->textarea($id, $field);
			} else {
				return $this->textbox($id, $field);
			} 
			break;
			case OBJ_DTYPE_INT:	
				return $this->textbox($id, $field);
			break;
			default:	
				return $this->textbox($id, $field);
			break;
		
		}
		
	}
	
	function textbox($id, $f){
		$text="<input type='text' name='$id' id='id' value=''>";
		return $text;
	}
	function textarea($id, $f){
		$text="<textarea name='$id' id='id'></textarea>";
		return $text;
	}
}

