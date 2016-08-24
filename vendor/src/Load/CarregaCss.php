<?php

namespace Service\Load;

use Service\Model\Aplicacao;
use Service\Model\AplicacaoHasCss;
use Service\Model\Css;

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
		//pega a aplicação
		$app=new Aplicacao();
		$listApp=$app->listarPorWhere("nome=:nome",[
				":nome"=>$this->aplicacao
		]);
		
		//verfica se existe a aplicação
		if(empty($listApp)){
			throw new \Exception("Aplicação não cadastrada",-1);
		}
		
		//pega a lista de arquivos
		$appCss=new AplicacaoHasCss();
		
		/** @var \Service\Model\AplicacaoHasCss[] $listAppCss */
		$listAppCss=$appCss->listarPorWhere("aplicacaoId=:appId",[
				":appId"=>$listApp[0]->getId()
		],"ordem");
		
		//a princípio não é necessário minificar
		$deveMinificar=false;
		
		//lista de url e protocolos para coletar os arquivos se necessário
		$listUrl=array();
		
		foreach($listAppCss as $vCss){
			
			//se o arquivo não foi minificado então será necessário minificar
			if(empty($vCss->getMinificado())){
				$deveMinificar=true;
			}
			
			//seta nova data de minificação/consulta
			$vCss->setMinificado($hoje->format("Y-m-d H:i:s"));
			$vCss->atualizar();
			
			//pega os dados do arquivo Css
			$css=new Css($vCss->getCssId());
			if($css->getProtocolo()=='file'){
				$listUrl[]=[
						"protocolo"=>$css->getProtocolo(),
						"url"=>$css->getUrl()
				];
			}
		}
		
		// arquivo dessa aplicação
		$tmp=__DIR__."/../Tmp/".$app->getId().".css";
		
		//arquivo a ser retornado
		$file="";
		
		if($deveMinificar){
			
			//abre os arquivos
			foreach($listUrl as $vPU){
				if($vPU["protocolo"]=='tcp'){
					$file.=Curl::get($vPU["url"]);
				}
				
				if($vPU["protocolo"]=='file'){
					$file.=file_get_contents($vPU["url"]);
				}
			}
			
			//minifica e salva
			$file=Minifica::mimificar($file);
			file_put_contents($tmp,$file);
		}
		
		//se foi minificado então abre o arquivo
		if(file_exists($tmp)){
			$file=file_get_contents($tmp);
		}
		
		return $file;
		
	}
}