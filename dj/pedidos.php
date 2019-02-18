<?php
require_once("inc/protecao-final-dj.php");

list($codigo_stm, $dj_login, $dj_senha) = explode("|",$_SESSION["dj_logado"]);

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$codigo_stm."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-dj.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-dj.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
    setTimeout("window.location.reload(true);",30000);
   };
</script>
</head>

<body>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid; margin-top:5px" id="tab" class="sortable">
<tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="15%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_pedidos_musicais_data']; ?></td>
    <td width="35%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_pedidos_musicais_ouvinte']; ?></td>
    <td width="40%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_streaming_pedidos_musicais_musica']; ?></td>
    <td width="5%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;</td>
    </tr>
<?php
$pagina_atual = query_string('2');

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
<td height='25' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;<img src='/img/icones/img-icone-fechar.png' onclick='executar_acao_streaming_dj(\"".$pedido_code."\",\"remover-pedido-musical\" );' style='cursor:pointer; margin-left:20px' width='10' height='10' border='0' align='absmiddle' /></td>
</tr>";

}
?>
</table>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="border:#D5D5D5 1px solid;">
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
?>      </td>
  </tr>
  </table>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
