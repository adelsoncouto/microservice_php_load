<?php
namespace Service\Load;

use Service\Model\AplicacaoHasJavascript;
use Service\Model\Javascript;
use Service\Model\Aplicacao;

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
		$app = new Aplicacao();
		$listApp = $app->listarPorWhere("nome=:nome",[":nome"=>$this->aplicacao]);
		
		if(empty($listApp)){
			throw new \Exception("Aplicação não cadastrada", -1);
		}
		
		$appJs = new AplicacaoHasJavascript();
		$listAppJs = $appJs->listarPorWhere("aplicacaoId=:appId",[":appId"=>$listApp[0]->getId()],"ordem");
		
		$file = "";
		
		foreach ($listAppJs as $vJs){
			$js = new Javascript($vJs->getJavascriptId());
			
			if($js->getProtocolo() == 'tcp'){
				$file .= Curl::get($js->getUrl());
			}
			
			if($js->getProtocolo() == 'file'){
				$file .= file_get_contents($js->getUrl());
			}
			
		}
		
		$file = Minifica::mimificar($file);
		
		return $file;
		
	}
	
}