<?php
namespace Service\Model;
use Service\Banco\ConexaoMySQL;
use Service\Util\Util;

if (strcmp(basename($_SERVER["SCRIPT_NAME"]), basename(__FILE__)) === 0) { exit("Acesso negado"); }

abstract class Model{

	public function __construct(){
	}
	
	public function __destruct(){
	}
	
	/**
	 * Método responsável por salvar o novo objeto no banco de dados
	 */
	public function salvar(){
		$tabela = Util::maiusculaToUnderline($this->className());
		$param = array();
		$colunas = "";
		$variavel = "";
		$attr = $this->toArray();
		foreach($attr as $k=>$v){
			if($k == "id"){
				continue;
			}
				
			if($colunas != ""){
				$colunas .= ",";
			}
				
			if($variavel != ""){
				$variavel .= ",";
			}
			$key = Util::maiusculaToUnderline($k);
				
			$colunas .= $key;
			$variavel .= ":".$k;
			$param[":".$k] = $v;
		}
		$sql = "INSERT INTO ".$tabela." (".$colunas.") VALUES (".$variavel.")";
		$p = ConexaoMySQL::getInstance()->prepare($sql);
		$p->execute($param);
		$id = ConexaoMySQL::getInstance()->lastInsertId();
		$p->closeCursor();
		$this->setId($id);
		return $id;
	}
	
	/**
	 * Método responsável por atualizar o objeto no banco
	 */
	public function atualizar(){
		$tabela = Util::maiusculaToUnderline($this->className());
		$param = array();
		$valor = "";
		$attr = $this->toArray();
		foreach($attr as $k=>$v){
			if($k == "id"){
				$param[":id"] = $v;
				continue;
			}
	
			if($valor != ""){
				$valor .= ",";
			}
	
			$key = Util::maiusculaToUnderline($k);
			$valor .= $key."=:".$k;
				
			$param[":".$k] = $v;
		}
		$sql = "UPDATE ".$tabela." SET ".$valor." WHERE id=:id";
		if(empty($param[":id"])){
			throw new \Exception("Para atualizar o id é obrigatório.",
					-debug_backtrace()[0]["line"]);
		}
		$p = ConexaoMySQL::getInstance()->prepare($sql);
		$p->execute($param);
		ConexaoMySQL::objeto($this->getId(), $this);
		$p->closeCursor();
		return $this->getId();
	}
	
	/**
	 * Método responsável por deletar um objeto do banco
	 */
	public function deletar(){
		$sql = "DELETE FROM ".Util::maiusculaToUnderline($this->className()).
		" WHERE id=:id";
		$p = ConexaoMySQL::getInstance()->prepare($sql);
		$p->execute([':id'=>$this->getId()]);
		$p->closeCursor();
		return $this;
	}
	
	/**
	 * Método responsável por listar com base em um sql
	 * @param string $aSql sql para ser usando no prepare statement
	 * @param array $aPrametros paramentros para o execute exemplo: [":id"=>1]
	 * @return  $this->absoluteClassName()[] [obj,obj,...]
	 */
	public function listarPorSql($aSql, $aPrametros){
		$p = ConexaoMySQL::getInstance()->prepare($aSql);
		$p->execute($aPrametros);
		$result = array();
		while($obj = $p->fetchObject($this->absoluteClassName())){
			$result[] = $obj;
		}
		$p->closeCursor();
		return $result;
	}
	
	/**
	 * Método responsável por listar com base em parametros
	 * @param string $aWhere use sempre o padrão Camel Case "nomeSobrenome" 
	 * 			por exemplo: "id=:id and sobreNome like :sobreNome"
	 * @param array $aPrametros paramentros para o execute 
	 * 			exemplo: [":id"=>1,":sobreNome"=>"%Silva%"]
	 * @param string $aOrderBy exemplo "id desc, nome" ordena pelo id 
	 * 			decrescente e pelo nome crescente
	 * @param string $aLimite exemplo "5,8" limita do 5 ao 8
	 * @return $this->absoluteClassName()[] [obj,obj,...]
	 */
	public function listarPorWhere(
			$aWhere = null, 
			$aPrametros = null, 
			$aOrderBy = null,
			$aLimite = null
		){
		
		//sql básico
		$sql = "SELECT id FROM ".Util::maiusculaToUnderline($this->className());
		
		//verifica se tem where
		if(!empty($aWhere)){
			$where = Util::maiusculaToUnderline($aWhere);
			$sql .= " WHERE ".$where;
		}
		
		//verifica se tem order by
		if(!empty($aOrderBy)){
			$sql .= " ORDER BY ".$aOrderBy;
		}
		
		//verifica se tem limite
		if(!empty($aLimite)){
			$sql .= " LIMIT ".$aLimite;
		}
		
		//prepara o sql
		$p = ConexaoMySQL::getInstance()->prepare($sql);
		
		//verifica se tem parametros
		if(!empty($aPrametros)){
			$param = array();
			foreach ($aPrametros as $k => $v){
				$param[Util::maiusculaToUnderline($k)] = $v;
			}
			$p->execute($param);
		}else{
			$p->execute();
		}
		
		$result = array();
		$class = $this->absoluteClassName();
		while($row = $p->fetch(\PDO::FETCH_ASSOC)){
			$result[] = new $class($row["id"]);
		}
		$p->closeCursor();
		return $result;
		
	}
	
	
	/**
	 * Método responsável por retornar uma versão da classe em json
	 */
	public function toJson(){
		return json_encode($this->toArray(), true);
	}
	
	/**
	 * Método responsável por alimentar o objeto atual com o id informado
	 * @param int $aId id do objeto
	 */
	public function buscarPorId($aId){
		ConexaoMySQL::objeto($aId, $this, $this->className());
	}
	
	/**
	 * Método responsável por retornar o id da classe no banco
	 */
	abstract public function getId();
	
	/**
	 * Método responsável por retornar os parametros da classe em forma de array
	 */
	abstract public function toArray();
	
	/**
	 * Método responsável por retornar o nome simples da classe;
	 */
	abstract public function className();
	
	/**
	 * Método responsável por retornar o nome completo da classe;
	 */
	abstract public function absoluteClassName();
}
	