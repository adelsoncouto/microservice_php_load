<?php
namespace Service\Model;
use \Service\Banco\ConexaoMySQL;
use \Service\Util\Util;
		
class AplicacaoHasCss extends Model{
	
	private $id;
	private $aplicacaoId;
	private $cssId;
	
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
	public function getCssId(){
		return $this->cssId;
	}
	
	///////SET
	
	public function setId($aId){
		$this->id = $aId;
	}
	public function setAplicacaoId($aAplicacaoId){
		$this->aplicacaoId = $aAplicacaoId;
	}
	public function setCssId($aCssId){
		$this->cssId = $aCssId;
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
		return "AplicacaoHasCss";
	}

	/**
	* Método responsável por retornar o nome completo da classe
	*/
	public function absoluteClassName(){
		return "\Service\Model\AplicacaoHasCss";
	}
					
	
	
	/**
	* Método responsável por retornar a lista de AplicacaoHasCss com base no id de aplicacao
	* Em caso de herança deve ser reimplementado
	* @param int $aAplicacaoId id de aplicacao
	* @return \Service\Model\AplicacaoHasCss[]
	*/
	public function buscarPorAplicacao($aAplicacaoId){
		$sql = "SELECT 
					id as id, 
					aplicacao_id as aplicacaoId, 
					css_id as cssId 
				FROM 
					aplicacao_has_css
				WHERE
					aplicacao_id =:id";
		$pst = ConexaoMySQL::getInstance()->prepare($sql);
		$pst->execute([":id"=>$aAplicacaoId]);
		$result = [];
		while($obj = $pst->fetchObject("\Service\Model\AplicacaoHasCss")){
			$result[] = $obj;
		}
		$pst->closeCursor();
		return $result;
	}
	
	/**
	* Método responsável por retornar a lista de AplicacaoHasCss com base no id de css
	* Em caso de herança deve ser reimplementado
	* @param int $aCssId id de css
	* @return \Service\Model\AplicacaoHasCss[]
	*/
	public function buscarPorCss($aCssId){
		$sql = "SELECT 
					id as id, 
					aplicacao_id as aplicacaoId, 
					css_id as cssId 
				FROM 
					aplicacao_has_css
				WHERE
					css_id =:id";
		$pst = ConexaoMySQL::getInstance()->prepare($sql);
		$pst->execute([":id"=>$aCssId]);
		$result = [];
		while($obj = $pst->fetchObject("\Service\Model\AplicacaoHasCss")){
			$result[] = $obj;
		}
		$pst->closeCursor();
		return $result;
	}
}