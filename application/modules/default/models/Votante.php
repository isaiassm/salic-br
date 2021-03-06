<?php
class Votante extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbVotante';
    protected $_schema = 'bdcorporativo.scSAC';

    public function selecionarvotantes($idreuniao) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('tbv' => $this->_name),
            array ( 'tbv.idAgente'),
            $this->_schema
        );
        $select->joinInner(
                array('nm' => 'Nomes'),
                "nm.idAgente = tbv.idAgente",
                array('nm.Descricao'),
                'agentes'
        );
        $select->where('tbv.idreuniao = ?', $idreuniao);
        $select->order('nm.Descricao asc');
        return $this->fetchAll($select);
    }
}
