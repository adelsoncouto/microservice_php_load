<?php
namespace Service;

use Service\Load\CarregaCss;
use Service\Load\CarregaJavaScript;
use Service\Load\CarregaHtml;

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
		$file = new CarregaJavaScript($this->_request["params"]);
		return $file->carregar();
	}
	
	
	public function css(){
		//define o tipo de retorno
		header('Content-Type:  text/css');
		header('access-control-allow-origin:*');
		header('X-Content-Type-Options: nosniff');
		$file = new CarregaCss($this->_request["params"]);
		return $file->carregar();
	}
	
	public function html(){
		//define o tipo de retorno
		header('Content-Type: text/html');
		header('access-control-allow-origin:*');
		header('X-Content-Type-Options: nosniff');
		$file = new CarregaHtml($this->_request["params"]);
		return $file->carregar();
	}
}
