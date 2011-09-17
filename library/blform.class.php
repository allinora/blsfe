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
	function __construct($model, $id) {
		
		$x=new bltransport();
		$shadow=$x->callBusinessLogicService("/core/user/reflectjson");
		$y=json_decode($shadow);
		return $this->render($y);
		
		exit;
		
	}
	function render($y){
		$_vars=$y->vars;
		//print "<pre>" . print_r($_vars->lastname, true) . "</pre>";
		foreach ($_vars as $id=> $f) {
			$this->field($id, $f);
			//print "<pre>" . print_r($f, true) . "</pre>";
		}
	}

	function field($id, $z){
		
		print "<pre>" . print_r($z, true) . "</pre>";
		switch($z->data_type){
			case OBJ_DTYPE_STRING:
			if ($z->maxlength>40){
				return $this->textarea($id, $z);
			} else {
				return $this->textbox($id, $z);
			} 
			print "Its a string \n";
			break;
		
		}
		
	}
	
	function textbox($id, $f){
		$text="<input type='text' name='$id' id='id' value=''>";
		print $text;
	}
	function textarea($id, $f){
		$text="<textarea name='$id' id='id'></textarea>";
		print $text;
	}
}

