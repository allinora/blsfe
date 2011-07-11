<?php 

# Must install HTTP_Request
# pear install HTTP_Request

function getURL($url, $params=array()){
        //syslog(LOG_DEBUG, "getURL: $url");
        require_once("HTTP/Request.php");
        $req =new HTTP_Request($url);
        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        if (is_array($params["auth_data"])) {
            $req->setBasicAuth($params["auth_data"]["login"], $params["auth_data"]["pass"]);
        }
        if (is_array($params["request_data"])) {
            foreach ($params["request_data"] as $var=>$val){
                $req->addQueryString($var, $val);
            }
        }

        if (is_array($params["headers"])) {
            foreach ($params["headers"] as $var=>$val){
                $req->addHeader($var, $val);
            }
        }


        if (!PEAR::isError($req->sendRequest())) {
            return $req->getResponseBody();
        }
}
