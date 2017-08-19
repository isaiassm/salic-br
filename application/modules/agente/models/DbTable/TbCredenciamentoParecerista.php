<?php

/**
 * Class Agente_Model_DbTable_TbCredenciamentoParecerista
 *
 * @name Agente_Model_DbTable_TbCredenciamentoParecerista
 * @package Modules/Agente
 * @subpackage Models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_TbCredenciamentoParecerista extends MinC_Db_Table_Abstract
{
    /**
     * @var string
     * @access protected
     */
    protected $_banco = 'agentes';

    /**
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    /**
     * @var string
     * @access protected
     */
    protected $_name = 'tbcredenciamentoparecerista';

    public function carregar($idAgente)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c'=>$this->_name),
            array('c.idAgente', 'c.idcredenciamentoparecerista', 'c.sicredenciamento', 'c.qtponto', 'c.idverificacao'),
            $this->_schema
        );

        $select->joinInner(
            array('a'=>'Area'),'a.codigo = c.idcodigoarea',
            array('a.descricao as area'),
            $this->getSchema('sac')
        );

        $select->joinInner(
            array('s'=>'segmento'),'s.codigo = c.idcodigosegmento',
            array('s.descricao as segmento'),
            $this->getSchema('sac')
        );

        $select->joinLeft(
            array('v'=>'Verificacao'),'v.idverificacao = c.idverificacao',
            array('v.descricao as nivel'),
            $this->_schema
        );

        $select->joinLeft(
            array('x'=>'Visao'),'x.idAgente = c.idAgente',
            array('x.Visao'),
            $this->_schema
        );

        $select->where('c.idAgente = ?', $idAgente);
        $select->where('x.Visao = ?', 209);
        $select->order('a.descricao');
        $select->order('s.descricao');

        return $this->fetchAll($select);
    }

    //Select count(distinct idCodigoArea) as qtdArea from agentes.dbotbCredenciamentoParecerista where idCodigoSegmento LIKE '1%'
	//Select count(distinct idCodigoSegmento) as qtdSeguimentos from agentes.dbotbCredenciamentoParecerista where idCodigoSegmento LIKE '1%'
    
    public function QtdArea($idAgente) 
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('A'=>$this->_name),
        			  array('count(distinct idCodigoArea) as qtd')
        );

        $select->where('A.idAgente = ?', $idAgente);

        $select->where('A.siCredenciamento = 1');
        return $this->fetchAll($select);

    }

    public function QtdSegmento($idAgente, $idSegmento) 
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('A'=>$this->_name),
        			  array('count(distinct idCodigoSegmento) as qtd')
        );

        $select->where("A.idCodigoSegmento LIKE '".$idSegmento."%'");

        $select->where('A.idAgente = ?', $idAgente);

        $select->where('A.siCredenciamento = 1');
        
        return $this->fetchAll($select);

    }

    public function verificarCadastrado($idAgente, $idSegmento, $idArea) 
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('A'=>$this->_name),
        			  array('*')
        );

        $select->where('A.idCodigoArea = ?', $idArea);

        $select->where('A.idCodigoSegmento = ?', $idSegmento);

        $select->where('A.idAgente = ?', $idAgente);

        $select->where('A.siCredenciamento = 1');
        
        return $this->fetchAll($select);

    }


    public function inserirCredenciamento($dados) 
    {
        $insert = $this->insert($dados);
        return $insert;
    }

    public function alteraCredenciamento($dados, $idCredenciamento)
	{
		
		$where = "idCredenciamentoParecerista = " . $idCredenciamento;
		
		return $this->update($dados, $where);
	} 
    
    public function excluiCredenciamento($idCredenciamento)
	{
		$sql ="DELETE FROM agentes.dbo.tbCredenciamentoParecerista WHERE idCredenciamentoParecerista = ".$idCredenciamento;
        
        $db = Zend_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_DB::FETCH_OBJ);
        if($db->query($sql))
        {
            return true;
        }
        else
        {
            return false;
        }
	} 
    
   

}
?>
