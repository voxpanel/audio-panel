<?php
require_once("inc/protecao-revenda.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'"));

// Verifica se o streaming é do cliente
if($dados_stm["codigo_cliente"] != $_SESSION["code_user_logged"]) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["aviso_acesso_negado"] = status_acao(lang_info_acesso_stm_nao_permitido,"erro");

header("Location: /admin/revenda-informacoes");
exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong><?php echo lang_info_streaming_logs_tab_titulo; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo lang_info_streaming_logs_info; ?><br />
<br />
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="13%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_logs_data; ?></td>
      <td width="11%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_logs_ip; ?></td>
      <td width="16%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_logs_navegador; ?></td>
      <td width="60%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_logs_log; ?></td>
    </tr>
<?php
$pagina_atual = query_string('3');

$sql = mysql_query("SELECT * FROM logs_streamings WHERE codigo_stm = '".$dados_stm["codigo"]."'");
$lpp = 100; // total de registros por página
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data FROM logs_streamings WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by data DESC LIMIT $inicio, $lpp");
while ($dados_log = mysql_fetch_array($sql)) {

$log = str_replace("<br>","",$dados_log["log"]);
$log = str_replace("<br />","",$log);

$log = (strlen($log) > 105) ? substr($log, 0, 105)."..." : $log;

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_log["data"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_log["ip"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_log["navegador"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$log."</td>
</tr>";

}
?>
  </table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
  <tr>
    <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("SELECT * FROM logs_streamings WHERE codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">".lang_info_sem_registros."</span>";
} else {
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" $linksp\">$linksp</span>";
      } else {
              $url = "/admin/".query_string('1')."/".query_string('2')."/$i";
              echo " <a href=\"$url\" class=\"texto_padrao\">$linksp</a></span>";
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
  </div>
<br />
<br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>