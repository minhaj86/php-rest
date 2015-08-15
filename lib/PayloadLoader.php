<?php
class PayloadLoader{
	var $tableName;
	var $whereColumns;
	var $toColumns;
	public function __construct(){ }
	public function setTableName($t){
		$this->tableName=$t;
		return $this;
	}
	public function addWhereColumn($name,$value){
		$this->whereColumns[$name] = $value;
		return $this;
	}
	public function constructPayload(){
		$dbh = new DBL();
		$dbh->setOpType("select");
		$query = "select * from ".$this->tableName." where 1 ";
		foreach ($this->whereColumns as $col => $val){
			$query = $query."and $col = '$val' ";
		}
		$dbh->run($query);
		if($dbh->getRowCount()<1){
			throwError("Requested resource not found.","404");
			return false;
		}
		$x=new XML();
		foreach ($dbh->getColumns() as $col => $val){
			if($col === 'id')
				continue;
			$x->getDOM()->addChild($col,$val);
		}
		return $x->toXML();
	}
}
?>