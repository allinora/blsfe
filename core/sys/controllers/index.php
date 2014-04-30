<?php

class Core_Sys_IndexController extends Core_Controller {

	function indexAction() {
		$this->render = 0;
		print "hi";
	}

	function packagesAction() {
		$this->render = 0;
		$packages_file = BLSFE_ROOT  . '/library/packages.php';
		if (file_exists($packages_file)){
			include($packages_file);
			print "<pre>" . print_r($packagesConfig, true) . "</pre>";
		}
	}
}
