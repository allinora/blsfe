<?php 



function postURL($url, $params=array()){
        require_once("HTTP/Request.php");
        $req =& new HTTP_Request($url);
        $req->setMethod(HTTP_REQUEST_METHOD_POST);

        //print "<pre>" . print_r($params, true) . "</pre>";

        if (!$params["options"]["no-multipart"]) {
            $req->addHeader("Content-type","multipart/form-data");
        }
        if (preg_match("/http:\/\/([^\/]*)/", $url, $match)){
            $host=$match[1];
            $req->addHeader("Host",$host);
        }


        if (is_array($params["headers"])) {
            foreach ($params["headers"] as $var=>$val){
                $req->addHeader($var, $val);
            }
        }

        if (is_array($params["request_data"])) {

            foreach ($params["request_data"] as $var=>$val){
                $req->addPostData($var, $val);
            }
        }

        $req->addPostData("Sender", "postURL");


        if (!PEAR::isError($req->sendRequest())) {
            return $req->getResponseBody();
        }
}
