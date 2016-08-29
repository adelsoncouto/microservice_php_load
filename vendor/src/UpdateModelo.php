<?php

namespace Service;

use Service\Banco\ConexaoMySQL;
use Service\Util\Util;

class UpdateModelo implements Client{
	private $_request;
	public function __construct($aRequest){
		$this->_request=$aRequest;
	}
	public function update(){
		$p=ConexaoMySQL::getInstance()->prepare("SHOW TABLES");
		$p->execute();
		$lista=array();
		while($row=$p->fetch(\PDO::FETCH_ASSOC)){
			$r=array();
			foreach($row as $k=>$v){
				$r[Util::underlineToMaiuscula($k)]=Util::underlineToMaiuscula($v,true);
			}
			$lista[]=$r;
		}
		$p->closeCursor();
		
		$attrs=array();
		
		foreach($lista as $v){
			$class="";
			foreach($v as $vv){
				$class=$vv;
			}
			$atributos=$this->atributo($class);
			$indices=$this->indice($class);
			$attrs[]=$atributos;
			
			file_put_contents(__DIR__."/Model/".$class.".php","<?php
namespace Service\Model;
use \Service\Banco\ConexaoMySQL;
use \Service\Util\Util;
		
class ".$class." extends Model{
	".$this->variaveis($atributos)."
	
	/**
	* Construtor
	* @param int \$aId opcional, se informado busca o objeto no banco de dados
	*/
	public function __construct(\$aId = null){
		if(\$aId != null){
			ConexaoMySQL::objeto(\$aId, \$this);
		}
	}
		
	public function __destruct(){
	}
	
	///////GET
	".$this->get($atributos)."
	
	///////SET
	".$this->set($atributos)."
	/**
	* Método responsável por retornar uma versão da classe em array
	*/
	public function toArray(){
		\$attr = get_object_vars(\$this);
		foreach(\$attr as \$k=>\$v){
			if(preg_match(\"/^_/\",\$k)){
				unset(\$attr[\$k]);
			}
		}
		return \$attr;
	}
					
	/**
	* Método responsável por retornar o de/para das colunas do objeto com o banco
	* @param string \$aAliasTabela aliás da tabela
	* @return string 
	*/
	public function dePara(\$aAliasTabela = \"\"){
		\$alias = \"\";
		if(!empty(\$aAliasTabela)){
			\$alias = \$aAliasTabela.\".\";
		}
		\$colunas = [];
		\$attr = get_object_vars(\$this);
		foreach(\$attr as \$k=>\$v){
			if(preg_match(\"/^_/\",\$k)){
				unset(\$attr[\$k]);
			}else{
				\$colunas[] = \$alias.Util::maiusculaToUnderline(\$k).\" as \".\$k;
			}
		}
		return implode(\",\",\$colunas);
	}
					
	/**
	* Método responsável por retornar o nome simples da classe
	*/
	public function className(){
		return \"".$class."\";
	}

	/**
	* Método responsável por retornar o nome completo da classe
	*/
	public function absoluteClassName(){
		return \"\\Service\\Model\\".$class."\";
	}
					
	".$this->buscaPorParent($class,$atributos)."
	".$this->buscaPorIndice($class,$indices, $atributos)."
}");
			$outro=("

	/**
	* Método responsável por criar a tabela do banco se ela não existir
	*/
	private function criarTabela(){
		\$sql = \"CREATE TABLE IF NOT EXISTS `sistema_rota`(
					`id` int(10) auto_increment,
					`nome` varchar(255) ,
					`view` varchar(255) ,
					`view_url` varchar(255) ,
					`controller` varchar(255) ,
					`controller_url` varchar(255) ,
					`descricao` varchar(255) ,
					PRIMARY KEY(`id`)
				)ENGINE=InnoDB DEFAULT CHARSET=utf8;\";
		\$pst = \"ConexaoMySQL::getInstance()->prepare(\$sql);
		\$pst->execute();
		\$pst->closeCursor();
	}

	/**
	* Método responsável por salvar o novo objeto no banco de dados
	*/
	".$this->salvar($atributos)."

	/**
	* Método responsável por atualizar o objeto no banco
	*/
	".$this->atualizar($atributos)."

	/**
	* Método responsável por deletar um objeto do banco
	*/
	".$this->deletar($atributos)."

	/**
	* Método responsável por consultar um objeto no banco
	*/
	".$this->consultar($atributos)."
		
	/**
	* Método responsável por retornar uma versão da classe em json
	*/
	public function toJson(){
		\$attr = get_object_vars(\$this);
		foreach(\$attr as \$k=>\$v){
			if(preg_match(\"/^_/\",\$k)){
				unset(\$attr[\$k]);
			}
		}
		return json_encode(\$attr, true);
	}
}
");
		}
		return true;
	}
	private function salvar($aAtributos){
		$txt="public function salvar(){
		\$classe = preg_replace(\"/.+\\\\\\\\([^\\\\\\\\]+)\$/\", \"\$1\", __CLASS__);
		\$tabela = Util::maiusculaToUnderline(\$classe);
		\$param = array();
		\$colunas = \"\";
		\$variavel = \"\";
		\$attr = get_object_vars(\$this);
		foreach(\$attr as \$k=>\$v){
			if(preg_match(\"/^_/\",\$k)){
				continue;
			}
			if(\$k == \"id\"){
				continue;	
			}
			
			if(\$colunas != \"\"){
				\$colunas .= \",\";
			}
			
			if(\$variavel != \"\"){
				\$variavel .= \",\";
			}
			\$key = Util::maiusculaToUnderline(\$k);
			
			\$colunas .= \$key;
			\$variavel .= \":\".\$k;
			\$param[\":\".\$k] = \$v;
		}
		\$sql = \"INSERT INTO \".\$tabela.\" (\".\$colunas.\") VALUES (\".\$variavel.\")\";
		\$p = ConexaoMySQL::getInstance()->prepare(\$sql);
		\$p->execute(\$param);
		\$id = ConexaoMySQL::getInstance()->lastInsertId();
		\$p->closeCursor();
		\$this->setId(\$id);
		return \$id;
	}
";
		return $txt;
	}
	private function atualizar($aAtributos){
		$txt="public function atualizar(){
		\$classe = preg_replace(\"/.+\\\\\\\\([^\\\\\\\\]+)\$/\", \"\$1\", __CLASS__);
		\$tabela = Util::maiusculaToUnderline(\$classe);
		\$param = array();
		\$valor = \"\";
		\$attr = get_object_vars(\$this);
		foreach(\$attr as \$k=>\$v){
			if(preg_match(\"/^_/\",\$k)){
				continue;
			}
			if(\$k == \"id\"){
				\$param[\":id\"] = \$v;
				continue;
			}
				
			if(\$valor != \"\"){
				\$valor .= \",\";
			}
				
			\$key = Util::maiusculaToUnderline(\$k);
			\$valor .= \$key.\"=:\".\$k;
			
			\$param[\":\".\$k] = \$v;
		}
		\$sql = \"UPDATE \".\$tabela.\" SET \".\$valor.\" WHERE id=:id\";
		if(empty(\$param[\":id\"])){
			throw new \\Exception(\"Para atualizar o id é obrigatório.\",-1);
		}
		\$p = ConexaoMySQL::getInstance()->prepare(\$sql);
		\$p->execute(\$param);
		\$p->closeCursor();
		ConexaoMySQL::objeto(\$this->getId(), \$this);
		return \$this->getId();
	}";
		return $txt;
	}
	private function deletar($aAtributos){
		$txt="";
		return $txt;
	}
	private function consultar($aAtributos){
		$txt="";
		return $txt;
	}
	private function buscaPorParent($aClasse, $aAtributos){
		$txt="";
		
		$colunas="";
		foreach($aAtributos as $v){
			if(!empty($colunas)){
				$colunas.=", \n\t\t\t\t\t";
			}else{
				$colunas.="\n\t\t\t\t\t";
			}
			$colunas.=$v["field"]." as ".Util::underlineToMaiuscula($v["field"]);
		}
		
		foreach($aAtributos as $v){
			if(preg_match("/_id$/",$v['field'])){
				$parent=preg_replace("/(.+?)_id$/","$1",$v['field']);
				
				$txt.="\n\t"."\n\t/**"."\n\t* Método responsável por retornar a lista de ".$aClasse." com base no id de ".$parent."\n\t* Em caso de herança deve ser reimplementado"."\n\t* @param int \$a".Util::underlineToMaiuscula($parent,true)."Id id de ".$parent."\n\t* @return \\Service\\Model\\".$aClasse."[]"."\n\t*/"."\n\tpublic static function buscarPor".Util::underlineToMaiuscula($parent,true)."(\$a".Util::underlineToMaiuscula($parent,true)."Id){"."\n\t\t\$sql = \"SELECT $colunas \n\t\t\t\tFROM "."\n\t\t\t\t\t".Util::maiusculaToUnderline($aClasse)."\n\t\t\t\tWHERE"."\n\t\t\t\t\t".$v['field']." =:id"."\";"."\n\t\t\$pst = ConexaoMySQL::getInstance()->prepare(\$sql);"."\n\t\t\$pst->execute([\":id\"=>\$a".Util::underlineToMaiuscula($parent,true)."Id]);"."\n\t\t\$result = [];"."\n\t\twhile(\$obj = \$pst->fetchObject(\"\\Service\\Model\\".$aClasse."\")){"."\n\t\t\t\$result[] = \$obj;"."\n\t\t}"."\n\t\t\$pst->closeCursor();\n\t\treturn \$result;"."\n\t}";
			}
		}
		return $txt;
	}
	
	private function buscaPorIndice($aClasse, $aIndices, $aAtributos){
		$txt="";
	
		//monta as colunas
		$colunas="";
		foreach($aAtributos as $v){
			if(!empty($colunas)){
				$colunas.=", \n\t\t\t\t\t";
			}else{
				$colunas.="\n\t\t\t\t\t";
			}
			$colunas.=$v["field"]." as ".Util::underlineToMaiuscula($v["field"]);
		}
		
		//monta os métodos
		
		foreach($aIndices as $v){
			$campo = $v['columnName'];
			
			//se a coluna terminar em _id então já foi feito o método
			if(preg_match("/_id$/",$campo)){
				continue;
			}
			
			//comentado com o objetivo de sobrescrever o método id
			//se terminar em id é padrão
			/*if($campo == "id"){
				continue;
			}*/

			$return = "\\Service\\Model\\".$aClasse;
			
			if($v["nonUnique"]){
				$return .= "[]";
			}
			
			$txt.="\n\t"
					."\n\t/**"
					."\n\t* Método responsável por retornar a lista de ".$aClasse." com base no campo ".$campo
					."\n\t* Em caso de herança deve ser reimplementado"."\n\t* @param string" 
					."\n\t* @return ".$return
					."\n\t*/"."\n\tpublic static function buscarPor".Util::underlineToMaiuscula($campo,true)."(\$a".Util::underlineToMaiuscula($campo,true)."){"
					."\n\t\t\$sql = \"SELECT $colunas \n\t\t\t\tFROM "."\n\t\t\t\t\t".Util::maiusculaToUnderline($aClasse)."\n\t\t\t\tWHERE"."\n\t\t\t\t\t".$campo." =:campo"."\";"
					."\n\t\t\$pst = ConexaoMySQL::getInstance()->prepare(\$sql);"
					."\n\t\t\$pst->execute([\":campo\"=>\$a".Util::underlineToMaiuscula($campo,true)."]);"
					."\n\t\t\$result = [];"
					."\n\t\twhile(\$obj = \$pst->fetchObject(\"\\Service\\Model\\".$aClasse."\")){"
					."\n\t\t\t\$result[] = \$obj;"."\n\t\t}"
					."\n\t\t\$pst->closeCursor();\n\t\treturn \$result;"."\n\t}";
		}
		return $txt;
	}
	
	private function set($aAtributos){
		$txt="";
		foreach($aAtributos as $v){
			$coment="\n\n\t/**\n\t* ".$v["comment"]."\n\t*/";
			$txt.=$coment."\n\tpublic function set".Util::underlineToMaiuscula($v['field'],true)."(\$a".Util::underlineToMaiuscula($v['field'],true)."){\n\t\t\$this->".Util::underlineToMaiuscula($v['field'])." = \$a".Util::underlineToMaiuscula($v['field'],true).";
	}";
		}
		return $txt;
	}
	private function get($aAtributos){
		$txt="";
		
		foreach($aAtributos as $v){
			$coment="\n\n\t/**\n\t* ".$v["comment"];
			$return='$this->'.Util::underlineToMaiuscula($v['field']);
			$param="";
			if($v["type"]=="date"){
				$param='$isObject = false';
				$return='$isObject?new \DateTime('.$return.'):'.$return;
				$coment.='
	 * @param bool $isObject [opcional] se true retorna um DateTime
	 * @return \DateTime';
			}
			if($v["type"]=="datetime"){
				$param='$isObject = false';
				$return='$isObject?new \DateTime('.$return.'):'.$return;
				$coment.='
	 * @param bool $isObject [opcional] se true retorna um DateTime
	 * @return \DateTime';
			}
			if($v["type"]=="time"){
				$param='$isObject = false';
				$return='$isObject?new \DateTime('.$return.'):'.$return;
				$coment.='
	 * @param bool $isObject [opcional] se true retorna um DateTime
	 * @return \DateTime';
			}
			
			$txt.=$coment."\n\t*/\n\tpublic function get".Util::underlineToMaiuscula($v['field'],true)."($param){
		return $return;
	}";
		}
		return $txt;
	}
	private function variaveis($aAtributos){
		$txt="";
		foreach($aAtributos as $v){
			$txt.="\n\tprivate $".Util::underlineToMaiuscula($v['field']).";";
		}
		return $txt;
	}
	private function atributo($aClasse){
		
		$sql="desc ".Util::maiusculaToUnderline($aClasse);
		$sql="show full columns from ".Util::maiusculaToUnderline($aClasse);
		
		$p=ConexaoMySQL::getInstance()->prepare($sql);
		$p->execute();
		$result=array();
		while($_row=$p->fetch(\PDO::FETCH_ASSOC)){
			$_r=array();
			foreach($_row as $k=>$v){
				$_r[Util::underlineToMaiuscula($k)]=$v;
			}
			$result[]=$_r;
		}
		$p->closeCursor();
		return $result;
	}
	
	private function indice($aClasse){
	
		$sql="show index from ".Util::maiusculaToUnderline($aClasse);
	
		$p=ConexaoMySQL::getInstance()->prepare($sql);
		$p->execute();
		$result=array();
		while($_row=$p->fetch(\PDO::FETCH_ASSOC)){
			$_r=array();
			foreach($_row as $k=>$v){
				$_r[Util::underlineToMaiuscula($k)]=$v;
			}
			$result[]=$_r;
		}
		$p->closeCursor();
		return $result;
	}
	
	
}