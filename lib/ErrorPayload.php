<?php
class ErrorPayload{
	var $errorMessage;
	public function __construct(){ }
	public function setError($e){
		$this->errorMessage=$e;
		return $this;
	}
	public function constructPayload(){
		$x=new XML();
		$x->setIsError();
		$x->getDOM()->addChild("error",$this->errorMessage);
		return $x->toXML();
	}
}
?>