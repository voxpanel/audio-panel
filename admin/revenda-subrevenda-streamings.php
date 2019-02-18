<?php
require_once("inc/protecao-revenda.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode(query_string('2'),"D")."') AND tipo = '2'"));

// Estatísticas de Uso do Plano
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."'"));
$espaco = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."'"));

$porcentagem_uso_plano_stm = ($dados_subrevenda["streamings"] == 0 || $dados_subrevenda["streamings"] == 999999) ? "0" : $total_streamings*100/$dados_subrevenda["streamings"];
$porcentagem_uso_plano_ouvintes = ($dados_subrevenda["ouvintes"] == 0 || $dados_subrevenda["ouvintes"] == 999999) ? "0" : $ouvintes["total"]*100/$dados_subrevenda["ouvintes"];
$porcentagem_uso_plano_espaco_ftp = ($dados_subrevenda["espaco"] == 0 || $dados_subrevenda["espaco"] == 999999) ? "0" : $espaco["total"]*100/$dados_subrevenda["espaco"];

// Dados do Plano
$plano_limite_stm = ($dados_subrevenda["streamings"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : $dados_subrevenda["streamings"];
$plano_limite_ouvintes = ($dados_subrevenda["ouvintes"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : $dados_subrevenda["ouvintes"];
$plano_limite_espaco_ftp = ($dados_subrevenda["espaco"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : tamanho($dados_subrevenda["espaco"]);

// Verifica se a sub revenda existe
if($dados_subrevenda["codigo_revenda"] != $_SESSION["code_user_logged"]) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["aviso_acesso_negado"] = status_acao(lang_info_acesso_subrevenda_nao_permitido,"erro");

header("Location: /admin/revenda-informacoes");
exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Informações da Sub Revenda</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax-revenda.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
  <table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
        <div id="quadro-topo"> <strong><?php echo lang_info_subrevenda_streamings_tab_titulo; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
          <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
            <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
              <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_subrevenda_streamings_porta; ?></td>
              <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_subrevenda_streamings_porta_dj; ?></td>
              <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_subrevenda_streamings_servidor; ?></td>
              <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_subrevenda_streamings_plano; ?></td>
              <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_subrevenda_streamings_status; ?></td>
              <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_subrevenda_streamings_acoes; ?></td>
            </tr>
<?php
$sql = mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."' ORDER by porta ASC");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$cor_status = ($dados_stm["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$status_inicial = ($dados_stm["status"] != 1) ? "Bloqueado" : "<img src='/admin/img/spinner.gif' />";

$servidor = ($dados_subrevenda["dominio_padrao"]) ? strtolower($dados_servidor["nome"]).".".$dados_subrevenda["dominio_padrao"] : strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];

$porta_code = code_decode($dados_stm["porta"],"E");

echo "<tr style='background-color:".$cor_status.";cursor:pointer' onmouseover='this.style.backgroundColor=\"#F3F3F3\"' onmouseout='this.style.backgroundColor=\"".$cor_status."\"'>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["porta"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["porta_dj"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$servidor."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["ouvintes"]." ".lang_info_subrevenda_streamings_ouvintes." | ".$dados_stm["bitrate"]." Kbps | ".tamanho($dados_stm["espaco"])." FTP</td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno' id='".$porta_code."'>".$status_inicial."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>
<select style='width:100%' onchange='executar_acao_streaming_subrevenda(\"".$porta_code."\",this.value);'>
  <option value='' selected='selected'>".lang_info_subrevenda_streamings_escolha_acao."</option>
  <option value='informacoes'>".lang_info_subrevenda_streamings_acao_informacoes."</option>
  <option value='mover-streaming'>".lang_info_subrevenda_streamings_acao_mover_streaming."</option>
  <option value='mover-streaming-subrevenda'>".lang_info_subrevenda_streamings_acao_mover_streaming_subrevenda."</option>
</select>
</td>
</tr>";

// Adiciona na lista de checagem do status apenas se estiver ativo
if($dados_stm["status"] == 1) {
$array_streamings .= "".$porta_code."|";
}

}

?>
          </table>
<?php
$total_registros = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."' ORDER by porta ASC"));

if($total_registros == 0) {
echo '<table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">
    <tr>
      <td height="25" align="center" class="texto_padrao">'.lang_info_sem_registros.'</td>
    </tr>
</table>';
}
?>
        </div>
      </div></td>
    </tr>
  </table>
</div>
<script type="text/javascript">
// Checar o status dos streamings
checar_status_streamings_subrevenda('<?php echo $array_streamings; ?>');
</script>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>