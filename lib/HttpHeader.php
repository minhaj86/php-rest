<?php

class HttpHeader{

	var $HttpHeaders;

	public function __construct() {

		$headers=headers_list();

		foreach ($headers as $k => $v){

			$s=explode(": ", $v);

			$this->HttpHeaders[$s[0]]=$s[1];

		}

		$this->HttpHeaders["request_body"]=@file_get_contents('php://input');

		$this->HttpHeaders["request_method"]=$_SERVER['REQUEST_METHOD'];

		$this->HttpHeaders["request_uri"]=$_SERVER['REQUEST_URI'];

// 		var_dump($this->HttpHeaders);

 		$headers=apache_request_headers();

		foreach ($headers as $k => $v){

			$this->HttpHeaders[$k]=$v;

		}

		return $this;

	}

	public function get($headerName){

		if(isset($this->HttpHeaders[$headerName]))

			return $this->HttpHeaders[$headerName];

		else 

			return false;

	}

	public function set($headerName,$headerValue){

		header($headerName.": ".$headerValue);

		$this->HttpHeaders[$headerName] = $headerValue;

	}

}

?>