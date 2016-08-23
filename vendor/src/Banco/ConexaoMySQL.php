<?php

namespace Service\Banco;

use \PDO;
use \Service\Util\Util;

class ConexaoMySQL{
	private static $instance;
	private function __construct(){
	}
	public static function getInstance(){
		if(!isset(self::$instance)){
			$cfg=json_decode(file_get_contents(__DIR__."/../../config.json"),true);
			$mysql=$cfg["mysql"];
			$host=empty($mysql["host"])?"localhost":$mysql["host"];
			$port=empty($mysql["port"])?"3306":$mysql["port"];
			$user=empty($mysql["user"])?"root":$mysql["user"];
			$pw=empty($mysql["pw"])?"":$mysql["pw"];
			$db=empty($mysql["db"])?"load":$mysql["db"];
			
			self::$instance=new PDO("mysql:host=$host;dbname=$db;port=$port",$user,$pw,array(
					PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"
			));
			self::$instance->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			self::$instance->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);
			self::$instance->setAttribute(PDO::ATTR_PERSISTENT,true);
		}
		
		return self::$instance;
	}
	
	/**
	 * Método responsável por retornar um objeto pelo id
	 *
	 * @param int $aId
	 *        	o id do objeto
	 * @param object $aObjeto
	 *        	o objeto
	 * @param string $aTabela
	 *        	a tabela se não informado será usado o nome da
	 *        	classe
	 */
	public static function objeto($aId,$aObjeto,$aTabela=null){
		if($aTabela==null){
			$classe=$aObjeto->className();
			$aTabela=Util::maiusculaToUnderline($classe);
		}else{
			$aTabela=Util::maiusculaToUnderline($aTabela);
		}
		$sql="select * from ".$aTabela." where id=:id";
		
		$p=ConexaoMySQL::getInstance()->prepare($sql);
		$p->execute([
				":id"=>$aId
		]);
		while($row=$p->fetch(\PDO::FETCH_ASSOC)){
			foreach($row as $k=>$v){
				$metodo="set".Util::underlineToMaiuscula($k);
				if(method_exists($aObjeto,$metodo)){
					$aObjeto->$metodo($v);
				}
			}
		}
		$p->closeCursor();
	}
}
