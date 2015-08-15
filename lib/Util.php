<?php
function throwError($errorMessage,$errorCode){
	$log = new KLogger ( "log.txt" , KLogger::DEBUG );
	$log->LogError("HTTP ERROR: Status Code: ".$errorCode."  Error Message: ".$errorMessage);
	$log->LogDebug("Creating Error Payload");
	$e=new ErrorPayload();
	$log->LogDebug("Setting Message in Error Payload");
	$e->setError($errorMessage);
	$log->LogDebug("Construct Error Payload as XML ");
	$p=$e->constructPayload();
	$log->LogDebug("Setting Error Code $errorCode");
	header("HTTP/1.1 ".$errorCode);
	header("Content-Type: text/xml");
	$log->LogDebug("Dying with payload ========= ".$p);
	die($p);
}

function flushOutput($output){
	$log = new KLogger ( "log.txt" , KLogger::DEBUG );
	$log->LogInfo("HTTP Response: ".$output);
	print $output;
}

function flushRequestPayload($requestPayload){
	$log = new KLogger ( "log.txt" , KLogger::DEBUG );
	$log->LogInfo("Request Payload: ".$requestPayload);
	print $requestPayload;
}

?>