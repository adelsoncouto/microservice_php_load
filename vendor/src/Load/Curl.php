<?php

namespace Service\Load;

class Curl{
	private function __construct(){
	}
	public static function get($aUrl){
		$ch=curl_init($aUrl);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
				'Pragma: no-cache',
				'Accept-Encoding: gzip, deflate, br',
				'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4',
				'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
				'Cache-Control: no-cache',
				'Connection: keep-alive'
		));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$response=curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}