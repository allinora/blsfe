<?php
// A Simple php client for th arj-server
// Requires php with support for pcntl and sockets
if (!function_exists("pcntl_fork") || !function_exists("socket_create")){
	die("Need functions pcntl_fork and socket_create\n");
}



$listen_port 	= (isset($argv[1])) ? $argv[1] : 8888;
$jobs_directory = (isset($argv[2])) ? $argv[2] : '/var/arj/jobs';

# Do not allow limits to these processes
ini_set("max_execution_time",	0);
ini_set("memory_limit", 		-1);


$hostname = php_uname("n");
error_reporting (E_ERROR);
print "Jobs will be fetched from $jobs_directory\n";


$app = function($request) {
	global $GET_VARS;
	$GET_VARS = array(); //intialization is needed for non-forking server
	$body = "";

	// We are getting the RAW HTTP request. Clean it up so we can use
	// what we need.
	print $request . "\n";
	$request = preg_replace('@^(GET|POST|HEAD) /@', '', $request);
	$request = preg_replace("@HTTP.[\W\w]*@", "", $request);
	$req = parse_url(trim($request));
		
	$body.="<pre>" . print_r($req, true) . "</pre>";
	
	if ($req["query"]) {
        $pairs = array();
        $pairs = explode('&', $req["query"]);
        foreach ($pairs as $pair) {
            list($var, $val) = explode('=', $pair);
            if (preg_match("/\[\]$/", $var)) {
                $var = preg_replace("/\[\]$/", '', $var);
                $GET_VARS[$var][] = $val;
            } else {
                $GET_VARS[$var] = $val;
            }
        }
    }

    $function = trim($req["path"]);
	$function = str_replace('/', '', $function);
	

	if (!function_exists($function)) {
		return array(
			'404 NOT FOUND',
			array('Content-Type' => 'text/plain; charset=utf-8'),
			'No such function : ' . $function
		);
	}
	

	if (function_exists($function)) {

		if ($ret = $function()){
			$body = serialize($ret);
		}


		return array(
			'200 OK',
			array('Content-Type' => 'text/plain'),
			$body
		);
	}
};


function ping() {
	global $GET_VARS;
	return "pong " . php_uname("n") .  print_r($GET_VARS, true);
}

function docmd() {
	global $GET_VARS, $jobs_directory;

	$result=array();
	$cmd=$GET_VARS["cmd"];
	$result["request"]=$cmd;

	$cmd_file=$jobs_directory . '/' . $cmd;
	$full_exec=$cmd_file . " " . urldecode(join (" ", $GET_VARS));
	
	if (file_exists($cmd_file)){
		if (is_executable($cmd_file)){
			$result["exec"]=$full_exec;
			$output=`$full_exec`;
			$result["output"]=$output;
		} else {
			$result["output"]="The file for $cmd is not executable";
		}
	} else {
		$result["output"]="No file for $cmd";
	}
	

	return $result;
}





// The rest is the server code. Not really interesting

if (function_exists("pcntl_fork") && function_exists("socket_create")){
	print "Starting Forking Listener\n";

	$defaults = array(
		'Content-Type' => 'text/plain',
		'Server' => 'PHP-Forking-Listener-'.phpversion()
	);



	if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
		echo 'failed to create socket : ', socket_strerror($sock), PHP_EOL;
		exit();
	}

	if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) { 
	    echo socket_strerror(socket_last_error($sock)); 
	    exit; 
	} 


	if (($ret = socket_bind($sock, '0.0.0.0', $listen_port)) < 0) {
		echo 'failed to bind socket : ', socket_strerror($ret), PHP_EOL;
		exit();
	}
	if (($ret = socket_listen($sock, 0)) < 0) {
		echo 'failed to listent to socket : ', socket_strerror($ret), PHP_EOL;
		exit();
	}

	echo 'Server is running on 0.0.0.0:'. $listen_port . ', OK.', PHP_EOL;
	$children=array();

	//  Ignore signals form child processes
	pcntl_signal(SIGCHLD, SIG_IGN);


	$listening = true;
	while ($listening) {
		$conn = socket_accept($sock);
		if ($conn < 0) {
			echo 'error: ', socket_strerror($conn), PHP_EOL;
			exit();
		} else if ($conn === false) {
			usleep(100);
		} else {
			$pid = pcntl_fork();
			if ($pid == -1) {
				echo 'fork failure: ', PHP_EOL;
				exit();
			} else if ($pid == 0) {
				$listening = false;
				socket_close($sock);
				$request = '';
				while (substr($request, -4) !== "\r\n\r\n") {
					$request .= socket_read($conn, 1024);
				}
				list($code, $headers, $body) = $app($request);
				$headers += $defaults;
				if (!isset($headers['Content-Length'])) {
					$headers['Content-Length'] = strlen($body);
				}
				$header = '';
				foreach ($headers as $k => $v) {
					$header .= $k.': '.$v."\r\n";
				}
				socket_write($conn, implode("\r\n", array(
					'HTTP/1.1 '.$code,
					$header,
					$body
				)));
				socket_close($conn);
			} else {
				//$children[] = $pid; // Push the PID of the created child into $children
				print "Forked a process with $pid\n";
				socket_close($conn);
			}
		}
	}
} else {
	print "Starting Blocking Listener\n";
	$socket = stream_socket_server('tcp://0.0.0.0:' . $listen_port, $errno, $errstr);
	if (!$socket) {
		echo $errstr, ' (', $errno,')', PHP_EOL;
	} else {
		$defaults = array(
			'Content-Type' => 'text/plain',
			'Server' => 'PHP-Blocking-Listener-'.phpversion()
		);
		echo 'Server is running on 0.0.0.0:' . $listen_port . ', OK.', PHP_EOL;
		while ($conn = stream_socket_accept($socket, -1)) {
			$request = '';
			while (substr($request, -4) !== "\r\n\r\n") {
				$request .= fread($conn, 1024);
			}
			list($code, $headers, $body) = $app($request);
			$headers += $defaults;
			if (!isset($headers['Content-Length'])) {
				$headers['Content-Length'] = strlen($body);
			}
			$header = '';
			foreach ($headers as $k => $v) {
				$header .= $k.': '.$v."\r\n";
			}
			fwrite($conn, implode("\r\n", array(
				'HTTP/1.1 '.$code,
				$header,
				$body
			)));
			fclose($conn);
		}
		fclose($socket);
	}
}



