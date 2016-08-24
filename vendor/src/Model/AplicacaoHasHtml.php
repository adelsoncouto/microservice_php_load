<?php
namespace Service\Model;
use \Service\Banco\ConexaoMySQL;
use \Service\Util\Util;
		
class AplicacaoHasHtml extends Model{
	
	private $id;
	private $aplicacaoId;
	private $htmlId;
	private $ordem;
	private $minificado;
	
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
	public function getHtmlId(){
		return $this->htmlId;
	}
	public function getOrdem(){
		return $this->ordem;
	}
	/**
	 * @param bool $isObject [opcional] se true retorna um DateTime
	 * @return \DateTime
	 */
	public function getMinificado($isObject = false){
		return $isObject?new \DateTime($this->minificado):$this->minificado;
	}
	
	///////SET
	
	public function setId($aId){
		$this->id = $aId;
	}
	public function setAplicacaoId($aAplicacaoId){
		$this->aplicacaoId = $aAplicacaoId;
	}
	public function setHtmlId($aHtmlId){
		$this->htmlId = $aHtmlId;
	}
	public function setOrdem($aOrdem){
		$this->ordem = $aOrdem;
	}
	public function setMinificado($aMinificado){
		$this->minificado = $aMinificado;
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
		return "AplicacaoHasHtml";
	}

	/**
	* Método responsável por retornar o nome completo da classe
	*/
	public function absoluteClassName(){
		return "\Service\Model\AplicacaoHasHtml";
	}
					
	
	
	/**
	* Método responsável por retornar a lista de AplicacaoHasHtml com base no id de aplicacao
	* Em caso de herança deve ser reimplementado
	* @param int $aAplicacaoId id de aplicacao
	* @return \Service\Model\AplicacaoHasHtml[]
	*/
	public function buscarPorAplicacao($aAplicacaoId){
		$sql = "SELECT 
					id as id, 
					aplicacao_id as aplicacaoId, 
					html_id as htmlId, 
					ordem as ordem, 
					minificado as minificado 
				FROM 
					aplicacao_has_html
				WHERE
					aplicacao_id =:id";
		$pst = ConexaoMySQL::getInstance()->prepare($sql);
		$pst->execute([":id"=>$aAplicacaoId]);
		$result = [];
		while($obj = $pst->fetchObject("\Service\Model\AplicacaoHasHtml")){
			$result[] = $obj;
		}
		$pst->closeCursor();
		return $result;
	}
	
	/**
	* Método responsável por retornar a lista de AplicacaoHasHtml com base no id de html
	* Em caso de herança deve ser reimplementado
	* @param int $aHtmlId id de html
	* @return \Service\Model\AplicacaoHasHtml[]
	*/
	public function buscarPorHtml($aHtmlId){
		$sql = "SELECT 
					id as id, 
					aplicacao_id as aplicacaoId, 
					html_id as htmlId, 
					ordem as ordem, 
					minificado as minificado 
				FROM 
					aplicacao_has_html
				WHERE
					html_id =:id";
		$pst = ConexaoMySQL::getInstance()->prepare($sql);
		$pst->execute([":id"=>$aHtmlId]);
		$result = [];
		while($obj = $pst->fetchObject("\Service\Model\AplicacaoHasHtml")){
			$result[] = $obj;
		}
		$pst->closeCursor();
		return $result;
	}
}