<?php
if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
	define("_IDS_REMOTE_ADDR", $_SERVER["HTTP_X_FORWARDED_FOR"]);
} elseif(isset($_SERVER["REMOTE_ADDR"])){
	define("_IDS_REMOTE_ADDR", $_SERVER["REMOTE_ADDR"]);
}


if  (isset($_SERVER["HTTP_HOST"])){
	$_my_host_name=strtolower($_SERVER["HTTP_HOST"]);
	$_my_host_name=preg_replace("@^www\.@", "", $_my_host_name);
	define("_IDS_HTTP_HOST",$_my_host_name);
}

// Check if the config file exists.

$config_file = ROOT . '/config/ids.php';
if (file_exists($config_file)) {
	$conf = include($config_file);
	$ids = new IDS($conf);
}




class IDS {
	var $tolerated_cidrs = array();
	
	public  function __construct($conf) {
		if (!defined('_IDS_REMOTE_ADDR')){
			return;
		}
		if (!defined('_IDS_HTTP_HOST')){
			return;
		}
		$this->conf = $conf;
		
		$this->db = new DBQuery($conf['dbhost'], $conf['dbuser'], $conf['dbpass'], $conf['dbname']);
		$this->rules = $this->setupRules();
		$this->setupTolerated();
		$this->run();
		
	}
	private function run(){
		$this->_ids_check_jail();
		$this->_ids_track_fields();
		
		$ret=$this->matchRules($_SERVER["REQUEST_URI"]);
		if ($ret){
			if (is_array($ret)){
				$this->_ids_log_attempt($ret[0]);
				switch($ret[0]){
					case "intbug":
					$this->_ids_log_int($ret[1], $ret[2]);
					$this->_ids_complain_int($ret[1], $ret[2]);
					break;
					case "langbug":
					$this->_ids_complain_lang($ret[1], $ret[2]);
					break;
					default:
					$this->_ids_complain($ret[1], $ret[2]);
					break;
				}
			} else {
				print "$ret<br>";
				$this->_ids_log_attempt($ret);
			}
			exit;
		};
		
	}
	
	private function matchRules($uri){
		foreach($this->rules as $name=>$rule){
			$type=$rule["type"];
			if ($type=="filter_int"){
				$ret=$this->filter_int_query($rule, $uri);
				if ($ret) {
					return $ret;
				}

			}
			if ($type=="filter_lang"){
				$ret=$this->filter_lang_query($rule, $uri);
				if ($ret) {
					return $ret;
				}

			}
			if ($type=="preg_match"){
				$ret=$this->filter_pregmatch_query($rule, $uri);
				if ($ret) {
					return $ret;
				}
			}
		}
		return false;
	}
	private function filter_lang_query($rule, $uri){
		
		foreach($rule["langkeys"] as $key){
			if ($this->_ids_get_request_param($key)){
				if (trim($this->_ids_get_request_param($key))!=strtolower(substr($this->_ids_get_request_param($key), 0,2))){
					return array($rule["status"], $key , $this->_ids_get_request_param($key));
				}
				
			}
		}
		//print "Returning false from " . __FUNCTION__ . "\n";
		return false;

	}

	private function filter_int_query($rule, $uri){
		foreach($rule["intkeys"] as $key){
			if ($this->_ids_get_request_param($key)){
				if(!filter_var($this->_ids_get_request_param($key), FILTER_VALIDATE_INT)){ 
					return array($rule["status"], $key, $this->_ids_get_request_param($key));
				}
			}
		}
		//print "Returning false from " . __FUNCTION__ . "\n";
		return false;

	}
	private function filter_pregmatch_query($rule, $uri){
		$re = $rule["expr"];
		if ($rule["target"] == 'QUERY_STRING'){
			$uri = $_SERVER['QUERY_STRING'];
		}
		if ($rule["target"] == 'REQUEST_URI'){
			$uri = $_SERVER['REQUEST_URI'];
		}
		
		if (preg_match($re, $uri, $m)){
			return array_merge(array($rule["status"]), $m);
		}
		if (preg_match($re, urldecode($uri), $m)){
			return array_merge(array($rule["status"]), $m);
		}
		//print "Returning false from " . __FUNCTION__ . "\n";
		return false;
	}


