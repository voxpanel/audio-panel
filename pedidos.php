<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$porta_code = code_decode($dados_stm["porta"],"E");

$url_pedidos = (!empty($dados_revenda["dominio_padrao"])) ? "player.".$dados_revenda["dominio_padrao"]."" : "player.".$dados_config["dominio_padrao"]."";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
    setTimeout("window.location.reload(true);",30000);
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
<div id="quadro-topo"><span><img src="/img/icones/img-icone-janela-64x64.png" width="16" height="16" onclick="abrir_janela('/pedidos',905,600 );" style="cursor:pointer; padding-top:7px;" title="Nova Janela/New Windows" /></span><strong><?php echo $lang['lang_info_streaming_pedidos_musicais_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_pedidos_musicais_tab_titulo']; ?></h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="15%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_pedidos_musicais_data']; ?></td>
      <td width="40%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_pedidos_musicais_ouvinte']; ?></td>
      <td width="40%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_pedidos_musicais_musica']; ?></td>
      <td width="5%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;</td>
    </tr>
<?php
$pagina_atual = query_string('1');

$sql = mysql_query("SELECT * FROM pedidos_musicais WHERE codigo_stm = '".$dados_stm["codigo"]."'");
$lpp = 100; // total de registros por página
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("SELECT * FROM pedidos_musicais WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo DESC LIMIT $inicio, $lpp");
while ($dados_pedido = mysql_fetch_array($sql)) {

$pedido_code = code_decode($dados_pedido["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".formatar_data($dados_stm["formato_data"], $dados_pedido["data"], $dados_stm["timezone"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_pedido["nome"]." - ".$dados_pedido["email"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_pedido["musica"]."</td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;<img src='/img/icones/img-icone-fechar.png' onclick='executar_acao_streaming_autodj(\"".$pedido_code."\",\"remover-pedido-musical\" );' style='cursor:pointer; margin-left:20px' width='16' height='16' border='0' align='absmiddle' /></td>
</tr>";

}
?>
  </table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border:#D5D5D5 1px solid;">
  <tr>
    <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("SELECT * FROM pedidos_musicais WHERE codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">Nenhum log encontrado.</span>";
} else {
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/".query_string('0')."/$i";
              echo " <a href=\"$url\" class=\"texto_padrao\" title=\"Ir para p&aacute;gina $linksp\">$linksp</a></span>";
      }
	}

}
?>
    </td>
  </tr>
</table></td>
    </tr>
</table>
  </div>
  <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_pedidos_musicais_widget_tab_titulo']; ?></h2>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">
 			 <tr>
   				 <td align="left" class="texto_padrao" scope="col" style="padding-left:5px"><br />
                 <?php echo $lang['lang_info_streaming_pedidos_musicais_widget_info']; ?><br /><br />
                 <span class="texto_padrao_destaque">URL:</span><br />
<input type="text" value="<?php echo "http://".$url_pedidos."/pedido/".$dados_stm["porta"].""; ?>" style="width:99%; height:30px"  onclick="this.select()" readonly="readonly" /><br />
<br />
<span class="texto_padrao_destaque">Widget:</span><br />
<textarea readonly="readonly" style="width:99%; height:300px"  onclick="this.select()">
<script type="text/javascript">
function abrir_pedido( url,largura,altura ) {
window.open( url, "","width="+largura+",height="+altura+",toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO" );
}
</script>
<a href="javascript:abrir_pedido( '<?php echo "http://".$url_pedidos."/pedido/".$dados_stm["porta"].""; ?>','505','180' );">Pedir M&uacute;sica</a>
</textarea>
                 </td>
  			</tr>
		</table>
  </div>
    </div>
      </div>
      </div>
<br />
<br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>