<?php
namespace Service\Mimifica;

class Mimifica{
	
	public function __construct(){
		
	}
	
	/**
	 * Método responsável por remover as quebras de linhas, tabulação e espaços duplos
	 * @param string $aConteudo
	 * Conteúdo do arquivo a ser mimificado
	 * @return string
	 * Conteúdo mimificado
	 */
	public static function mimificar($aConteudo){
		
		$aConteudo = preg_replace("/\s+/", " ", $aConteudo);
		
		return $aConteudo;
		
	}
}