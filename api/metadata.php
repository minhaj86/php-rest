<?php
class metadata{
	var $method;
	var $payload;
	var $metadata_type;
	public function __construct(){ }
	public function setMethod($m){
		$this->method=$m;
	}
	public function setPK($pk){
		$this->metadata_type=$pk;
	}
	public function setPayload($p){
		$this->payload=$p;
	}
	public function exec(){
		if( preg_match('/(POST|GET|PUT|DELETE)/',$this->method) ){  // CHECK IF THE REQUESTED METHOD IS VALID
			if( preg_match('/GET/',$this->method) ){
				$p=new FeedPayloadLoader();
				$p->setCustomSQL("SELECT type, value  FROM metadata where type='".$this->metadata_type."';");
				flushOutput( $p->constructPayload());
			} else {
				throwError($this->method." Method not allowed for this api","405");
			}
		} else {
				// THE REQUEST METHOD IS NOT POST/GET/PUT/DELETE . So here handle this.
			throwError($this->method." Method not allowed for this api","405");
		}
	}
}
?>