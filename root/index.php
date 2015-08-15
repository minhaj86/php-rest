<?php

ob_start();

include '../lib/HttpHeader.php';
include '../lib/DBL.php';
include '../lib/PayloadLoader.php';
include '../lib/ErrorPayload.php';
include '../lib/FeedPayloadLoader.php';
include '../lib/XML.php';
include '../lib/Access.php';
include '../lib/PayloadDispatcher.php';
include '../lib/Util.php';
include '../lib/KLogger.php';
$log = new KLogger ( "log.txt" , KLogger::DEBUG );

try {

$a=new HttpHeader();
$log->LogDebug("Requested ".$a->get("request_method")." on URI: ".$a->get("request_uri"));
$chars = preg_split('/\//', $a->get("request_uri"), -1, PREG_SPLIT_NO_EMPTY);
// print $chars[0];
$api=$_GET['api'];
$key=$_GET['key'];
//$api=$chars[2];
//$key=$chars[3];
//if ($chars[1] == 'api' ) {
	$ac=new Access();
	if($a->get("Auth-Key")){
		$ac->setAuthkey($a->get("Auth-Key"));
	} else if ($a->get("Authorization")){
		$b=preg_split('/BASIC /', $a->get("Authorization"));
		$ac->setBasicAuthorization($b[1]);
	} else if ($a->get("Cookie")){
		$ac->setCookie($a->get("Cookie"));
	} else if ($a->get("User") && $a->get("Password")){
		$ac->setCredential($a->get("User"),$a->get("Password"));
	} else {
//		header("HTTP/1.1 401 Unauthorized");
		throwError("Not found any Authkey or any Credential.","401");
	}
	
	if( $ac->login() == false ){
//		header("HTTP/1.1 401 Unauthorized");
		throwError("Login Failed due to wrong Authkey or Credential.","401");
	}
	// echo $chars[2];
	if ( preg_match('/.+/',$api) ){
	// 	print "###--".$chars[3]."--##";
			try {
	// 			print "Starting try catch ::::::";
				$log->LogDebug("Calling API: ".$api);
				$log->LogDebug("Recieved Request Body: ".$a->get("request_body"));
				
				if ((include '../api/'.$api.'.php') ) {
					$log->LogDebug("Including API $api");
				} else {
					throwError("Requested API doesn't exist.","404");
				}

//	        	include '../api/'.$api.'.php';
				$b=new $api();
				$b->setMethod($a->get("request_method"));
				$b->setPayload($a->get("request_body"));
				$b->setPK(@$key);
				$a->set('Content-Type', 'text/xml');
				$b->exec();
			} catch (Exception $e) {
//				header("HTTP/1.0 400 Bad Request");
				throwError($e->getMessage(),"400");
			}
	}
} catch (Exception $e) {
	$log->LogError($e->getMessage());
}

ob_flush();


?>
