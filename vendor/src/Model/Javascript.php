<?php
namespace Service\Model;
use \Service\Banco\ConexaoMySQL;
use \Service\Util\Util;
		
class Javascript extends Model{
	
	private $id;
	private $nome;
	private $versao;
	private $protocolo;
	private $url;
	
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
	public function getNome(){
		return $this->nome;
	}
	public function getVersao(){
		return $this->versao;
	}
	public function getProtocolo(){
		return $this->protocolo;
	}
	public function getUrl(){
		return $this->url;
	}
	
	///////SET
	
	public function setId($aId){
		$this->id = $aId;
	}
	public function setNome($aNome){
		$this->nome = $aNome;
	}
	public function setVersao($aVersao){
		$this->versao = $aVersao;
	}
	public function setProtocolo($aProtocolo){
		$this->protocolo = $aProtocolo;
	}
	public function setUrl($aUrl){
		$this->url = $aUrl;
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
		return "Javascript";
	}

	/**
	* Método responsável por retornar o nome completo da classe
	*/
	public function absoluteClassName(){
		return "\Service\Model\Javascript";
	}
					
	
}