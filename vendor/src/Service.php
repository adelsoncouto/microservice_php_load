<?php

namespace Service;


class Service implements Client {
	protected $_request;
	protected static $_config;
	
	/**
	 * Método responsável por executar serviço externo
	 * @params string $aName name definido no arquivo de config.json
	 * @params string $aId de da requisição JSONRPC 2.0
	 * @params string $aMethod método da requisição JSONRPC 2.0, exemplo Service.config
	 * @params string $aParams parâmetros da requisição JSONRPC 2.0 deve ser um array chave=>valor
	 * 
	 * @return string retornar um json no padrão do JSONRPC 2.0
	 */
	public static function execute($aName, $aId, $aAuth, $aMethod, $aParams) {
		if (empty ( self::$_config )) {
			self::$_config = json_decode ( file_get_contents ( __DIR__ . "/../../config.json" ), true );
		}
		$request = array ();
		$request ["jsonrpc"] = "2.0";
		$request ["id"] = $aId;
		$request ["auth"] = $aAuth;
		$request ["params"] = $aParams;
		$request ["method"] = $aMethod;
		$response = array ();
		$response ["jsonrpc"] = "2.0";
		$response ["id"] = $aId;
		$response ["error"] = [ ];
		
		foreach ( self::$_config ["service"] as $vService ) {
			if ($vService ["name"] == $aName) {
				if ($vService ["protocolo"] == "tcp") {
					$ch = curl_init ( $vService ["url"] );
					$payload = json_encode ( $request, true );
					curl_setopt ( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
							'Pragma: no-cache',
							'Accept-Encoding: gzip, deflate, br',
							'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4',
							'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
							'Content-Type: application/json;charset=UTF-8',
							'Accept: application/json, text/plain, */*',
							'Cache-Control: no-cache',
							'Connection: keep-alive' 
					) );
					curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
					$response = curl_exec ( $ch );
					curl_close ( $ch );
					return $response;
				}
				if ($vService ["protocolo"] == "script") {
					$result = shell_exec ( $vService ["lineComand"] . " " . $aId . " " . $aAuth . " " . $aMethod . " '" . preg_replace ( "/'/", "\\'", json_encode ( $aParams, true ) ) . "'" );
					return $result;
				}
				break;
			}
		}
		$response ["error"] ["message"] = "Requisição inválida do subservico";
		$response ["error"] ["code"] = - 1;
		return json_encode ( $response, true );
	}
	public function __construct($aRequest) {
		$this->_request = $aRequest;
	}
	public function status() {
		return "OK";
	}
}
