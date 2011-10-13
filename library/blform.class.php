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
	
	function replaceTray($id, $value){
		//print "<pre>" . print_r($this->tray, true) . "</pre>";
		$this->tray[$id]["field"]=$value;
	}
	function removeFromTray($id){
		unset($this->tray[$id]);
	}
	function hideFromTray($id){
		$this->tray[$id]["hidden"]=1;
	}
	function setTrayClass($id, $value){
		$this->tray[$id]["class"]=$value;
	}
	
	function setupTray(){
		$this->tray=array();
		foreach ($this->vars as $id=> $f) {
			if (in_array($id, array("createtime", "ts"))){ // Ignore these always. They are handled by the system
				continue;
			}
			$this->tray[$id]["field"]=$this->setupField($id, $f);
			$this->tray[$id]["label"]=$f["label"];
			$this->tray[$id]["required"]=$f["required"];
		}
	}
	
	function getTray(){
		return $this->tray;
	}


	function setupField($id, $field){
		if ($id=="country"){
			return $this->countryList($id, $field);
		}
		if ($id=="language"){
			return $this->languageList($id, $field);
		}
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
	
	function selectbox($id, $field, $values=array()){
		$text="<select name='$id' id='$id'>";
		foreach($values as $v){
			$text.="<option value='" . $v["id"] . "'";
			if ($field["value"]==$v["id"]){
				$text.=" selected ";
			}
			$text.=">" . $v["value"] . "</option>";
		}
		$text.="</select>";
		return $text;
	}
	
	function countryList($id, $field){
		$m=new BLModel("core/country");
		$list=$m->getall(1);
		$ret=array();
		foreach($list as $c){
			$ret[$c["isocode"]]["id"]=$c["isocode"];
			$ret[$c["isocode"]]["value"]=$c["name"];
		}
		return $this->selectbox($id, $field, $ret);
	}
	
	function languageList($id, $field){
		$m=new BLModel("core/language");
		$list=$m->getall(1);
		$ret=array();
		foreach($list as $l){
			$ret[$l["lang"]]["id"]=$l["lang"];
			$ret[$l["lang"]]["value"]=$l["name"];
		}
		return $this->selectbox($id, $field, $ret);
	}
	
	
	function textbox($id, $f){
		$text="<input type='text' name='$id' id='$id' value='"  . $f["value"].  "'";
		if ($f["maxlength"]){
			$text.=" maxlength="  . $f["maxlength"];
		}

		if ($f["maxlength"]){
			if ($f["maxlength"]>50){
				$text.=" size=50 ";
			} else {
				$text.=" size=" . $f["maxlength"] ;
			}
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
	
	function render(){
		$formData=$this->tray;
		$data="<form method='POST'><table class='blsfeformtable'>";
		foreach($formData as $id=>$f){
			$data.= "\n<tr ";
			if ($f["class"]){
				$data.=" class='" . $f["class"] . "'";
			}
			if ($f["hidden"]){
				$data.=" style='display: none;'";
			}
			$data.=">";
			$data.= "<th align='right'>" . $f["label"];
			if ($f["required"]){
				$data.= " * ";
			}
			$data.=  "</th>";
			$data.= "<td>" . $f["field"] . "</td>";

			$data.= "</tr>";
		}
		$data.="<tr><td colspan=2 align=right>";
		$data.="<input type='submit'>";
		$data.="</td></tr>";

		$data.="</table></form>";
		return $data;
	}
}

