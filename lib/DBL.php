<?php
class DBL{
	var $connectionParams;
	var $resultArray;
	var $resultRowCount;
	var $opType;
	var $dbconf;
	var $result;
	public function __construct(){
		$this->resultRowCount=0;
		$this->dbconf = parse_ini_file("../conf/db.ini");
		$this->connectionParams["database"]=$this->dbconf['DBNAME'];//$db;
		$this->connectionParams["host"]=$this->dbconf['DBHOST'];//$h;
		$this->connectionParams["username"]=$this->dbconf['DBUSER'];//$u;
		$this->connectionParams["password"]=$this->dbconf['DBPASS'];//$p;
	}
	public function setHost($h){
		$this->connectionParams["host"]=$this->dbconf['DBHOST'];//$h;
		return $this;
	}
	public function setUsername($u){
		$this->connectionParams["username"]=$this->dbconf['DBUSER'];//$u;
		return $this;
	}
	public function setPassword($p){
		$this->connectionParams["password"]=$this->dbconf['DBPASS'];//$p;
		return $this;
	}
	public function setDatabase($db){
		$this->connectionParams["database"]=$this->dbconf['DBNAME'];//$db;
		return $this;
	}
	public function setOpType($o){
		$this->opType=$o;
		return $this;
	}
	public function run($query){
		$mysqli = new mysqli($this->connectionParams["host"], $this->connectionParams["username"], 
		                     $this->connectionParams["password"], $this->connectionParams["database"]);
		if ($mysqli->connect_error) {
//		echo "ERROR: ".$mysqli->connect_error;
//			include "Util.php";
			throwError($mysqli->connect_error,"400");
//			header("HTTP/1.0 400 Bad Request");
//			die('Connect Error (' . $mysqli->connect_errno . ') '
//			. $mysqli->connect_error);
		}
		if ($this->result = $mysqli->query($query)) {
//		echo "Result Valid. No of rows = ".$this->result->num_rows;
			if( isset($this->result->num_rows) or $this->result->num_rows !=0 ) {
//			echo "NUM_ROWS isset.";
				$this->resultRowCount=$this->result->num_rows;
			} else {
				$this->resultRowCount=0;
			}
			if($this->opType == 'select' ){
				if($this->result->num_rows > 0){
					$array = $this->result->fetch_array();
					foreach ($array as $k => $v){
						if(!preg_match('/^[0-9]+$/', $k)){
							$this->resultArray[$k] = $v ;
						}
					}
				} else{
					return false;
				}
// 				$result->close();
			}
		} else{
			throwError($mysqli->error,"400");
//			header("HTTP/1.0 400 Bad Request");
//			die($mysqli->error);
		}
		$mysqli->close();
		return $this;
	}
	public function fetchNext(){
		if($this->opType == 'select' ){
			if( $array = $this->result->fetch_array() ){
				;
				foreach ($array as $k => $v){
					if(!preg_match('/^[0-9]+$/', $k)){
						$this->resultArray[$k] = $v ;
					}
				}
			} else{
				return false;
			}
		}
		return 1;
	}
	public function getRowCount(){
		return $this->resultRowCount;
	}
	public function getColumnValue($column){
		if(isset($this->resultArray[$column])) 
			return $this->resultArray[$column];
		else 
			return false;
	}
	public function getColumns(){
		return $this->resultArray;
	}
}
?>
