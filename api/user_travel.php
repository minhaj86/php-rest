<?php
class user_travel{
	var $method;
	var $payload;
	var $travel;
	public function __construct(){ }
	public function setMethod($m){
		$this->method=$m;
	}
	public function setPK($pk){
		$this->travel=$pk;
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
				$p->setTableName('vehicle_logs');
				$p->setOpType('insert');
				$p->dispatchPayload();
				flushRequestPayload($this->payload);
			} else if( preg_match('/PUT/',$this->method) ){
				// UPDATE ANY EXISTING ENTITY (MORE SPECIFICALLY A TABLE ROW) HERE
				$p=new PayloadDispatcher();
				$p->setPayload($this->payload);
				$p->setTableName('vehicle_logs');
				$p->setOpType('update');
				$p->addWhereColumn('travel', $this->travel);
				$p->dispatchPayload();
				flushRequestPayload($this->payload);
			} else if( preg_match('/DELETE/',$this->method) ){
				// NOT YET IMPLEMENTED
				throwError($this->method." Method not allowed for this api","405");
			} else if( preg_match('/GET/',$this->method) ){
				$p=new FeedPayloadLoader();
				$p->setCustomSQL("SELECT v.user,v.registration_number,v.registration_due_date,v.make,v.model,v.engine_type,l.date_begin,l.start_odometer_reading,l.start_location,l.date_finish,l.end_odometer_reading,l.end_location,l.kilometre_travelled,l.travel_type,l.purpose_of_the_journey from user_details u, vehicle_details v, vehicle_logs l where u.user=v.user and v.registration_number=l.registration_number and u.user = '".$this->travel."' ;");
				flushOutput( $p->constructPayload());
			}
			//preg_match('/GET/',$this->method) ){
				//$p=new PayloadLoader();
				//print $p->setTableName('vehicle_logs')->addWhereColumn('registration_number', $this->travel)->constructPayload();
		} else {
				// THE REQUEST METHOD IS NOT POST/GET/PUT/DELETE . So here handle this.
			throwError($this->method." Method not allowed for this api","405");
		}
	}
}
?>