	private function setupTolerated(){
		$this->tolerated_cidrs["129.0.0.0/24"]="Localhost";
	}
	
	
	
	private function setupRules(){
		$rules=array();
		
		$rules['SQLINJECTION1'] = array(
			'type' 		=> 'preg_match',
			'status'	=> 'sqlinject',
			'expr'		=> '@.*(select.*from|union.*select|.order.by.\d).*@',
			'target'	=> 'QUERY_STRING'
			);

		$rules['SQLINJECTION2'] = array(
			'type'		=> 'preg_match',
			'status'	=> 'sqlinject',
			'expr'		=> '@.*(benchmark|sleep|information_schema|drop.*table).*@',
			'target'	=> 'QUERY_STRING'
			);

		$rules['XSS1'] = array(
			'type'		=> 'preg_match',
			'status'	=> 'xss',
			'expr'		=> '@.*(\+chosen\+nickname\+).*@',
			'target'	=> 'QUERY_STRING'
			);

		$rules['XSS2'] = array(
			'type'		=> 'preg_match',
			'status'	=> 'xss',
			'expr'		=> '@.*(\+result\:).*@i',
			'target'	=> 'QUERY_STRING'
			);


		$rules['SPAM1'] = array(
			'type'		=> 'preg_match',
			'status'	=> 'spam',
			'expr'		=> '@.*(mailto\:\?).*@',
			'target'	=> 'QUERY_STRING'
			);

		$rules['BANNED1'] = array(
			'type'		=> 'preg_match',
			'status'	=> 'xss',
			'expr'		=> '@.*(https://|http://|ftp://|cmd\.txt|/id.*\.txt).*@',
			'target'	=> 'QUERY_STRING'
			);

		$rules['INTBUG'] = array(
			'type' 		=> 'filter_int',
			'status' 	=> 'intbug',
			'intkeys' 	=> array(	"article_id", "company_id", "user_id", "bill_id", "booked_article_id", "client_id", "reseller_id", "domain_id", "template_id", "bill_number", "quantity"),
			'target' 	=> 'QUERY_STRING'
			);

		$rules['LANGBUG'] = array(
			'type' 		=> 'filter_lang',
			'status' 	=> 'langbug',
			'langkeys' 	=> array('lang', 'language'),
			'target' 	=> 'QUERY_STRING'
			);
			return $rules;
	}
	
	private function _ids_track_fields(){
		// Collect stats on variables and their value types
		$db=$this->db;
		foreach($_REQUEST as $k=>$v){
			if (!empty($v)){
				if (is_numeric($v)){
					$query=sprintf("insert into TrackFields set createtime=now(), req_var=%s, is_number=1 on duplicate key update is_number=is_number+1",$db->quote($k));
				} else {
					$query=sprintf("insert into TrackFields set createtime=now(), req_var=%s, is_string=1 on duplicate key update is_string=is_string+1",$db->quote($k));
				}
			    //print "<pre>$query</pre>";
			    $db->execute($query);	
				
			}
		}
	}
	private function _ids_log_attempt($status=""){
		$db=$this->db;
		$query=sprintf(
		        "insert into HackAttempts set createtime=now(),  ts=now(), date=now(),  agent=%s, ip=%s, domain=%s, request_uri=%s, status=%s",
		        $db->quote($_SERVER['HTTP_USER_AGENT']),
		        $db->quote(_IDS_REMOTE_ADDR),
		        $db->quote(_IDS_HTTP_HOST),
		        $db->quote($_SERVER["REQUEST_URI"]),
		        $db->quote($status)
		);


		    //print "$query";
		    $db->execute($query);	
	}
	private function _ids_log_int($badfield="unkown", $badvalue=''){
		return;
		$db=$this->db;

		$query=sprintf(
		        "insert into intbugs set ts=now(), date=now(), agent=%s,  ip=%s, domain=%s, request_uri=%s, badfield=%s, badvalue=%s, method=%s",
		        $db->quote($_SERVER['HTTP_USER_AGENT']),
		        $db->quote(_IDS_REMOTE_ADDR),
		        $db->quote(_IDS_HTTP_HOST),
		        $db->quote($_SERVER["REQUEST_URI"]),
		        $db->quote($badfield),
		        $db->quote($badvalue),
		        $db->quote($_SERVER["REQUEST_METHOD"])
		);


		    //print "<pre>$query</pre><br>";
		    $db->execute($query);	
	}

