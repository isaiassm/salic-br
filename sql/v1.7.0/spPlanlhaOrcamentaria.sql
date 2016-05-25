-- =========================================================================================
-- Autor: R�mulo Menh� Barbosa
-- Data de Cria��o: 10/09/2013
-- Descri��o: Montar planilha or�ament�ria
-- Data de Altera�o: 15/10/2013
-- =========================================================================================
-- Motivo : incluir a planilha da proposta
-- Data de Altera�o: 24/01/2014
-- =========================================================================================
-- Motivo : incluir o tipo 5 para remanejamento de at� 20%.
-- Data de Altera��o: 07/02/2014
-- Motivo : Inclus�o do atributo valor comprovado nas planilha tipo 3 e 5.
-- =========================================================================================
-- Data de Altera��o: 06/03/2014
-- Motivo : Inclus�o da planilha do tipo 6 para remanejamento acima de 20%,
--          complementa��o e redu��o.
-- =========================================================================================
-- Data de Altera��o: 29/04/2014
-- Motivo : Altera��o na planilha tipo 4 - WHERE
-- =========================================================================================
-- Data de Altera��o: 29/06/2015
-- Motivo : Altera��o na query das planilhas tipo 5 e 6
-- =========================================================================================
-- Data de Altera��o: 14/06/2015
-- Motivo : Altera��o na query das planilhas tipo 6
-- =========================================================================================
-- Data de Altera��o: 02/10/2015
-- Motivo : Altera��o na query das planilhas - mostrar somente stAtivo = 'S' e permitir que
--          proponente veja a planilha durante a sua constru��o, ap�s o envio volta novamen-
--          te visualizar a planilha ativa.
-- =========================================================================================
-- Data de Altera��o: 15/01/2016
-- Motivo : Altera��o na query das planilhas tipo 5
-- =========================================================================================
-- Data de Altera��o: 22/01/2016
-- Motivo : Altera��o na query das planilhas tipo 6
-- =========================================================================================
-- Data de Altera��o: 17/02/2016
-- Motivo : Altera��o na query das planilhas tipo 5 e 6 - 
--          Alterado o valor comprovado.
-- =========================================================================================
-- Data de Altera��o: 19/05/2016
-- Motivo : Inclus�o de restri��o na planilha tipo 6 ativa, n�o mostrar registros exclu�dos.
-- =========================================================================================

IF OBJECT_ID ( 'dbo.spPlanilhaOrcamentaria', 'P' ) IS NOT NULL 
    DROP PROCEDURE dbo.spPlanilhaOrcamentaria;
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET NOCOUNT ON
GO
CREATE PROCEDURE dbo.spPlanilhaOrcamentaria (@idPronac int, @TipoPlanilha char(1))
AS  
SET NOCOUNT ON
-- =========================================================================================
-- PLANILHA OR�AMENT�RIA DA PROPOSTA
-- =========================================================================================
IF @TipoPlanilha = 0
   BEGIN
      SELECT a.idPreProjeto as idPronac,' ' AS PRONAC,a.NomeProjeto,
            b.idProduto,b.idPlanilhaProposta,
            CASE 
              WHEN idProduto = 0
                   THEN 'Administra��o do Projeto'
                   ELSE c.Descricao 
              END as Produto,
            b.idEtapa,d.Descricao as Etapa,
            i.Descricao as Item,e.Descricao as Unidade,b.Quantidade,b.Ocorrencia,b.ValorUnitario as vlUnitario,
            ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSolicitado,QtdeDias,x.Descricao as FonteRecurso,b.FonteRecurso as idFonte,
            f.UF,f.Municipio, convert(varchar(max),b.dsJustificativa) as JustProponente
            FROM PreProjeto a
            INNER JOIN tbPlanilhaProposta b on (a.idPreProjeto = b.idProjeto)
            LEFT JOIN Produto c on (b.idProduto = c.Codigo)
            INNER JOIN tbPlanilhaEtapa d on (b.idEtapa = d.idPlanilhaEtapa)
            INNER JOIN tbPlanilhaUnidade e on (b.Unidade = e.idUnidade)
            INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
            INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
            INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
            WHERE a.idPreProjeto = @idPronac
            ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
   END
