<?php if($this->dados){ ?>
<!-- ========== IN�CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>

<!-- ========== IN�CIO AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
<script type="text/javascript">
<!--
function layout_fluido()
{
	var janela         = $(window).width();
	var fluidNavGlobal = janela - 245;
	var fluidConteudo  = janela - 253;
	var fluidTitulo    = janela - 252;
	var fluidRodape    = janela - 19;
	$("#navglobal").css("width", fluidNavGlobal);
	$("#conteudo").css("width", fluidConteudo);
	$("#titulo").css("width", fluidTitulo);
	$("#rodapeConteudo").css("width", fluidConteudo);
	$("#imagemRodape").css("width", fluidConteudo);
	$("#rodape").css("width", fluidRodape);
	$("#conteudo").css("min-height", $('#menuContexto').height()); // altura minima do conteudo
	$("#rodapeConteudo").css("margin-left", "225px");
	$(".sanfonaDiv").css("clear", "both");
	$(".sanfonaDiv").css("width", "91%");
} // fecha fun&ccedil;&atilde;o layout_fluido()

$(document).ready(function()
{
	$('a.sanfona').click(function()
	{
		$(this).next().toggle('fast');
	});
});
//-->
</script>
<!-- ========== FIM AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->

<!-- in&iacute;cio: navega&ccedil;&atilde;o local #qm0 -->
<script type="text/javascript">
$(function(){
   carregaPagina();
});

function carregaPagina(){
    $.get(
        '<?php echo $this->url(array('controller' => 'checarregularidade', 'action' => 'index')); ?>',
        { CgcCpf : <?php echo $this->dados[0]->CNPJCPF ?>},
            function(data){
                $('#UC27').html(data)
            }
        );
   }
</script>


<div id="menuContexto" style="margin-bottom: 15px;">
	<?php
	$_id = $this->dados[0]->IdPRONAC;
	if ($this->verificarStatusDiligencia($_id) == 'A') :
	?>
    <!--<div class="top"></div>
    <div id="qm0" class="qmmc">
    	<a class="no_seta last" onclick="return diligenciarNovoPadrao(<?php echo $this->dados[0]->IdPRONAC; ?>)" href="#" title="Ir para Despachar Documentos">Diligenciar</a>
    </div>
    <div class="bottom"></div>-->
    <?php endif; ?>
    <div id="alertar"></div>
    <div id="espaco">
		<br /><br />
		<div id="UC27" style="width:180px; height:280px; margin-left: 10px;"></div>
        <table style="width:16.8em; height:13em; margin: 1.5em 0em 0em 1em; border: 0;">
            <tr>
                <th colspan="2"><span style="font-weight: normal; font-size: 10px">TIPO DE SOLICITA&ccedil;&atilde;O</span></th>
            </tr>
            <tr>
                <td width="1%"; style="font-size: 40px; color: blue;">&bull;</td>
                <td width="99%" style="text-align:left;" >Readequa&ccedil;&atilde;o de itens do produto</td>
            </tr>
            <tr>
                <td style="font-size: 40px; color: green;">&bull;</td>
                <td style="text-align:left; padding-left:3%;">Inclus&atilde;o de novo produto</td>
            </tr>
            <tr>
                <td style="font-size: 40px; color: red;">&bull;</td>
                <td style="text-align:left;">Exclus&atilde;o de produto</td>
            </tr>
            <tr>
                <td style="font-size: 40px; color: black;">&bull;</td>
                <td style="text-align:left; padding-left:3%;">Nenhuma solicita&ccedil;&atilde;o de altera&ccedil;&atilde;o</td>
            </tr>
        </table>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
<?php } ?>


<table class="tabela">
	<tr>
		<th class="<?php if (strstr($this->url(), 'planilhaaprovada')== 'planilhaaprovada') {echo "fundo_linha4";} else { echo "fundo_linha2"; } ?>"><a href="<?php echo $this->url(array('controller' => 'verificarsolicitacaodereadequacoes', 'action' => 'planilhaaprovada'));?>?idPronac=<?php echo $_GET['idPronac']?>">Planilha Aprovada</a></th>
		<th class="<?php if (strstr($this->url(), 'planilhasolicitada')    == 'planilhasolicitada')     {echo "fundo_linha4";} else { echo "fundo_linha2"; } ?>"><a href="<?php echo $this->url(array('controller' => 'verificarsolicitacaodereadequacoes', 'action' => 'planilhasolicitada'));    ?>?idPronac=<?php echo $_GET['idPronac']?>">Planilha Readequada</a></th>
	</tr>
</table>