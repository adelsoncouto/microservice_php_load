<?php
namespace Service\Load;

use Service\Model\Aplicacao;
use Service\Model\AplicacaoHasJavascript;
use Service\Model\Javascript;

/**
 * Classe responsável por carregar os arquivos javascript
 *
 * @author Adelson Silva Couto
 *
 */
class CarregaJavaScript{
	
/**
	 * Nome da aplicação que está solicitando o js
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
		$appJavascript=new AplicacaoHasJavascript();
		
		/** @var \Service\Model\AplicacaoHasJavascript[] $listAppJavascript */
		$listAppJavascript=$appJavascript->listarPorWhere("aplicacaoId=:appId",[
				":appId"=>$listApp[0]->getId()
		],"ordem");
		
		//a princípio não é necessário minificar
		$deveMinificar=false;
		
		//lista de url e protocolos para coletar os arquivos se necessário
		$listUrl=array();
		
		foreach($listAppJavascript as $vJavascript){
				
			//se o arquivo não foi minificado então será necessário minificar
			if(empty($vJavascript->getMinificado())){
				$deveMinificar=true;
			}
				
			//seta nova data de minificação/consulta
			$vJavascript->setMinificado($hoje->format("Y-m-d H:i:s"));
			$vJavascript->atualizar();
				
			//pega os dados do arquivo Javascript
			$javascript=new Javascript($vJavascript->getJavascriptId());
			if($javascript->getProtocolo()=='file'){
				$listUrl[]=[
						"protocolo"=>$javascript->getProtocolo(),
						"url"=>$javascript->getUrl()
				];
			}
		}
		
		// arquivo dessa aplicação
		$tmp=__DIR__."/../Tmp/".$app->getId().".js";
		
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