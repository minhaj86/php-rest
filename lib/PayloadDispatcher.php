<?php
class PayloadDispatcher{
	var $tableName;
	var $whereColumns;
	var $updateColumns;
	var $toColumns;
	var $payload;
	var $opType;
	public function __construct(){ }
	public function setOpType($o){
		$this->opType=$o;
	}
	public function setTableName($t){
		$this->tableName=$t;
		return $this;
	}
	public function setPayload($p){
		$this->payload=$p;
		return $this;
	}
	public function addWhereColumn($name,$value){
		$this->whereColumns[$name] = $value;
		return $this;
	}
	public function addUpdateColumn($name,$value){
		$this->updateColumns[$name] = $value;
		return $this;
	}
	public function dispatchPayload(){
		$x=new XML();
		$x->fromXML($this->payload);
		$children=$x->simpledom->children();
		$dbh = new DBL();
		$dbh->setOpType("update");
		if($this->opType === 'update'){
			$query = "update ".$this->tableName." set ";
			$count=0;
			foreach ($children as $col => $val){
				if($count>0)
					$query = $query.", $col = '$val'  ";
				else
					$query = $query." $col = '$val' ";
				$count++;
			}
			$query = $query." where ";
			$count = 0;
			foreach ($this->whereColumns as $col => $val){
				if($count>0)
					$query = $query." and $col = '$val' ";
				else
					$query = $query." $col = '$val' ";
				$count++;
			}
		} else if($this->opType === 'insert'){
			$query = "insert into ".$this->tableName." ";
			$query_columns=' ( ';
			$query_values=' values( ';
			$count=0;
			foreach ($children as $col => $val){
				if($count>0){
					$query_columns = $query_columns . " , $col ";
					$query_values = $query_values . " , '$val' ";
				} else {
					$query_columns = $query_columns . " $col ";
					$query_values = $query_values . " '$val' ";
				}
				$count++;
			}
			$query_columns=$query_columns.' ) ';
			$query_values=$query_values.' ) ;';
			$query = $query . $query_columns . $query_values;
		}
		print $query;
		$dbh->run($query);
	}
}
?>