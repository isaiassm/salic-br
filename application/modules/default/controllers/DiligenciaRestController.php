<?php

/**
 * Dados da diligência via REST
 * 
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright © 2016 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 */
class DiligenciaRestController extends Minc_Controller_AbstractRest {

    public function init(){
        parent::init();
    }

    public function postAction(){}
    
    public function indexAction(){}

    public function getAction(){
        $idDiligencia = $this->_request->getParam('id');
        $modelDiligencia = new tbDiligencia();
        $resultado = $modelDiligencia->buscarPorIdDiligencia($idDiligencia);
        $diligencia = (object) $resultado->toArray();

        if($diligencia){
            # Formatando dados
            $diligencia->dataSolicitacao = $diligencia->DtSolicitacao? date('d/m/Y H',strtotime($diligencia->DtSolicitacao)). 'h'. date('i',strtotime($diligencia->DtSolicitacao)): NULL;
            $diligencia->texto = html_entity_decode(utf8_encode($diligencia->texto), ENT_COMPAT, 'UTF-8');
        }

        # Resposta do servi&ccedil;o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($diligencia));
    }
    
    public function putAction(){}

    public function deleteAction(){}

}