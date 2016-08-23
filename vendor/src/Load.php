<?php
namespace Service;

use Service\Mimifica\Mimifica;

class Load implements Client {
	protected $_request;
	
	public function __construct($aRequest) {
		$this->_request = $aRequest;
	}
	
	
	public function js(){
		//define o tipo de retorno
		header('Content-Type: text/javascript; charset=UTF-8');
		header('access-control-allow-origin:*');
		header('X-Content-Type-Options: nosniff');
		
		return $file;
	}
	
	
	public function css(){
		//define o tipo de retorno
		header('Content-Type:  text/css');
		header('access-control-allow-origin:*');
		header('X-Content-Type-Options: nosniff');
		return "";
	}
	
	public function html(){
		//define o tipo de retorno
		header('Content-Type: text/html');
		header('access-control-allow-origin:*');
		header('X-Content-Type-Options: nosniff');
		
		return "";
	}
}
