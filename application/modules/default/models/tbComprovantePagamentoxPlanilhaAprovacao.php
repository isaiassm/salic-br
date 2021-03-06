<?php
/**
 * DAO tbComprovantePagamentoxPlanilhaAprovacao 
 * @since 27/08/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbComprovantePagamentoxPlanilhaAprovacao extends MinC_Db_Table_Abstract
{
	/* dados da tabela */
	protected $_banco   = "bdcorporativo";
	protected $_schema  = "bdcorporativo.scsac";
	protected $_name    = "tbComprovantePagamentoxPlanilhaAprovacao";


	/**
	 * M�todo para buscar as inconsist�ncias do extrato de movimenta��o banc�ria
	 * @param string $pronac
	 * @param array $data_recibo
	 * @param string $proponente
	 * @param string $incentivador
	 * @param array $data_credito
	 * @return object
	 */
	public function buscarDadosItens($idPronac, $idPlanilhaAprovacao)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
//                $select->distinct();
		$select->from(
			array("a" => $this->_name)
			,array('vlComprovado as vlComprovacao')
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("b" => "tbComprovantePagamento")
			,"a.idComprovantePagamento = b.idComprovantePagamento"
			,array('DtPagamento','tpDocumento','nrComprovante','dtEmissao','idArquivo','tpFormaDePagamento')
            ,"bdcorporativo.scSAC"
		);
		$select->joinLeft(
			array("c" => "tbPlanilhaAprovacao")
			,"a.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
			,array()
            ,"SAC"
		);
		$select->joinLeft(
			array("d" => "tbPlanilhaItens")
			,"c.idPlanilhaItem = d.idPlanilhaItens"
			,array('Descricao as Item')
            ,"SAC"
		);
		$select->joinLeft(
			array("e" => "Nomes")
			,"b.idFornecedor = e.idAgente"
			,array('Descricao as Fornecedor')
            ,"agentes"
		);
		$select->joinLeft(
			array("f" => "tbArquivo")
			,"b.idArquivo = f.idArquivo"
			,array('nmArquivo')
            ,"bdcorporativo.scCorp"
		);
        
        $select->where("c.stAtivo = ?", 'S');
        $select->where("c.idPronac = ?", $idPronac);
        $select->where("a.idPlanilhaAprovacao = ?", $idPlanilhaAprovacao);
		
		//$select->order("t.dtCredito");
        
		return $this->fetchAll($select);
	} // fecha m�todo buscarDados()
    
    
	public function buscarRelacaoPagamentos($idPronac, $idPlanilhaAprovacao = null)
	{
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(
                    array("a" => $this->_name),
                    array(
                        new Zend_Db_Expr("
                            c.idPronac,
                            d.Descricao as Item,
                            b.idComprovantePagamento,
                            a.idPlanilhaAprovacao,
                            g.CNPJCPF,
                            e.Descricao as Fornecedor,
                            b.DtPagamento as DtComprovacao,
                            CASE tpDocumento
                                WHEN 1 THEN ('Cupom Fiscal')
                                WHEN 2 THEN ('Guia de Recolhimento')
                                WHEN 3 THEN ('Nota Fiscal/Fatura')
                                WHEN 4 THEN ('Recibo de Pagamento')
                                WHEN 5 THEN ('RPA')
                                ELSE ''
                            END as tbDocumento,
                            b.nrComprovante,
                            b.dtEmissao as DtPagamento,
                            CASE
                              WHEN b.tpFormaDePagamento = '1'
                                 THEN 'Cheque'
                              WHEN b.tpFormaDePagamento = '2'
                                 THEN 'Transferencia Banc&aacute;ria'
                              WHEN b.tpFormaDePagamento = '3'
                                 THEN 'Saque/Dinheiro'
                                 ELSE ''
                            END as tpFormaDePagamento,
                            b.nrDocumentoDePagamento,
                            a.vlComprovado as vlPagamento,
                            b.idArquivo,
                            b.dsJustificativa,
                            f.nmArquivo"
                        )
                    )
                ,"bdcorporativo.scSAC"
            );
            
            $select->joinInner(
                array("b" => "tbComprovantePagamento")
                ,"a.idComprovantePagamento = b.idComprovantePagamento"
                ,array()
                ,"bdcorporativo.scSAC"
            );
            $select->joinLeft(
                array("c" => "tbPlanilhaAprovacao")
                ,"a.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
                ,array()
                ,"SAC"
            );
            $select->joinLeft(
                array("d" => "tbPlanilhaItens")
                ,"c.idPlanilhaItem = d.idPlanilhaItens"
                ,array()
                ,"SAC"
            );
            $select->joinLeft(
                array("e" => "Nomes")
                ,"b.idFornecedor = e.idAgente"
                ,array()
                ,"agentes"
            );
            $select->joinLeft(
                array("f" => "tbArquivo")
                ,"b.idArquivo = f.idArquivo"
                ,array('nmArquivo')
                ,"bdcorporativo.scCorp"
            );
            $select->joinLeft(
                array("g" => "Agentes")
                ,"b.idFornecedor = g.idAgente"
                ,array()
                ,"agentes"
            );

            $select->where("c.idPronac = ?", $idPronac);
            if($idPlanilhaAprovacao){
                $select->where("a.idPlanilhaAprovacao = ?", $idPlanilhaAprovacao);
            }
		
            $select->order("d.Descricao");
            $select->order("e.Descricao");
            
            return $this->fetchAll($select);
        } // fecha m�todo buscarDados()
    
	public function pagamentosPorUFMunicipio($idPronac)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array("a" => $this->_name)
			,array(
                new Zend_Db_Expr("
                    c.idPlanilhaAprovacao,
                    agentes.dbo.fnUFAgente(idFornecedor) AS UFFornecedor,
                    agentes.dbo.fnMunicipioAgente(idFornecedor) AS MunicipioFornecedor,
                    c.idPronac,
                    d.Descricao AS Item,
                    g.CNPJCPF,
                    e.Descricao AS Fornecedor,
                    b.DtPagamento AS DtComprovacao,
                    b.vlComprovacao AS vlPagamento,
                    b.idArquivo,
                    f.nmArquivo
                ")
            )
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("b" => "tbComprovantePagamento")
			,"a.idComprovantePagamento = b.idComprovantePagamento"
			,array()
            ,"bdcorporativo.scSAC"
		);
		$select->joinLeft(
			array("c" => "tbPlanilhaAprovacao")
			,"a.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
			,array()
            ,"SAC"
		);
		$select->joinLeft(
			array("d" => "tbPlanilhaItens")
			,"c.idPlanilhaItem = d.idPlanilhaItens"
			,array()
            ,"SAC"
		);
		$select->joinLeft(
			array("e" => "Nomes")
			,"b.idFornecedor = e.idAgente"
			,array()
            ,"agentes"
		);
		$select->joinLeft(
			array("f" => "tbArquivo")
			,"b.idArquivo = f.idArquivo"
			,array()
            ,"bdcorporativo.scCorp"
		);
		$select->joinLeft(
			array("g" => "Agentes")
			,"b.idFornecedor = g.idAgente"
			,array()
            ,"agentes"
		);

        //Altera��o realizada no dia 25/02/2016 a pedido da area demandante.
        #$select->where("c.stAtivo = ?", 'S');
        $select->where("c.idPronac = ?", $idPronac);
		
		$select->order("d.Descricao");
		$select->order("e.Descricao");

        

		return $this->fetchAll($select);
	} // fecha m�todo buscarDados()
    
    
	public function pagamentosConsolidadosPorUfMunicipio($idPronac)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array("a" => $this->_name)
			,array(
                new Zend_Db_Expr("
                    agentes.dbo.fnUFAgente(idFornecedor) AS UFFornecedor,
                    agentes.dbo.fnMunicipioAgente(idFornecedor) AS MunicipioFornecedor,
                    SUM(b.vlComprovacao) as vlPagamento
                ")
            )
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("b" => "tbComprovantePagamento")
			,"a.idComprovantePagamento = b.idComprovantePagamento"
			,array()
            ,"bdcorporativo.scSAC"
		);
		$select->joinLeft(
			array("c" => "tbPlanilhaAprovacao")
			,"a.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
			,array()
            ,"SAC"
		);
		$select->joinLeft(
			array("d" => "tbPlanilhaItens")
			,"c.idPlanilhaItem = d.idPlanilhaItens"
			,array()
            ,"SAC"
		);
		$select->joinLeft(
			array("g" => "Agentes")
			,"b.idFornecedor = g.idAgente"
			,array()
            ,"agentes"
		);

        $select->where("c.idPronac = ?", $idPronac);

		$select->order("agentes.dbo.fnUFAgente(idFornecedor)");
		$select->order("agentes.dbo.fnMunicipioAgente(idFornecedor)");
		$select->group("agentes.dbo.fnUFAgente(idFornecedor), agentes.dbo.fnMunicipioAgente(idFornecedor)");
		
        return $this->fetchAll($select);
	}
    
    
	public function buscarRelatorioBensDeCapital($idPronac)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array("a" => $this->_name)
			,array(
                new Zend_Db_Expr("
                    CASE
                        WHEN tpDocumento = 1 THEN 'Boleto Banc�rio'
                        WHEN tpDocumento = 2 THEN 'Cupom Fiscal'
                        WHEN tpDocumento = 3 THEN 'Nota Fiscal / Fatura'
                        WHEN tpDocumento = 4 THEN 'Recibo de Pagamento'
                        WHEN tpDocumento = 5 THEN 'RPA'
                    END as Titulo,
                    b.nrComprovante,
                    d.Descricao as Item,
                    DtEmissao as dtPagamento,
                    dsItemDeCusto Especificacao,
                    dsMarca as Marca,
                    dsFabricante as Fabricante,
                    (c.qtItem*nrOcorrencia) as Qtde,
                    c.vlUnitario,
                    (c.qtItem*nrOcorrencia*c.vlUnitario) as vlTotal
                ")
            )
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("b" => "tbComprovantePagamento")
			,"a.idComprovantePagamento = b.idComprovantePagamento"
			,array()
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("c" => "tbPlanilhaAprovacao")
			,"a.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
			,array()
            ,"SAC"
		);
		$select->joinInner(
			array("d" => "tbPlanilhaItens")
			,"c.idPlanilhaItem = d.idPlanilhaItens"
			,array()
            ,"SAC"
		);
		$select->joinInner(
			array("e" => "tbItemCusto")
			,"e.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
			,array()
            ,"bdcorporativo.scSAC"
		);

        //Linha retirada para corrigir problema na Visualiza��o dos Projetos (24/02/2016)
        #$select->where("c.stAtivo = ?", 'S');

        $select->where("c.idPronac = ?", $idPronac);
		$select->order(3);
        
		return $this->fetchAll($select);
	} // fecha m�todo buscarDados()
    
    
	public function buscarRelatorioFisico($idPronac)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array("a" => $this->_name)
			,array(
                new Zend_Db_Expr("
                    f.idPlanilhaEtapa,
                    f.Descricao AS Etapa,
                    d.Descricao AS Item,
                    g.Descricao AS Unidade,
                    (c.qtItem*nrOcorrencia) AS qteProgramada,
                    (c.qtItem*nrOcorrencia*c.vlUnitario) AS vlProgramado,
                    ((sum(b.vlComprovacao) / (c.qtItem*nrOcorrencia*c.vlUnitario)) * 100) AS PercExecutado,
                    (sum(b.vlComprovacao)) AS vlExecutado,
                    (100 - (sum(b.vlComprovacao) / (c.qtItem*nrOcorrencia*c.vlUnitario)) * 100) AS PercAExecutar
                ")
            )
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("b" => "tbComprovantePagamento")
			,"a.idComprovantePagamento = b.idComprovantePagamento"
			,array()
            ,"bdcorporativo.scSAC"
		);
		$select->joinInner(
			array("c" => "tbPlanilhaAprovacao")
			,"a.idPlanilhaAprovacao = c.idPlanilhaAprovacao"
			,array()
            ,"SAC"
		);
		$select->joinInner(
			array("d" => "tbPlanilhaItens")
			,"c.idPlanilhaItem = d.idPlanilhaItens"
			,array()
            ,"SAC"
		);
		$select->joinInner(
			array("f" => "tbPlanilhaEtapa")
			,"c.idEtapa = f.idPlanilhaEtapa"
			,array()
            ,"SAC"
		);
		$select->joinInner(
			array("g" => "tbPlanilhaUnidade")
			,"c.idUnidade= g.idUnidade"
			,array()
            ,"SAC"
		);

        //Linha retirada para corrigir problema na Visualiza��o dos Projetos (24/02/2016)
        //$select->where("c.stAtivo = ?", 'S');

        $select->where("c.idPronac = ?", $idPronac);
		$select->group(array('c.idPronac','f.Descricao','d.Descricao','g.Descricao','c.qtItem','nrOcorrencia','c.vlUnitario','f.idPlanilhaEtapa'));
		$select->order(array(1,2));
        
		return $this->fetchAll($select);
	} // fecha m�todo buscarDados()
        
    
	public function buscarRelatorioExecucaoReceita($idPronac)
	{
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
                array('a' => 'Captacao'),
                array( new Zend_Db_Expr("'RECEITA' AS tipo, a.CgcCpfMecena, c.Descricao AS Nome, sum(CaptacaoReal) AS vlIncentivado") ) , 'SAC'
        );
        $a->joinInner(
                array('b' => 'Agentes'), "a.CgcCpfMecena = b.CNPJCpf",
                array(), 'agentes'
        );
        $a->joinInner(
                array('c' => 'Nomes'), "b.idAgente = c.idAgente",
                array(), 'agentes'
        );
        $a->where('a.AnoProjeto+a.Sequencial = (SELECT x.Anoprojeto+x.Sequencial FROM sac.dbo.Projetos x WHERE x.idPronac = ? )', $idPronac);
        $a->group(array('a.CgcCpfMecena','c.Descricao'));
        $a->order(array('2','3'));

        

        return $this->fetchAll($a);
	} // fecha m�todo buscarDados()
    
	public function buscarRelatorioExecucaoDespesa($idPronac)
	{   
        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
                array('a' => $this->_name),
                array( new Zend_Db_Expr("'DESPESA' AS tipo, f.Descricao as Etapa, d.Descricao AS Item, sum(b.vlComprovacao) AS vlPagamento") ), 'bdcorporativo.scSAC'
        );
        $b->joinInner(
                array('b' => 'tbComprovantePagamento'), "a.idComprovantePagamento = b.idComprovantePagamento",
                array(), 'bdcorporativo.scSAC'
        );
        $b->joinInner(
                array('c' => 'tbPlanilhaAprovacao'), "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
                array(), 'SAC'
        );
        $b->joinInner(
                array('d' => 'tbPlanilhaItens'), "c.idPlanilhaItem = d.idPlanilhaItens",
                array(), 'SAC'
        );
        $b->joinInner(
                array('f' => 'tbPlanilhaEtapa'), "c.idEtapa = f.idPlanilhaEtapa",
                array(), 'SAC'
        );
        //Linha retirada para corrigir problema na Visualiza��o dos Projetos (24/02/2016)
        #$b->where('c.stAtivo = ?', 'S');

        $b->where('c.idPronac = ?', $idPronac);
        $b->group(array('c.idPronac','f.Descricao','d.Descricao'));
        $b->order(array('2','3'));
        return $this->fetchAll($b);
        
	} // fecha m�todo buscarDados()
    
    
	/**
	 * M�todo para cadastrar
	 * @access public
	 * @param array $dados
	 * @return integer (retorna o �ltimo id cadastrado)
	 */
	public function cadastrarDados($dados)
	{
		return $this->insert($dados);
	} // fecha m�todo cadastrarDados()



	/**
	 * M�todo para alterar
	 * @access public
	 * @param array $dados
	 * @param integer $where
	 * @return integer (quantidade de registros alterados)
	 */
	public function alterarDados($dados, $where)
	{
		$where = "idTmpCaptacao = " . $where;
		return $this->update($dados, $where);
	} // fecha m�todo alterarDados()



	/**
	 * M�todo para excluir
	 * @access public
	 * @param integer $where
	 * @return integer (quantidade de registros exclu�dos)
	 */
	public function excluirDados($where)
	{
		$where = "idTmpCaptacao = " . $where;
		return $this->delete($where);
	} // fecha m�todo excluirDados()


    public function buscarDadosParaRemanejamento($idTmpCaptacao){
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array(
                "a.nrAnoProjeto",
                "a.nrSequencial",
                new Zend_Db_Expr("(SELECT SUBSTRING(dsInformacao,2,4) FROM sac.dbo.tbDepositoIdentificadoCaptacao WHERE SUBSTRING(dsInformacao,1,1)='1') as NumeroRecibo"),
                "a.nrCpfCnpjIncentivador",
                new Zend_Db_Expr("(SELECT b.Enquadramento FROM sac.dbo.enquadramento AS b WHERE b.AnoProjeto+b.Sequencial =  a.nrAnoProjeto+a.nrSequencial) AS MedidaProvisoria"),
                "a.dtChegadaRecibo",
                "a.dtCredito",
                new Zend_Db_Expr("0 AS CaptacaoUfir"),
                new Zend_Db_Expr("(SELECT idUsuario FROM sac.DBO.tbDepositoIdentificadoCaptacao WHERE SUBSTRING(dsInformacao,1,1)='1') AS Logon"),
                new Zend_Db_Expr("(SELECT idPronac FROM sac.dbo.Projetos AS p WHERE p.AnoProjeto+p.Sequencial = a.nrAnoProjeto + a.nrSequencial) AS idProjeto")
            )
        );
        $select->where('a.idTmpCaptacao = ?', $idTmpCaptacao);

        
        return $this->fetchRow($select);

	} // fecha m�todo buscarDados()
    
    public function buscarValorComprovadoDoItem($idPlanilhaAprovacao)
	{   
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
                array( new Zend_Db_Expr("SUM(b.vlComprovacao) AS vlComprovado") ), 'bdcorporativo.scSAC'
        );
        $select->joinInner(
                array('b' => 'tbComprovantePagamento'), "a.idComprovantePagamento = b.idComprovantePagamento",
                array(), 'bdcorporativo.scSAC'
        );
        $select->where('a.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);
        return $this->fetchRow($select);
        
	} // fecha m�todo buscarDados()

} // fecha class