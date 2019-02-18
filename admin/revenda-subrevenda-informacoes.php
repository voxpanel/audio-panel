<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode(query_string('2'),"D")."'"));

// Estatísticas de Uso do Plano
$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_subrevenda["codigo"]."'"));
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."'"));
$espaco = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_subrevenda["codigo"]."'"));

$porcentagem_uso_plano_stm = ($dados_subrevenda["streamings"] == 0 || $dados_subrevenda["streamings"] == 999999) ? "0" : $total_streamings*100/$dados_subrevenda["streamings"];
$porcentagem_uso_plano_ouvintes = ($dados_subrevenda["ouvintes"] == 0 || $dados_subrevenda["ouvintes"] == 999999) ? "0" : $ouvintes["total"]*100/$dados_subrevenda["ouvintes"];
$porcentagem_uso_plano_espaco_ftp = ($dados_subrevenda["espaco"] == 0 || $dados_subrevenda["espaco"] == 999999) ? "0" : $espaco["total"]*100/$dados_subrevenda["espaco"];

// Dados do Plano
$plano_limite_subrevenda = ($dados_subrevenda["subrevendas"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_pagina_informacoes_subrevenda_plano_ilimitado.'</span>' : $dados_subrevenda["subrevendas"];
$plano_limite_stm = ($dados_subrevenda["streamings"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_pagina_informacoes_subrevenda_plano_ilimitado.'</span>' : $dados_subrevenda["streamings"];
$plano_limite_ouvintes = ($dados_subrevenda["ouvintes"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_pagina_informacoes_subrevenda_plano_ilimitado.'</span>' : $dados_subrevenda["ouvintes"];
$plano_limite_espaco_ftp = ($dados_subrevenda["espaco"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_pagina_informacoes_subrevenda_plano_ilimitado.'</span>' : tamanho($dados_subrevenda["espaco"]);

$status = ($dados_subrevenda["status"] == 1) ? '<span class="texto_padrao_verde">'.lang_info_pagina_informacoes_subrevenda_status_ativo.'</span>' : '<span class="texto_padrao_vermelho">'.lang_info_pagina_informacoes_subrevenda_status_bloqueado.'</span>';

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
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
      <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_subrevenda_tab_info_titulo; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="427" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="110" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_codigo; ?></td>
                <td width="317" align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo $dados_subrevenda["id"]; ?></td>
              </tr>
              <tr>
                <td width="110" height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_email; ?></td>
                <td width="317" align="left" class="texto_padrao_pequeno"><?php echo $dados_subrevenda["email"]; ?></td>
              </tr>
              <tr>
                <td width="110" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_status; ?></td>
                <td width="317" align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo $status; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_data_cadastro; ?></td>
                <td align="left" class="texto_padrao_pequeno"><?php echo date_format(date_create($dados_subrevenda["data_cadastro"]), 'd/m/Y H:i:s'); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_ultimo_acesso; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo "".date_format(date_create($dados_subrevenda["ultimo_acesso_data"]), 'd/m/Y H:i:s')." - ".$dados_subrevenda["ultimo_acesso_ip"].""; ?></td>
              </tr>
              <?php if($dados_revenda["subrevendas"] > 0) { ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno">&nbsp;</td>
              </tr>
              <?php } ?>
            </table>
      </div>
    </div></td>
    <td align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
      <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_subrevenda_tab_plano_titulo; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="427" border="0" cellpadding="0" cellspacing="0">
        <?php if($dados_revenda["subrevendas"] > 0) { ?>
        	  <tr>
                <td width="110" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_plano_limite_subrevendas; ?></td>
                <td width="317" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $total_subrevendas." / ".$plano_limite_subrevenda; ?></td>
              </tr>
        <?php } ?>
              <tr>
                <td width="110" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_plano_limite_stm; ?></td>
                <td width="317" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $total_streamings." / ".$plano_limite_stm; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_plano_limite_ouvintes; ?></td>
                <td align="left" class="texto_padrao"><?php echo $ouvintes["total"]." / ".$plano_limite_ouvintes; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_plano_limite_bitrate; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo $dados_subrevenda["bitrate"]; ?> Kbps</td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_plano_limite_espaco_ftp; ?></td>
                <td align="left" class="texto_padrao_pequeno"><?php echo tamanho($espaco["total"])." / ".$plano_limite_espaco_ftp; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_subrevenda_plano_aacplus; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo str_replace("nao","Não",str_replace("sim","Sim",$dados_subrevenda["aacplus"])); ?></td>
              </tr>
            </table>
      </div>
    </div></td>
    </tr>
</table>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>