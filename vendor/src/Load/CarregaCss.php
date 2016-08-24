<?php

namespace Service\Load;

use Service\Model\Html;
use Service\Model\Css;
use Service\Model\Aplicacao;
use Service\Model\AplicacaoHasCss;

/**
 * Classe responsável por carregar os arquivos css
 *
 * @author Adelson Silva Couto
 *        
 */
class CarregaCss{
	
	/**
	 * Nome da aplicação que está solicitando o css
	 * 
	 * @var string
	 */
	private $aplicacao;
	
	/**
	 *
	 * @param string $aAplicacaoNome
	 *        	Nome da aplicação
	 */
	public function __construct($aAplicacaoNome){
		$this->aplicacao=$aAplicacaoNome;
	}
	
	/**
	 * Método responsável por carregar os arquivos e minificar
	 * @throws \Exception
	 * @return string
	 */
	public function carregar(){
		$app = new Aplicacao();
		$listApp = $app->listarPorWhere("nome=:nome",[":nome"=>$this->aplicacao]);
		
		if(empty($listApp)){
			throw new \Exception("Aplicação não cadastrada", -1);
		}
		
		$appCss = new AplicacaoHasCss();
		$listAppCss = $appCss->listarPorWhere("aplicacaoId=:appId",[":appId"=>$listApp[0]->getId()],"ordem");
		
		$file = "";
		
		foreach ($listAppCss as $vCss){
			$css = new Css($vCss->getCssId());
			
			if($css->getProtocolo() == 'tcp'){
				$file .= Curl::get($css->getUrl());
			}
			
			if($css->getProtocolo() == 'file'){
				$file .= file_get_contents($css->getUrl());
			}
		}
		
		$file = Minifica::mimificar($file);
		
		return $file;
		
	}
}