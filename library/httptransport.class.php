<?php
class HttpTransport {
	function http_post_request($url, $params, $http_params = array()) {
		return $this->http_request($url,'POST', $params, $http_params);
	}

	function http_get_request($url, $params, $http_params = array()) {
		return $this->http_request($url,'GET', $params, $http_params);
	}


	function http_request($url, $method, $params, $http_params=array()) {

		if (defined('ALWAYS_REQUIRE_COMPANY_ID')){
			if (!isset($params['request_data']['company_id'])){
				if (isset($_SESSION['company_id'])){
					$params['request_data']['company_id'] = $_SESSION['company_id'];
				} else {
					$params['request_data']['company_id'] = 0;
					// throw new Exception("Cannot call backend without the company_id in $url<pre>" . print_r($params, true) . "</pre>");
				}
			} else {
				$_SESSION['company_id'] = $params['request_data']['company_id'] ;
			}
			//print "<pre>$url" . print_r($params, true) . "</pre>";
		}
		// Http stream options
		// See http://www.php.net/manual/en/context.http.php
		$headers = array();
		$headers['Accept-language'] = 'en';

		if (isset($params["headers"])) {
			foreach($params["headers"] as $key => $val){
				$headers[$key] = $val;
			}
		}

		$header_str = "";

		foreach($headers as $key => $val){
			$header_str .= $key . ": " . $val . "\r\n";
		}

	    $options = array( 
	          'http' => array( 
	            'method' => $method, 
	            'header' => $header_str
	         ) 
	   ); 

		if (isset($http_params["timeout"])){
			// This can be used to set a long timeout when called from the CLI based daemon
			$options["http"]["timeout"] = $http_params["timeout"];
		}

		if (isset($params['request_data']) && is_array($params['request_data'])){
			$url .= '?';

			foreach($params['request_data'] as $var => $val){
				if (empty($var)) {
					continue;
				}
				if (is_array($val)) {
					// print "$var: <pre>" . print_r($val, true) . "</pre>";
					foreach($val as $_array_key => $_array_value ) {
						 // print "Setting $var  [$_array_key] to $_array_value<br>";
						$url .= $var . '[' . $_array_key .  ']=' . urlencode($_array_value) . '&';
					}

				} else {
					// print "Setting $var to $val<br>";
					$url .= $var . '=' . urlencode($val) . '&';

				}
			}
		}
		//print "Calling $url<br>";
	    $context = stream_context_create($options); 
	    $response = file_get_contents($url, false, $context);
		if (!$response) {
			return false;
		}

		$result = trim($response);
		
		if (substr($result, 0, 30) == "SCGIServer Uncaught Exception:") {
			$this->sendError("danger", "Backend", $response);
		}
		
		return $result;
	}
	
	function sendError($type, $title, $text){
		syslog(LOG_ERR, "blsfe: error: $type: $title: $text" );
		print '<html><head><link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css"/></head>';
		print '<body>';
		print '<div class="jumbotron alert alert-' . $type . '">';
		print "<h3>$title</h3>";
		print $text;
		print "</div></body></html>";
		exit;
     }
}
