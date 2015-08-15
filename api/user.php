<?php
require_once 'user_access.php';
class user{
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
			if( preg_match('/POST/',$this->method) ){
				// CREATE AN ENTITY (MORE SPECIFICALLY A TABLE ROW) HERE
				$p=new PayloadDispatcher();
				$p->setPayload($this->payload);
				$p->setTableName('user_details');
				$p->setOpType('insert');
				$p->dispatchPayload();
				preg_match('/<user>.*<\/user>/', $this->payload , $user_tag);
				$pac=new user_access();
				$pac->setMethod("POST");
				$pac->setPK($this->user);
				$password=rand(100000, 999999);
 				$pac->setPayload("<entry>".$user_tag[0]."<type>password</type><value>$password</value></entry>");
				$pac->exec();
				preg_match('/<email_address>(.*)<\/email_address>/', $this->payload , $email);
				preg_match('/<user>(.*)<\/user>/', $this->payload , $user);
				preg_match('/<first_name>(.*)<\/first_name>/', $this->payload , $firstName);
				preg_match('/<last_name>(.*)<\/last_name>/', $this->payload , $lastName);
				$to  = $email[1]; 
				$subject = "Greetings from qLog";
				$message = "<html><body>Hi ".$firstName[1]." ".$lastName[1]."<br />Congratulations to your qLog Service. Below is your credential:<br />Username: ".$user[1]."<br />Password: ".$password."</body></html>";
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: QLOG <hello@QLog.mobi>' . "\r\n";
				mail($to,$subject,$message,$headers);
				flushRequestPayload($this->payload);
			} else if( preg_match('/PUT/',$this->method) ){
				// UPDATE ANY EXISTING ENTITY (MORE SPECIFICALLY A TABLE ROW) HERE
				$p=new PayloadDispatcher();
				$p->setPayload($this->payload);
				$p->setTableName('user_details');
				$p->setOpType('update');
				$p->addWhereColumn('user', $this->user);
				$p->dispatchPayload();
				flushRequestPayload($this->payload);
			} else if( preg_match('/DELETE/',$this->method) ){
				// NOT YET IMPLEMENTED
				throwError($this->method." Method not allowed for this api","405");
			} else if( preg_match('/GET/',$this->method) ){
				$p=new PayloadLoader();
				flushOutput($p->setTableName('user_details')->addWhereColumn('user', $this->user)->constructPayload());
			}
		} else {
				// THE REQUEST METHOD IS NOT POST/GET/PUT/DELETE . So here handle this.
			throwError($this->method." Method not allowed for this api","405");
		}
	}
}
?>