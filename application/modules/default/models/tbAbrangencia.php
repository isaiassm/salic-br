<?php
/**
 * DAO tbAbrangencia
 * OBS:
 * 	-> A tabela sac.dbo.Abrangencia armazena os locais de realizacao do projeto originais (aprovados)
 *  -> A tabela sac.dbo.tbAbrangencia armazena os locais de realizacao do projeto que foram solicitados na readequacao
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 18/04/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright  2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */

class tbAbrangencia extends MinC_Db_Table_Abstract
{
	/* dados da tabela */
//	protected $_banco   = "SAC";
	protected $_schema  = "sac";
	protected $_name    = "tbAbrangencia";
    protected $_primary = 'idAbrangencia';


	/**
	 * Busca os locais de abrangencia originais (aprovados)
	 * @access public
	 * @param array $where (filtros)
	 * @param array $order (ordenacao)
	 * @return object
	 */
	public function buscarLocaisAprovados($where = array(), $order = array())
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('a' => $this->_schema . '.Abrangencia')
			,array('a.idAbrangencia'
				,'a.idProjeto'
				,'a.idPais'
				,'a.idUF'
				,'a.idMunicipioIBGE')
		);
		$select->joinInner(
			array('p' => 'Pais')
			,'a.idPais = p.idPais'
			,array('p.Descricao AS dsPais')
			,'agentes'
		);
		$select->joinLeft(
			array('u' => 'UF')
			,'a.idUF = u.idUF'
			,array('u.Sigla AS dsUF')
			,'agentes'
		);
		$select->joinLeft(
			array('m' => 'Municipios')
			,'a.idMunicipioIBGE = m.idMunicipioIBGE'
			,array('m.Descricao AS dsMunicipioIBGE')
			,'agentes'
		);

		// adiciona quantos filtros foram enviados
		foreach ($where as $coluna => $valor) :
			$select->where($coluna, $valor);
		endforeach;

		// adicionando linha order ao select
		$select->order($order);

		return $this->fetchAll($select);
	} // fecha metodo buscarLocaisAprovados()



	/**
	 * Busca os locais de abrangencia solicitados (readequacao)
	 * @access public
	 * @param array $where (filtros)
	 * @param array $order (ordenacao)
	 * @return object
	 */
	public function buscarLocaisSolicitados($where = array(), $order = array())
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('a' => $this->_name)
			,array('a.idAbrangencia'
				,'a.idAbrangenciaAntiga'
				,'a.idPais'
				,'a.idUF'
				,'a.idMunicipioIBGE'
				,'a.tpAcao')
		);
		$select->joinInner(
			array('p' => 'Pais')
			,'a.idPais = p.idPais'
			,array('p.Descricao AS dsPais')
			,'agentes'
		);
		$select->joinLeft(
			array('u' => 'UF')
			,'a.idUF = u.idUF'
			,array('u.Sigla AS dsUF')
			,'agentes'
		);
		$select->joinLeft(
			array('m' => 'Municipios')
			,'a.idMunicipioIBGE = m.idMunicipioIBGE'
			,array('m.Descricao AS dsMunicipioIBGE')
			,'agentes'
		);

		// adiciona quantos filtros foram enviados
		foreach ($where as $coluna => $valor) :
			$select->where($coluna, $valor);
		endforeach;

		// adicionando linha order ao select
		$select->order($order);

		return $this->fetchAll($select);
	} // fecha metodo buscarLocaisSolicitados()


	public function buscarLocaisParaReadequacao($idPronac, $tabela = 'Abrangencia')
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array('a' => 'Projetos'),
			array(
                new Zend_Db_Expr('b.idAbrangencia, b.idPais, c.Descricao as Pais, b.idUF, d.Descricao as UF, b.idMunicipioIBGE as idCidade, e.Descricao as Cidade')
            ), 'SAC'
		);
        if($tabela == 'Abrangencia'){
		$select->joinInner(
                array('b' => 'Abrangencia'),"a.idProjeto = b.idProjeto AND b.stAbrangencia = 1",
                array(new Zend_Db_Expr("'N' AS tpSolicitacao")) ,'SAC'
		);
        } else {
		$select->joinInner(
                array('b' => 'tbAbrangencia'),"a.idPronac = b.idPronac AND stAtivo='S'",
                array('b.tpSolicitacao') ,'SAC'
		);
        }
		$select->joinInner(
			array('c' => 'Pais'),"b.idPais = c.idPais",
			array() ,'agentes'
		);
		$select->joinLeft(
			array('d' => 'Uf'),'b.idUF = d.idUF',
			array() ,'agentes'
		);
		$select->joinLeft(
			array('e' => 'Municipios'),"b.idMunicipioIBGE = e.idMunicipioIBGE",
			array() ,'agentes'
		);
		
        $select->where('a.IdPRONAC = ?', $idPronac);

        
		return $this->fetchAll($select);
	} // fecha metodo historicoReadequacao()
    
	public function buscarLocaisConsolidadoReadequacao($idReadequacao)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array('a' => 'Projetos'),
			array(
                new Zend_Db_Expr('b.idAbrangencia, b.idPais, c.Descricao as Pais, b.idUF, d.Descricao as UF, b.idMunicipioIBGE as idCidade, e.Descricao as Cidade')
            ), 'SAC'
		);
		$select->joinInner(
            array('b' => 'tbAbrangencia'),"a.idPronac = b.idPronac",
            array('b.tpSolicitacao','b.tpAnaliseTecnica','b.tpAnaliseComissao') ,'SAC'
		);
        $select->joinInner(
			array('c' => 'Pais'),"b.idPais = c.idPais",
			array() ,'agentes'
		);
		$select->joinLeft(
			array('d' => 'Uf'),'b.idUF = d.idUF',
			array() ,'agentes'
		);
		$select->joinLeft(
			array('e' => 'Municipios'),"b.idMunicipioIBGE = e.idMunicipioIBGE",
			array() ,'agentes'
		);

        $select->where('b.idReadequacao = ?', $idReadequacao);
        
		return $this->fetchAll($select);
	} // fecha metodo historicoReadequacao()

	public function buscarDadosAbrangenciaAtual($where = array())
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array('a' => 'Abrangencia'),
			array(
                new Zend_Db_Expr('a.*')
            ), 'SAC'
		);
        
		// adiciona quantos filtros foram enviados
		foreach ($where as $coluna => $valor) :
			$select->where($coluna, $valor);
		endforeach;

        
		return $this->fetchAll($select);
	} // fecha metodo historicoReadequacao()


} // fecha class