	private function _ids_cidr_match($ip, $cidr){
	    list($subnet, $mask) = explode('/', $cidr);
	    if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet)){ 
	        return true;
	    }
	    return false;
	}

	private function _ids_is_in_tolerated_range($ip = _IDS_REMOTE_ADDR){
		foreach($this->tolerated_cidrs as $cidr => $label){
			if ($this->_ids_cidr_match($ip, $cidr)){
				// Do not block
				return $label;
			}
		}
		return false;
	}

	private function _ids_get_request_param($key){
		if (isset($_REQUEST[$key])){
			$v = trim($_REQUEST[$key]);
			if (!empty($v)){
				return $v;
			}
		}
		return false;
	}
	private function _ids_get_attempts($ip){
		$db = $this->db;
		$sql=sprintf("select ts, domain, ip, request_uri from HackAttempts where ip=%s", $db->quote($ip));
		return $db->GetAll($sql);
	}
	private function _ids_check_jail(){
		$db = $this->db;

		if ($this->_ids_is_in_tolerated_range()){
			// Do not block
			return;
		}

		$query = sprintf("select * from Jail where ip=%s", $db->quote(_IDS_REMOTE_ADDR));
		$row = $db->GetRow($query);
		if ($row["id"]){
		    syslog(LOG_DEBUG, "ServiceDenied: " . $row["country"] . " / " . _IDS_REMOTE_ADDR);
			$data = $this->_ids_get_attempts(_IDS_REMOTE_ADDR);
			$text = _IDS_REMOTE_ADDR . "<br><pre>" . print_r($data, true) . "</pre>";
			$this->complain_and_die("danger",  $text , "Service Denied");
		}
	}
	
	private function _ids_complain_int($f, $v){
	    syslog(LOG_DEBUG, "HackAttempt: " . _IDS_REMOTE_ADDR . " Matched: \"$v\" IN \"$f\"");
	    print "<font color='red'>IDS Block</font><br>";
	    print "Value of $f is not a number at $v";
		die();
	}
	private function _ids_complain_lang($f, $v){
	    syslog(LOG_DEBUG, "HackAttempt: " . _IDS_REMOTE_ADDR . " Matched: \"$v\" IN \"$f\"");
		$this->complain_and_die("warning",  "Value of $f is not a valid language code  at $v");
	}
	private function _ids_complain($m_string, $string){
	    syslog(LOG_DEBUG, "HackAttempt: " . _IDS_REMOTE_ADDR . " Matched: \"$m_string\" IN \"$string\"");
		$text = "The request did not pass our safety tests.";
		$this->complain_and_die("danger",  $text);
	}

	private function complain_and_die($type,   $text, $title="Request Blocked") {
		print '<html><head><link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css"/></head>';
		print '<body>';
		print '<div class="jumbotron alert alert-' . $type . '">';
		print "<h3>$title</h3>";
		print $text;
		print "</div></body></html>";
		die();
	}
	
	
	
}




/* 


-- SQL Schema

CREATE TABLE `hackattempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `domain` varchar(100) NOT NULL,
  `ip` char(15) NOT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT '',
  `country` char(2) DEFAULT NULL,
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `agent` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `domain_idx` (`domain`),
  KEY `ip_idx` (`ip`),
  KEY `date_idx` (`date`),
  KEY `status_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `intbugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `domain` varchar(100) NOT NULL,
  `ip` char(15) NOT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `status` enum('true','false','unknown','bug','sqlinject','xss') DEFAULT 'unknown',
  `country` char(2) DEFAULT NULL,
  `badfield` varchar(20) DEFAULT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `badvalue` varchar(100) DEFAULT NULL,
  `agent` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `domain_idx` (`domain`),
  KEY `ip_idx` (`ip`),
  KEY `date_idx` (`date`),
  KEY `status_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE if exists `trackfields`;
CREATE TABLE `trackfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `req_var` varchar(30) DEFAULT NULL,
  `is_number` int(11) not null default 0,
  `is_string` int(11) not null default 0,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  unique key same_idx (`req_var`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



*/
