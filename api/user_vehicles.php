<?php
class user_vehicles{
	var $method;
	var $payload;
	var $user;
	public function __construct(){ }
	public function setMethod($m){
		$this->method=$m;
	}
	public function setPK($pk){
		$this->user=$pk;
	}
	public function setPayload($p){
		$this->payload=$p;
	}
	public function exec(){
		if( preg_match('/(POST|GET|PUT|DELETE)/',$this->method) ){  // CHECK IF THE REQUESTED METHOD IS VALID
			if( preg_match('/GET/',$this->method) ){
				$p=new FeedPayloadLoader();
				$p->setCustomSQL("SELECT u.user,v.registration_number FROM user_details u, vehicle_details v where u.user=v.user and v.user = '".$this->user."' ;");
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