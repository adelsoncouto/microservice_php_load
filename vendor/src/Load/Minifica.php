<?php
namespace Service\Load;

class Minifica{
	
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
		
		//remove comentário inline
		$aConteudo = preg_replace("/\/\/.+/","", $aConteudo);
		
		//remove quebras de linhas e tabulações
		$aConteudo = preg_replace("/\s+/","", $aConteudo);
		
		//remove comentários em bloco
		$aConteudo = preg_replace("/\/\*.+?\*\//","", $aConteudo);
		
		return $aConteudo;
		
	}
}