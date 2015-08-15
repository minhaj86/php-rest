<?php
class support{
	var $method;
	var $payload;
	var $mailId;
	public function __construct(){ }
	public function setMethod($m){
		$this->method=$m;
	}
	public function setPK($pk){
		$this->mailId=$pk;
	}
	public function setPayload($p){
		$this->payload=$p;
	}
	public function exec(){
		if( preg_match('/(POST|GET|PUT|DELETE)/',$this->method) ){  // CHECK IF THE REQUESTED METHOD IS VALID
			if( preg_match('/POST/',$this->method) ){
				// CREATE AN ENTITY (MORE SPECIFICALLY A TABLE ROW) HERE
				preg_match('/<name>(.*)<\/name>/', $this->payload , $name);
				preg_match('/<mobile>(.*)<\/mobile>/', $this->payload , $mobile);
				preg_match('/<email>(.*)<\/email>/', $this->payload , $email);
				preg_match('/<handset_model>(.*)<\/handset_model>/', $this->payload , $handset_model);
				preg_match('/<problem>(.*)<\/problem>/', $this->payload , $problem);
				$to  = "hello@qlog.mobi";//"hello@QLog.mobi";  //$email[1]; 
				$subject = "User Feedback";
				$message = "<html><body>Hi Site Admin, <br /><br /><br />Below is the problem description.<br /><table border=\"0\"><tr><td width=\"200\">Name</td><td width=\"200\">".$name[1]."</td></tr><tr><td>Mobile</td><td>".$mobile[1]."</td></tr><tr><td>Email</td><td>".$email[1]."</td></tr><tr><td>Handset Model</td><td>".$handset_model[1]."</td></tr><tr><td>Problem</td><td>".$problem[1]."</td></tr></table>"."</body></html>";
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: QLog User <qlog-user@snowbean.net>' . "\r\n";
				mail($to,$subject,$message,$headers);
				print $message;
//				$p=new PayloadDispatcher();
//				$p->setPayload($this->payload);
//				$p->setTableName('support');
//				$p->setOpType('insert');
//				$p->dispatchPayload();
//				flushRequestPayload($this->payload);
//			} else if( preg_match('/GET/',$this->method) ){
//				$p=new PayloadLoader();
//				flushOutput( $p->setTableName('support')->addWhereColumn('registration_number', $this->vehicle)->constructPayload());
			}
		} else {
				// THE REQUEST METHOD IS NOT POST/GET/PUT/DELETE . So here handle this.
			throwError($this->method." Method not allowed for this api","405");
		}
	}
}
?>