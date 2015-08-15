<?php
class FeedPayloadLoader{
	var $tableName;
	var $whereColumns;
	var $toColumns;
	var $customSQL;
	public function __construct(){ 
		$this->customSQL="";
	}
	public function setCustomSQL($sql){
		$this->customSQL=$sql;
	}
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
		if($this->customSQL==""){
			$query = "select * from ".$this->tableName." where 1 ";
			foreach ($this->whereColumns as $col => $val){
				$query = $query."and $col = '$val' ";
			}
		} else {
			$query=$this->customSQL;
		}
		$dbh->run($query);
		if($dbh->getRowCount()<1){
			return false;
		}
		$x=new XML();
		$x->setIsFeed();
		while (1){
			$entryElement=$x->getDOM()->addChild("entry");
			foreach ($dbh->getColumns() as $col => $val){
				$entryElement->addChild($col,$val);
			}
			if($dbh->fetchNext()==1)
				continue;
			else 
				break;
		}
		return $x->toXML();
	}
}
?>