ELSE
-- =========================================================================================
-- PLANILHA OR�AMENT�RIA DO PROPONENTE
-- =========================================================================================
IF @TipoPlanilha = 1
   BEGIN
     SELECT a.idPronac,a.AnoProjeto,a.Sequencial AS PRONAC,a.NomeProjeto,
            b.idProduto,b.idPlanilhaProposta,
            CASE 
              WHEN idProduto = 0
                   THEN 'Administra��o do Projeto'
                   ELSE c.Descricao 
              END as Produto,
            b.idEtapa,d.Descricao as Etapa,
            i.Descricao as Item,e.Descricao as Unidade,b.Quantidade,b.Ocorrencia,b.ValorUnitario as vlUnitario,
            ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSolicitado,QtdeDias,x.Descricao as FonteRecurso,b.FonteRecurso as idFonte,
            f.UF,f.Municipio, convert(varchar(max),b.dsJustificativa) as JustProponente
            FROM Projetos a
            INNER JOIN tbPlanilhaProposta b on (a.idProjeto = b.idProjeto)
            LEFT JOIN Produto c on (b.idProduto = c.Codigo)
            INNER JOIN tbPlanilhaEtapa d on (b.idEtapa = d.idPlanilhaEtapa)
            INNER JOIN tbPlanilhaUnidade e on (b.Unidade = e.idUnidade)
            INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
            INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
            INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
            WHERE a.idPronac = @idPronac
            ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
   END
ELSE
-- =========================================================================================
-- PLANILHA OR�AMENT�RIA DO PARECERISTA
-- =========================================================================================
IF @TipoPlanilha = 2
   BEGIN 
      SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,b.idProduto,b.idPlanilhaProjeto,
             CASE 
               WHEN b.idProduto = 0
                    THEN 'Administra��o do Projeto'
                    ELSE c.Descricao 
               END as Produto,
             b.idEtapa,d.Descricao as Etapa,b.idPlanilhaItem,i.Descricao as Item,b.UfDespesa as idUF,b.MunicipioDespesa as idMunicipio,
             ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) as vlSolicitado,
             convert(varchar(max),z.dsJustificativa) as JustProponente,
             e.Descricao as Unidade,b.Quantidade,b.Ocorrencia,b.ValorUnitario as vlUnitario,b.QtdeDias,
             b.FonteRecurso as idFonte,x.Descricao as FonteRecurso,f.UF,f.Municipio,
             ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSugerido,convert(varchar(max),b.Justificativa) as JustParecerista,b.idUsuario
             FROM Projetos a
             INNER JOIN tbPlanilhaProjeto b on (a.idPronac = b.idPronac)
             INNER JOIN tbPlanilhaProposta z on (b.idPlanilhaProposta=z.idPlanilhaProposta)
             LEFT JOIN Produto c on (b.idProduto = c.Codigo)
             INNER JOIN tbPlanilhaEtapa d on (b.idEtapa = d.idPlanilhaEtapa)
             INNER JOIN tbPlanilhaUnidade e on (b.idUnidade = e.idUnidade)
             INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
             INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
             INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
             WHERE a.idPronac = @idPronac
             ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
    END
ELSE
-- =========================================================================================
-- PLANILHA OR�AMENT�RIA DO APROVADA
-- =========================================================================================
IF @TipoPlanilha = 3
   BEGIN 
      SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.tpPlanilha,
             CASE 
               WHEN k.idProduto = 0
                    THEN 'Administra��o do Projeto'
                    ELSE c.Descricao 
               END as Produto,
             b.idEtapa,d.Descricao as Etapa,k.idPlanilhaItem,i.Descricao as Item,k.idUfDespesa as idUF,k.idMunicipioDespesa as idMunicipio,
             ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) as vlSolicitado,convert(varchar(max),z.dsJustificativa) as JustProponente,
             ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSugerido,convert(varchar(max),b.Justificativa) as JustParecerista,
             e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,k.QtDias as QtdeDias,
             k.TpDespesa,k.TpPessoa,k.nrContrapartida,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,f.UF,f.Municipio,
             ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
             CASE
               WHEN k.tpPlanilha = 'CO'
                  THEN (SELECT sum(b1.vlComprovacao) AS vlPagamento 
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                    WHERE c1.stAtivo = 'S' AND c1.idPlanilhaAprovacao = k.idPlanilhaAprovacao AND (c1.idPronac = k.idPronac) 
                    GROUP BY a1.idPlanilhaAprovacao)
                  ELSE 
                     (SELECT sum(b1.vlComprovacao) AS vlPagamento 
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacaoPai)
                    WHERE c1.stAtivo = 'S' AND c1.idPlanilhaAprovacaoPai = k.idPlanilhaAprovacaoPai AND (c1.idPronac = k.idPronac) 
                    GROUP BY a1.idPlanilhaAprovacao)
                 END as vlComprovado,
             CONVERT(varchar(max),k.dsJustificativa) as JustComponente
       FROM Projetos a
       INNER JOIN tbPlanilhaProjeto b on (a.idPronac = b.idPronac)
       INNER JOIN tbPlanilhaProposta z on (b.idPlanilhaProposta=z.idPlanilhaProposta)
       INNER JOIN tbPlanilhaAprovacao k on (b.idPlanilhaProposta=k.idPlanilhaProposta)
       LEFT JOIN Produto c on (b.idProduto = c.Codigo)
       INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
       INNER JOIN tbPlanilhaUnidade e on (b.idUnidade = e.idUnidade)
       INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
       INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
       INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
       WHERE k.stAtivo = 'S' AND a.idPronac = @idPronac
       ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao

   END
