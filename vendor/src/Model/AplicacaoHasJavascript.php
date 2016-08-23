<?php
namespace Service\Model;
use \Service\Banco\ConexaoMySQL;
use \Service\Util\Util;
		
class AplicacaoHasJavascript extends Model{
	
	private $id;
	private $aplicacaoId;
	private $javascriptId;
	
	/**
	* Construtor
	* @param int $aId opcional, se informado busca o objeto no banco de dados
	*/
	public function __construct($aId = null){
		if($aId != null){
			ConexaoMySQL::objeto($aId, $this);
		}
	}
		
	public function __destruct(){
	}
	
	///////GET
	
	public function getId(){
		return $this->id;
	}
	public function getAplicacaoId(){
		return $this->aplicacaoId;
	}
	public function getJavascriptId(){
		return $this->javascriptId;
	}
	
	///////SET
	
	public function setId($aId){
		$this->id = $aId;
	}
	public function setAplicacaoId($aAplicacaoId){
		$this->aplicacaoId = $aAplicacaoId;
	}
	public function setJavascriptId($aJavascriptId){
		$this->javascriptId = $aJavascriptId;
	}
	/**
	* Método responsável por retornar uma versão da classe em array
	*/
	public function toArray(){
		$attr = get_object_vars($this);
		foreach($attr as $k=>$v){
			if(preg_match("/^_/",$k)){
				unset($attr[$k]);
			}
		}
		return $attr;
	}
					
	/**
	* Método responsável por retornar o de/para das colunas do objeto com o banco
	* @param string $aAliasTabela aliás da tabela
	* @return string 
	*/
	public function dePara($aAliasTabela = ""){
		$alias = "";
		if(!empty($aAliasTabela)){
			$alias = $aAliasTabela.".";
		}
		$colunas = [];
		$attr = get_object_vars($this);
		foreach($attr as $k=>$v){
			if(preg_match("/^_/",$k)){
				unset($attr[$k]);
			}else{
				$colunas[] = $alias.Util::maiusculaToUnderline($k)." as ".$k;
			}
		}
		return implode(",",$colunas);
	}
					
	/**
	* Método responsável por retornar o nome simples da classe
	*/
	public function className(){
		return "AplicacaoHasJavascript";
	}

	/**
	* Método responsável por retornar o nome completo da classe
	*/
	public function absoluteClassName(){
		return "\Service\Model\AplicacaoHasJavascript";
	}
					
	
	
	/**
	* Método responsável por retornar a lista de AplicacaoHasJavascript com base no id de aplicacao
	* Em caso de herança deve ser reimplementado
	* @param int $aAplicacaoId id de aplicacao
	* @return \Service\Model\AplicacaoHasJavascript[]
	*/
	public function buscarPorAplicacao($aAplicacaoId){
		$sql = "SELECT 
					id as id, 
					aplicacao_id as aplicacaoId, 
					javascript_id as javascriptId 
				FROM 
					aplicacao_has_javascript
				WHERE
					aplicacao_id =:id";
		$pst = ConexaoMySQL::getInstance()->prepare($sql);
		$pst->execute([":id"=>$aAplicacaoId]);
		$result = [];
		while($obj = $pst->fetchObject("\Service\Model\AplicacaoHasJavascript")){
			$result[] = $obj;
		}
		$pst->closeCursor();
		return $result;
	}
	
	/**
	* Método responsável por retornar a lista de AplicacaoHasJavascript com base no id de javascript
	* Em caso de herança deve ser reimplementado
	* @param int $aJavascriptId id de javascript
	* @return \Service\Model\AplicacaoHasJavascript[]
	*/
	public function buscarPorJavascript($aJavascriptId){
		$sql = "SELECT 
					id as id, 
					aplicacao_id as aplicacaoId, 
					javascript_id as javascriptId 
				FROM 
					aplicacao_has_javascript
				WHERE
					javascript_id =:id";
		$pst = ConexaoMySQL::getInstance()->prepare($sql);
		$pst->execute([":id"=>$aJavascriptId]);
		$result = [];
		while($obj = $pst->fetchObject("\Service\Model\AplicacaoHasJavascript")){
			$result[] = $obj;
		}
		$pst->closeCursor();
		return $result;
	}
}