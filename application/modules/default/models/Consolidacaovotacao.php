<?php

/**
 * Description of Consolidacaovotacao
 *
 * @author augusto
 */
class Consolidacaovotacao extends MinC_Db_Table_Abstract{
    protected $_banco  = 'bdcorporativo';
    protected $_schema = 'bdcorporativo.scSAC';
    protected $_name   = 'tbConsolidacaoVotacao';


    public function verificarConsolidacaoprojeto($idnrreuniao , $idpronac){

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('cv'=>$this->_name),
                    array('cv.dsConsolidacao')
                    );
        $select->joinInner(
                            array('pa'=>'tbPauta'),
                            'pa.IdPRONAC = cv.IdPRONAC and pa.idNrReuniao = cv.idNrReuniao',
                            array(
                                 'pa.stAnalise'
                                 ),
                            'bdcorporativo.scSAC'
                          );
        $select->joinInner(
                            array('pr'=>'Projetos'),
                            'pr.IdPRONAC = cv.IdPRONAC',
                            array(
                                 '(pr.AnoProjeto+pr.Sequencial) as pronac',
                                 'pr.NomeProjeto'
                                 ),
                            'SAC'
                          );
        $select->where('cv.idNrReuniao = ?', $idnrreuniao);
        $select->where('cv.IdPRONAC = ?', $idpronac);

        return $this->fetchAll($select);

    }

}
