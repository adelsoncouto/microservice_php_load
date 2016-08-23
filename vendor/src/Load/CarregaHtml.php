<?php

namespace Service\Load;

use Service\Model\AplicacaoHasHtml;
use Service\Model\Html;
use Service\Model\Aplicacao;

/**
 * Classe responsável por carregar os arquivos html
 *
 * @author Adelson Silva Couto
 *        
 */
class CarregaHtml{
	
	/**
	 * Nome da aplicação que está solicitando o html
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
	 * 
	 * @throws \Exception
	 * @return string
	 */
	public function carregar(){
		$app=new Aplicacao();
		$listApp=$app->listarPorWhere("nome=:nome",[
				":nome"=>$this->aplicacao
		]);
		
		if(empty($listApp)){
			throw new \Exception("Aplicação não cadastrada",-1);
		}
		
		$appHtml=new AplicacaoHasHtml();
		$listAppHtml=$appHtml->listarPorWhere("aplicacaoId=:appId",[
				":appId"=>$listApp[0]->getId()
		],"ordem");
		$file="";
		
		foreach($listAppHtml as $vHtml){
			$html=new Html($vHtml->getHtmlId());
			if($html->getProtocolo()=='tcp'){
				$file.=Curl::get($html->getUrl());
			}
			
			if($html->getProtocolo()=='file'){
				$file.=file_get_contents($html->getUrl());
			}
		}
		
		$file=Minifica::mimificar($file);
		
		return $file;
	}
}