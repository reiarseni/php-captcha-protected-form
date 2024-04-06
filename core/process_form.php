<?php

//Require the verification file
require_once 'captcha_verify.php';

function isJson(){
	   // Check for JSON decoding errors 
	if (json_last_error() === JSON_ERROR_NONE) { 
		// JSON is valid 
		return true; 
		// Now you can use $jsonData as a PHP object or array 
	} else { 
		// JSON is invalid 
		return false; 
	} 
}

if($_SERVER['REQUEST_METHOD'] != "POST" ){
	header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo(json_encode(array("status"=>"error","code" => "405", "details"=>"405 Method Not Allowed")));
    exit();
};

// Takes raw data from the request
$obj = json_decode(file_get_contents('php://input'));

// Verify if request is in JSON format
if(!isJson()){
	header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo(json_encode(array( "status"=> "error","code" => "405", "details"=>"405 Method Not Allowed")));
    exit();
};

$serial = filter_var($obj->serial, FILTER_DEFAULT ); 
$captcha_num = filter_var($obj->captcha_num, FILTER_DEFAULT );

//call the function by binding it to a variable
$verify_captcha = json_decode(verifyCaptcha($captcha_num), true); 

$captcha_status = $verify_captcha['captcha_status'];
$status = "";
$imeiStatus = "";

if ($captcha_status == 200) {
	//If captcha verification is Successful
	//echo $verify_captcha;
	$status = "success";
	$imeiStatus = getDataFromApi($serial);  
} else {
	//If Unsuccessful
	//echo $verify_captcha;
	$status = "error";
}

function getDataFromApi($serial){
	//Make iFreeiCloud Api call with your credentials
	//...
	
	$myCheck["service"] = 238;
	$myCheck["imei"] = $serial;
	$myCheck["key"] = "AAA-BBB-CCC-DDD-EEE-FFF-GGG-HHH";
	$ch = curl_init("https://api.ifreeicloud.co.uk");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $myCheck);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$myResult = json_decode(curl_exec($ch));
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($httpcode != 200) {
		$cleaned_response = "<hr><pre>Error: HTTP Code ".$httpcode." </pre><hr>";
	} elseif($myResult->success !== true) {
		$cleaned_response = "<hr><pre>Error: ".$myResult->error." </pre><hr>";
	} else {
		$cleaned_response = $myResult->response;
	}
	
    return $cleaned_response; 
}
             
header('HTTP/1.1 '.$captcha_status);
header('Content-Type: application/json');
echo( json_encode(array("status" => $status,"code" => $captcha_status, "details" => $verify_captcha['captcha_message'], "imei_status" => $imeiStatus)) );
exit();