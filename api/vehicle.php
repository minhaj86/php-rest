<?php
class vehicle{
	var $method;
	var $payload;
	var $vehicle;
	public function __construct(){ }
	public function setMethod($m){
		$this->method=$m;
	}
	public function setPK($pk){
		$this->vehicle=$pk;
	}
	public function setPayload($p){
		$this->payload=$p;
	}
	public function exec(){
		if( preg_match('/(POST|GET|PUT|DELETE)/',$this->method) ){  // CHECK IF THE REQUESTED METHOD IS VALID
			if( preg_match('/POST/',$this->method) ){
				// CREATE AN ENTITY (MORE SPECIFICALLY A TABLE ROW) HERE
				$p=new PayloadDispatcher();
				$p->setPayload($this->payload);
				$p->setTableName('vehicle_details');
				$p->setOpType('insert');
				$p->dispatchPayload();
				flushRequestPayload($this->payload);
			} else if( preg_match('/PUT/',$this->method) ){
				// UPDATE ANY EXISTING ENTITY (MORE SPECIFICALLY A TABLE ROW) HERE
				$p=new PayloadDispatcher();
				$p->setPayload($this->payload);
				$p->setTableName('vehicle_details');
				$p->setOpType('update');
				$p->addWhereColumn('registration_number', $this->vehicle);
				$p->dispatchPayload();
				flushRequestPayload($this->payload);
			} else if( preg_match('/DELETE/',$this->method) ){
				// NOT YET IMPLEMENTED
				throwError($this->method." Method not allowed for this api","405");
			} else if( preg_match('/GET/',$this->method) ){
				$p=new PayloadLoader();
				flushOutput( $p->setTableName('vehicle_details')->addWhereColumn('registration_number', $this->vehicle)->constructPayload());
			}
		} else {
				// THE REQUEST METHOD IS NOT POST/GET/PUT/DELETE . So here handle this.
			throwError($this->method." Method not allowed for this api","405");
		}
	}
}
?>