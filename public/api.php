<?php
/**
* A única função desse arquivo é definir conrigurações básicas
* ler os parametros de requisição
* instanciar o core, chamar passando os parametros para o mesmo
*/

//logs
error_reporting(0);
ini_set("display_errors",0);
ini_set("display_startup_errors", 0);
ini_set("html_errors", true);
ini_set("log_errors", true);
ini_set("ignore_repeated_errors", false);
ini_set("ignore_repeated_source", false);
ini_set("report_memleaks", true);
ini_set("track_errors", true);
ini_set("docref_root", 0);
ini_set("docref_ext", 0);
ini_set("error_log", __DIR__."/../logs/php_error.log");
ini_set("error_reporting", 999999999);
ini_set("log_errors_max_len", 0);

//define o timezone
date_default_timezone_set("America/Sao_Paulo");

//trata os erros como exceptions
function exception_error_handler($errno, $errstr, $errfile, $errline){
	throw new Exception($errstr, 0);
}
set_error_handler("exception_error_handler", E_ALL);

//importa o autoload
require_once __DIR__.'/../vendor/autoload.php';

//tenta ler payload
$request = json_decode(file_get_contents("php://input"), true);

//tenta ler os request por parametros se não encontrar nada no payload
if(empty($request)){
	$request = $_REQUEST;
}

//tenta ler linha de comando
if(empty($request)){
	$request = empty($argv)?null:[
		"jsonrpc"=>"2.0",
		"id"=>empty($argv[1])?"":$argv[1],
		"auth"=>empty($argv[2])?"":$argv[2],
		"method"=>empty($argv[3])?"":$argv[3],
		"params"=>empty($argv[4])?"":json_decode($argv[4],true),
	];
}

//se não tiver encontrado nada então a requisição é inválida
if(empty($request)){
	$response = array();
	$response["jsonrpc"] = "2.0";
	$response["id"] = null;
	$response["error"] = [];
	$response["error"]["message"] = "Requisição inválida";
	$response["error"]["code"] = -1;
	echo json_encode($response, true);
	exit();
}

//verifica os campos obrigatórios do padrão json
$requisicaoValida = true;
if(!isset($request["id"])){
	$requisicaoValida = false;
}

if(!isset($request["auth"])){
	$requisicaoValida = false;
}

if(!isset($request["method"])){
	$requisicaoValida = false;
}

if(!isset($request["params"])){
	$requisicaoValida = false;
}

if(!$requisicaoValida){
	$response = array();
	$response["jsonrpc"] = "2.0";
	$response["id"] = null;
	$response["error"] = [];
	$response["error"]["message"] = "Requisição inválida, siga o padrão JSONRPC 2.0 ".json_encode($request);
	$response["error"]["code"] = -1;
	echo json_encode($response, true);
	exit();
}

//verifica se o method é valido
$servicoRequest = explode(".",$request["method"]);
if(count($servicoRequest) !== 2){
	$response = array();
	$response["jsonrpc"] = "2.0";
	$response["id"] = null;
	$response["error"] = [];
	$response["error"]["message"] = "Requisição inválida, o method deve seguir o padrão Servico.metodo";
	$response["error"]["code"] = -1;
	echo json_encode($response, true);
	exit();
}

//tudo ok
try{	
	$classe = "Service\\".$servicoRequest[0];
	$metodo = $servicoRequest[1];
	$obj = new $classe($request);
	$response = array();
	echo $obj->$metodo();	
}catch(\Exception $e){
	$response = array();
	$response["jsonrpc"] = "2.0";
	$response["id"] = empty($request["id"])?null:$request["id"];
	$response["error"] = [];
	$response["error"]["message"] = $e->getMessage();//"Requisição inválida";
	$response["error"]["code"] = -1;
	echo json_encode($response, true);
}
