<?php

class XML{

	var $doc;

	var $simpledom;
	
	var $isFeed;

	var $isError;

	public function __construct(){

		$this->simpledom = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><entry></entry>');

		return $this;

	}

	public function setIsFeed(){

		$this->simpledom = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><feed></feed>');

		return $this;

	}

	public function setIsError(){

		$this->simpledom = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><errors></errors>');

		return $this;

	}

	public function fromXML($xml){

		$this->simpledom = new SimpleXMLElement($xml);

		return $this;

	}

	public function toXML(){

		return $this->simpledom->asXML();

	}

	public function getDOM(){

		return $this->simpledom;

	}

	public function setElement($elementName,$elementValue){

		$e=$this->doc->getElementsByTagName($elementName);

		return $e[0];

	}

}

?>