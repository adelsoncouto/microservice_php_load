<?php

namespace Service;


class Load implements Client {
	protected $_request;
	
	public function __construct($aRequest) {
		$this->_request = $aRequest;
	}
	
	public function js(){
		//define o tipo de retorno
		header('Content-Type: application/javascript');
		
	}
	
	
	public function css(){
		//define o tipo de retorno
		header('Content-Type: application/json');
		
	}
	
	public function html(){
		//define o tipo de retorno
		header('Content-Type: application/html');
		
		
	}
}
