<?php

class Core_Controller extends BLController {

	function beforeAction(){
		global $default, $cache;

		// Set the reference to cache
		$this->cache = $cache;
		
		$_wrapper_directory = BLSFE_ROOT . "/core/admin/views";
		$this->setWrapperDir($_wrapper_directory);
		$this->set("blsfe_template_dir", $_wrapper_directory);
		$this->set("blsfe_root", BLSFE_ROOT);
		
		if (defined('WRAPPER')){
			$this->setWrapper(WRAPPER);
		} else {
			$this->setWrapper("wrapper");
		}
		
		
		$language_model = new BLModel("sys/language", "id");
		$_languages = $language_model->getall(1);
		$this->backend_languages = array();
		if (is_array($_languages)){
			foreach($_languages as $l){
				$this->backend_languages[$l["lang"]] = $l;	
			}
			$this->set("languages", $this->backend_languages);
		}
		
		if(isset($default["modules"]["sys"])){
			$this->set("coremodules", $default["modules"]["sys"]);
		}
		if (isset($default["modules"]["app"])){
			$this->set("appmodules", $default["modules"]["app"]);
		}
		

       	$this->render=0;
		
		$this->addPackage("jquery");
        $this->addPackage("twitter-bootstrap");
       	$this->render=1;
		
	}
	
	
	function listAction() {
		$x=$this->model->getall(1);
		$this->set("aData", $x);
	}
	function addAction($redirect) {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$x = $this->model->add($_POST);
			if (substr($x, 0, 10) == "Exception:") {
				$this->sendError("danger", "Backend", $x);
			}
			$this->redirect($redirect);
		}
		
		if ($_SERVER["REQUEST_METHOD"] == "GET"){
			$form=new BLForm($this->model->model());
			$this->preFormatters($form);
			$form->setupTray();
			$this->postFormatters($form);
			$this->set("form", $form->render());
		}
	}

	function editAction($id, $redirect, $formatters=array()) {
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			if ($this->model->methodReplacements["set"]){
				$setter=$this->model->methodReplacements["set"];
				$res=$this->model->$setter($_POST);
			} else {
				// Standard method
				$x = $this->model->set($_POST);
				if (substr($x, 0, 10) == "Exception:") {
					$this->sendError("danger", "Backend", $x);
				}
				
			}
			$this->redirect($redirect);
		}

		if ($_SERVER["REQUEST_METHOD"] == "GET"){
			if (!$id){
				die("Go away");
			}
			$_REQUEST['id'] = $id;
			
			if ($this->model->methodReplacements["get"]){
				$getter=$this->model->methodReplacements["get"];
				$res=$this->model->$getter(array($this->model->idField() => $id));
			} else {
				// Standard method
				$res=$this->model->get($id);
			}
			$this->res = $res;
			$form=new BLForm($this->model->model(), $res);
			$this->preFormatters($form, $res, $formatters);
			$form->setupTray();
			$this->postFormatters($form, $res, $formatters);
			$this->set("form", $form->render());
		}
	}
	
	function preFormatters(&$form, $res=array()){
		// Default postFormatting
		parent::preFormatters($form, $res);
	}

	function postFormatters(&$form, $res=array()){
		// Default postFormatting
		parent::postFormatters($form, $res);
	}

	function afterAction(){
		parent::afterAction();
	}
	
	function setTabName($c){
		$x = explode('_', $c);
		$tab = strtolower($x[1]);
		$this->set("tab", $tab);
		return $tab;
	}


	function setSubTabName($c){
		$c = preg_replace("@Controller$@", "", $c);
		$x = explode('_', $c);
		$subtab = strtolower($x[2]);
		$this->set("subtab", $subtab);
		return $subtab;
	}
	
	function sendError($type, $title, $text){
             print '<html><head><link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css"/></head>';
             print '<body>';
             print '<div class="jumbotron alert alert-' . $type . '">';
             print "<h3>$title</h3>";
             print $text;
             print "</div></body></html>";
             exit;
     }
	
}