ELSE
-- =========================================================================================
-- CORTES OR�AMENT�RIOS APROVADO
-- =========================================================================================
IF @TipoPlanilha = 4
   BEGIN 
      SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,b.idPlanilhaProjeto,
             CASE 
               WHEN k.idProduto = 0
                    THEN 'Administra��o do Projeto'
                    ELSE c.Descricao 
               END as Produto,
             b.idEtapa,d.Descricao as Etapa,i.Descricao as Item,
             ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) as vlSolicitado,convert(varchar(max),z.dsJustificativa) as JustProponente,
             ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSugerido,convert(varchar(max),b.Justificativa) as JustParecerista,
             e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,k.QtDias as QtdeDias,
             k.TpDespesa,k.TpPessoa,k.nrContrapartida,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,f.UF,f.Municipio,
             ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
       convert(varchar(max),k.dsJustificativa) as JustComponente
       FROM Projetos a
       INNER JOIN tbPlanilhaProjeto b on (a.idPronac = b.idPronac)
       INNER JOIN tbPlanilhaProposta z on (b.idPlanilhaProposta=z.idPlanilhaProposta)
       INNER JOIN tbPlanilhaAprovacao k on (b.idPlanilhaProposta=k.idPlanilhaProposta)
       LEFT JOIN Produto c on (b.idProduto = c.Codigo)
       INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
       INNER JOIN tbPlanilhaUnidade e on (b.idUnidade = e.idUnidade)
       INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
       INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
       INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
       WHERE k.stAtivo = 'S' 
            AND (ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) <> ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) OR
                 ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) <> ROUND((k.QtItem * k.nrOcorrencia * k.vlUnitario),2))
            AND a.idPronac = @idPronac
       ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
   END
ELSE
-- =========================================================================================
-- REMANEJAMENTO AT� 20%
-- =========================================================================================
IF @TipoPlanilha = 5
   BEGIN
      IF NOT EXISTS( SELECT TOP 1 * FROM tbPlanilhaAprovacao WHERE idPronac = @idPronac AND stAtivo = 'S' AND tpPlanilha = 'RP')
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN k.idProduto = 0
						THEN 'Administra��o do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				 e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				 ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
				 (SELECT sum(b1.vlComprovacao) AS vlPagamento 
                         FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                         INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                         INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                         WHERE c1.idPlanilhaItem = k.idPlanilhaItem AND c1.idPronac = k.idPronac 
                         GROUP BY c1.idPlanilhaItem) as vlComprovado,
				 k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente
			   FROM Projetos a
			   INNER JOIN tbPlanilhaAprovacao k on (a.idPronac = k.idPronac)
			   INNER JOIN tbPlanilhaProposta z on (k.idPlanilhaProposta=z.idPlanilhaProposta)
			   LEFT JOIN Produto c on (k.idProduto = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade e on (k.idUnidade = e.idUnidade)
			   INNER JOIN tbPlanilhaItens i on (k.idPlanilhaItem=i.idPlanilhaItens)
			   INNER JOIN Verificacao x on (k.nrFonteRecurso = x.idVerificacao)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   WHERE k.stAtivo = 'N' 
					AND k.tpPlanilha = 'RP'
					AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					     OR (k.dsJustificativa IS NOT NULL))
					AND a.idPronac = @idPronac
			   ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	     END
	  ELSE
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN k.idProduto = 0
						THEN 'Administra��o do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				 e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				 ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
				 (SELECT sum(b1.vlComprovacao) AS vlPagamento 
                         FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                         INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                         INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                         WHERE c1.idPlanilhaItem = k.idPlanilhaItem AND c1.idPronac = k.idPronac 
                         GROUP BY c1.idPlanilhaItem) as vlComprovado,
				 k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente
			   FROM Projetos a
			   INNER JOIN tbPlanilhaAprovacao k on (a.idPronac = k.idPronac)
			   INNER JOIN tbPlanilhaProposta z on (k.idPlanilhaProposta=z.idPlanilhaProposta)
			   LEFT JOIN Produto c on (k.idProduto = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade e on (k.idUnidade = e.idUnidade)
			   INNER JOIN tbPlanilhaItens i on (k.idPlanilhaItem=i.idPlanilhaItens)
			   INNER JOIN Verificacao x on (k.nrFonteRecurso = x.idVerificacao)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   WHERE k.stAtivo = 'S' 
			        AND k.tpPlanilha = 'RP'
					AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					     OR (k.dsJustificativa IS NOT NULL))
					AND a.idPronac = @idPronac
			   ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	   END	  
   END	  
