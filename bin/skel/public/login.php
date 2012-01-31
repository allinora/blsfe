<html>
<head>
	<link rel="stylesheet" type="text/css" href="/fwassets/css/default.css" />
	<link rel="stylesheet" type="text/css" href="/fwassets/css/fb.css" />
</head>
<body class="fbbody">
<?php 
$app_id = FB_APP_ID;
$app_secret = FB_APP_SECRET;
$app_domain = APP_DOMAIN;

blsfe_load_class("BLTransport");
$transport=new BLTransport();
$my_url = "http://" . $_SERVER["HTTP_HOST"] . "/?op=fbsignup2";
//print "URL is $my_url";


$scope="email,publish_stream,user_birthday";

if (empty($_SESSION["fb_access_token"])){
	$code = $_REQUEST["code"];
	if ($op!="fbsignup2") {
	    if(empty($code)) {
	        $dialog_url = "http://www.facebook.com/dialog/oauth?scope=$scope&client_id=" 
	            . $app_id . "&redirect_uri=" . urlencode($my_url);
	
				print "<div class='fbbluebox'>Please wait .. redirectiong to facebook authentication system... </div>";
	
	      echo("<script> self.location.href='" . $dialog_url . "'</script>");
	        exit;
	    }
	}

	if ($op=="fbsignup2") {
	    if(empty($code)) {
	        if ($_REQUEST["error"]) {
	            // User denied access to facebook
				print "User denied access!";
	            exit;
	        }
	        //print "<pre>" . print_r($_REQUEST, true) . "</pre>";
	    }
	}
}



if (empty($_SESSION["fb_access_token"])) {
    $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
        . $app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret="
        . $app_secret . "&code=" . $code;

    $access_token = file_get_contents($token_url);
    $_SESSION["fb_access_token"]=$access_token;
	print "<div class='fbinfobox'>Please wait .. Getting acess token from FB from facebook ... </div>";
} else {
	print "<div class='fbgreybox'>Please wait .. Getting acess token from FB from session... </div>";
    $access_token=$_SESSION["fb_access_token"];
}

//print "<br>Access token is $access_token";
if (empty($_SESSION["fb_user_data"])) {
    $graph_url = "https://graph.facebook.com/me?" . $access_token;
    $user = json_decode(file_get_contents($graph_url));
    $_SESSION["fb_user_data"]=$user;
} else {
    $user = $_SESSION["fb_user_data"];
}

//print "<pre>SESSION: " . print_r($_SESSION, true) . "</pre>";
print "<div class='fbbluebox'>Thankyou .. Redirecting back to homepage ... </div>";
echo("<script> self.location.href='/'</script>");
