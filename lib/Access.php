<?php
class Access{
	var $acces_info;
	public function __construct(){
		$this->acces_info['user']="";
		$this->acces_info['password']="";
		$this->acces_info['authkey']="";
		$this->acces_info['authorization_basic']="";
		$this->acces_info['cookie']="";
	}
	public function setBasicAuthorization($authoriaztion){
		$this->acces_info['authorization_basic']=$authoriaztion;
	}
	public function setCookie($cookie){
		$this->acces_info['cookie']=$cookie;
	}
	public function setAuthkey($authkey){
		$this->acces_info['authkey']=$authkey;
	}
	public function setCredential($user,$password){
		$this->acces_info['user']=$user;
		$this->acces_info['password']=$password;
	}
	public function login(){
		$dbh = new DBL();
		$dbh->setOpType('select');
		$query = "select * from access where 1 ";
		if( isset($this->acces_info['authorization_basic']) && $this->acces_info['authorization_basic'] != ""){
			$user_pass_base64=base64_decode($this->acces_info['authorization_basic']);
			$arr=preg_split('/:/', $user_pass_base64);
			$user=$arr[0];
			$pass=$arr[1];
			$query = $query."and user = '".$user."' ";
			$query = $query."and value = '".$pass."' ";
			$query = $query."and type = 'password' ";
			$this->acces_info['user']=$user;
			$dbh->run($query);
			if( $dbh->getRowCount() < 1 ){
				return false;
			} 
		} else if( isset($this->acces_info['cookie']) && $this->acces_info['cookie'] != ""){
			return $this->validateCookie($this->acces_info['cookie']);
		} else if( isset($this->acces_info['authkey']) && $this->acces_info['authkey'] != ""){
			$query = $query."and authkey = '".$this->acces_info['authkey']."' ";
			$dbh->run($query);
			if( $dbh->getRowCount() < 1 ){
				return false;
			} 
			$this->acces_info['user']=$dbh->getColumnValue("user"); 
		} else if ( preg_match('/.+/',$this->acces_info['user']) && preg_match('/.+/',$this->acces_info['password']) ){
			$query = $query."and user = '".$this->acces_info['user']."' ";
			$query = $query."and value = '".$this->acces_info['password']."' ";
			$query = $query."and type = 'password' ";
			$dbh->run($query);
			if( $dbh->getRowCount() < 1 ){
				return false;
			}
		} else{
			return false;
		}
		$this->setAuthorizationHeaders();
		return true;
	}
	public function setAuthorizationHeaders(){
		$dbh = new DBL();
		$dbh->setOpType('select');
		$a=new HttpHeader();
		$query = "select * from access where ( user = '".$this->acces_info['user']."' and type ='cookie' )";
		$dbh->run($query);
		if($dbh->getRowCount() > 0) {
			$this->updateCookie();
		} else {
			$this->createCookie();
		}
		$query = "select * from access where ( user = '".$this->acces_info['user']."' and type ='authkey' )";
		$dbh->run($query);
		if($dbh->getRowCount() > 0) $a->set("Auth-Key", $dbh->getColumnValue("value"));
	}
	public function createCookie(){
		$dbh = new DBL();
		$dbh->setOpType('insert');
		$t=new DateTime();
		$t->add(new DateInterval('P10D'));
		$cookie="token=".$this->gen_md5_password(64)."; path=/; expires=".$t->format('D, d-M-Y G:i:s T');
		$query = "insert into access ( user , type , value ) values( '".$this->acces_info['user']."' , 'cookie' , '$cookie' ) ;";
		$dbh->run($query);
		$a=new HttpHeader();
		$a->set("Set-Cookie", $cookie);
	}
	public function updateCookie($cookie=""){
		if($cookie === ""){ 
			$dbh = new DBL();
			$dbh->setOpType('select');
			$query = "select * from access where ( user = '".$this->acces_info['user']."' and type ='cookie' )";
			$dbh->run($query);
			if($dbh->getRowCount() > 0) {
				$cookie=$dbh->getColumnValue("value");
				$storedCookie=$this->parseCookieString($cookie);
				if(isset($storedCookie['expires']) ){
					$t=new DateTime();
					$t->setTimestamp(strtotime($storedCookie['expires']));
					$interval=$t->diff(new DateTime());
					if($interval->format('%R') === '+'){
						$t=new DateTime();
						$t->add(new DateInterval('P10D'));
						$cookie="token=".$this->gen_md5_password(64)."; path=/; expires=".$t->format('D, d-M-Y G:i:s T');
						$dbh1 = new DBL();
						$dbh1->setOpType('update');
						$query1 = "update access set value='".$cookie."' where user='".$this->acces_info['user']."' and type= 'cookie' ;";
						echo $query1;
						$dbh1->run($query1);
					}
				}
			}
		} else{
			$storedCookie=$this->parseCookieString($cookie);
			if(isset($storedCookie['expires']) ){
				$t=new DateTime();
				$t->setTimestamp(strtotime($storedCookie['expires']));
				$interval=$t->diff(new DateTime());
				if($interval->format('%R') === '+'){
					$this->updateCookie();
				}
			}
		}
		$a=new HttpHeader();
		$a->set("Set-Cookie",$cookie);
	}
	public function validateCookie($cookie){
		$dbh = new DBL();
		$dbh->setOpType('select');
		$query = "select * from access where ( user = '".$this->acces_info['user']."' and type ='cookie' ) or ( value like '%".$cookie."%' )";
		$dbh->run($query);
		if( $dbh->getRowCount() < 1 ){
			return false;
		} 
		$toSetCookie=$dbh->getColumnValue("value");
		$this->acces_info['user']=$dbh->getColumnValue("user"); 
		$storedCookie=$this->parseCookieString($dbh->getColumnValue("value"));
		$clientCookie=$this->parseCookieString($cookie);
		if($clientCookie['token'] !== $storedCookie['token']){
			return false;
		} 
		if($storedCookie['expires'] ){
			$t=new DateTime();
			$t->setTimestamp(strtotime($storedCookie['expires']));
			$interval=$t->diff(new DateTime());
			if($interval->format('%R') === '+'){
				return false;
			}
		}
		$this->updateCookie($toSetCookie);
		return true;
	}
	public function parseCookieString($cookieString){
		$cookieArray=preg_split('/; /', $cookieString);
		$cookieParams=array();
		foreach ($cookieArray as $v){
			$name_value=preg_split('/=/', $v);
			$cookieParams[$name_value[0]]=$name_value[1];
		}
		return $cookieParams;
	}
	public function genAuthkey($authkey=0){
		if($authkey == 0){ 
			$authkey=$this->gen_md5_password();
			$dbh = new DBL();
			$dbh->setOpType('update');
			$query = "update access set authkey='".$authkey."' where user='".$this->acces_info['user']."' and password= '".$this->acces_info['password']."' ;";
			$dbh->run($query);
		}
		$a=new HttpHeader();
		$a->set("Auth-Key",$authkey);
	}
	public function gen_md5_password($len = 24){
		return substr(md5(rand().rand()).md5(rand().rand()).md5(rand().rand()).md5(rand().rand()), 0, $len);
	}
}
?>