ELSE
-- =========================================================================================
-- REMANEJAMENTO, COMPLEMENTA��O E REDU��O
-- =========================================================================================
IF @TipoPlanilha = 6
   BEGIN
      IF EXISTS(SELECT TOP 1 * FROM tbPlanilhaAprovacao a
	                           INNER JOIN tbReadequacao b on (a.idPronac = b.idPronac)
	                           WHERE a.idPronac = @idPronac 
								     AND a.stAtivo = 'N' 
								     AND a.tpPlanilha = 'SR'
									 AND b.idTipoReadequacao = 2 
                                     AND b.siEncaminhamento <> 15
		                             AND b.stEstado = 0)
									  --AND b.siEncaminhamento IN (1,3,4,5,6,7,8,10,12,14))
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN k.idProduto = 0
						THEN 'Administra��o do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				 e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				 ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
				 (SELECT sum(b1.vlComprovacao) AS vlPagamento 
                         FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                         INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                         INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                         WHERE c1.idPlanilhaItem = k.idPlanilhaItem AND c1.idPronac = k.idPronac 
                         GROUP BY c1.idPlanilhaItem) as vlComprovado,
				 k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente,k.tpAcao
			   FROM Projetos a
			   INNER JOIN tbPlanilhaAprovacao k on (a.idPronac = k.idPronac)
			   LEFT JOIN Produto c on (k.idProduto = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade e on (k.idUnidade = e.idUnidade)
			   INNER JOIN tbPlanilhaItens i on (k.idPlanilhaItem=i.idPlanilhaItens)
			   INNER JOIN Verificacao x on (k.nrFonteRecurso = x.idVerificacao)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   WHERE k.stAtivo = 'N' 
			   		AND k.tpPlanilha = 'SR'
					AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					     OR (k.dsJustificativa IS NOT NULL))
					AND a.idPronac = @idPronac
			   ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	     END
	  ELSE
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN k.idProduto = 0
						THEN 'Administra��o do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				 e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				 ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
				 (SELECT sum(b1.vlComprovacao) AS vlPagamento 
                         FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                         INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                         INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                         WHERE c1.idPlanilhaItem = k.idPlanilhaItem AND c1.idPronac = k.idPronac 
                         GROUP BY c1.idPlanilhaItem) as vlComprovado,
				 k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente,k.tpAcao
			   FROM Projetos a
			   INNER JOIN tbPlanilhaAprovacao k on (a.idPronac = k.idPronac)
			   LEFT JOIN Produto c on (k.idProduto = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade e on (k.idUnidade = e.idUnidade)
			   INNER JOIN tbPlanilhaItens i on (k.idPlanilhaItem=i.idPlanilhaItens)
			   INNER JOIN Verificacao x on (k.nrFonteRecurso = x.idVerificacao)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   WHERE k.stAtivo = 'S' 
			        AND k.tpPlanilha = 'SR'
					AND k.tpAcao <> 'E'
					AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					     OR (k.dsJustificativa IS NOT NULL))
					AND a.idPronac = @idPronac
			   ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	   END	  
   END 

GO

GRANT EXECUTE ON dbo.spPlanilhaOrcamentaria TO usuarios_internet
GO
