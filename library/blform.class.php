<?php
include_once(dirname(__FILE__) . "/blmodel.class.php");

// These types should match the bls types
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
	var $vars;

	function __construct($model, $data=array()) {
		$x=new bltransport();
		$shadow=$x->callBusinessLogicService($model . "/reflect");
		$objectProperties = get_object_vars($shadow);
        $this->vars=$objectProperties["vars"];
		//print "<pre>" . print_r($this->vars, true) . "</pre>";


		// Fillin data for the vars if provided
		foreach ($this->vars as $id=> &$var) {
			$var["label"]=$id; // Set the name as the label
			if (isset($data[$id])){
				$var["value"]=$data[$id];
			}
		}
	}
	
	function setLabel($id, $value){
		$this->vars[$id]["label"]=$value;
	}
	function setCss($id, $value){
		$this->vars[$id]["css"]=$value;
	}
	
	
	function setupTray(){
		$this->tray=array();
		foreach ($this->vars as $id=> $f) {
			if (in_array($id, array("createtime", "ts"))){ // Ignore these always. They are handled by the system
				continue;
			}
			$this->tray[$id]["field"]=$this->setupField($id, $f);
			$this->tray[$id]["label"]=$f["label"];
		}
	}
	
	function getTray(){
		return $this->tray;
	}


	function setupField($id, $field){
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
		$text="<input type='text' name='$id' id='$id' value='"  . $f["value"].  "'";
		if ($f["maxlength"]){
			$text.=" maxlength="  . $f["maxlength"];
		}
		if ($f["maxlength"]>50){
			$text.=" size=50 ";
		} else {
			$text.=" size=" . $f["maxlength"] ;
		}
		
		
		if ($f["css"]){
			$text.=" style='" . $f["css"] . "'";
		}
		$text.=" >";
		return $text;
	}
	function textarea($id, $f){
		$text="<textarea name='$id' id='$id' ";
		if ($f["css"]){
			$text.="style='" . $f["css"] . "'";
		}
		$text.=">";
		if ($f["value"]){
			$text.=$f["value"];
		}
		$text.="</textarea>";
		return $text;
	}
}

