<?php
require_once("inc/protecao-revenda.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));

$limite_ouvintes = ($dados_stm["ouvintes"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_ilimitado.'</span>' : $dados_stm["ouvintes"];

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
<title>Informações do Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax.js"></script>
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
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="770" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<!--
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 2px dashed">
	<tr>
      <td width="40" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="850" align="left" class="texto_padrao_vermelho_destaque" scope="col"><?php echo lang_info_pagina_informacoes_streaming_aviso_gerenciamento; ?></td>
    </tr>
</table>
-->
<table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_streaming_tab_stm; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_stm_porta; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["porta"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_stm_porta_dj; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_stm["porta_dj"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_stm_servidor; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_stm_shoutcast; ?></td>
                <td align="left" class="texto_padrao_pequeno"><a href="javascript:abrir_janela('http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>',720,500);">http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?></a></td>
              </tr>
              <?php if($dados_stm["aacplus"] == 'sim') { ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_stm_rtmp; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno">rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_stm_identificacao; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["identificacao"]; ?></td>
              </tr>
            </table>
        </div>
      </div></td>
      <td align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_streaming_tab_plano; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_plano_servidor; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_servidor["nome"]; ?></td>
              </tr>
              <?php if($dados_stm["aacplus"] == 'sim') { ?>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_plano_rtmp; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_servidor_aacplus["nome"]; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_plano_ouvintes; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $limite_ouvintes; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_plano_ftp; ?></td>
                <td align="left" class="texto_padrao"><?php echo tamanho($dados_stm["espaco"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_plano_playlists; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $total_playlists; ?> <span class="texto_padrao_pequeno"><?php echo lang_info_pagina_informacoes_streaming_tab_plano_playlists_info; ?></span></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_plano_bitrate; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_stm["bitrate"]; ?> Kbps</td>
              </tr>
            </table>
        </div>
      </div></td>
      <td align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_streaming_tab_ftp; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_ftp_endereco; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_ftp_login; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_stm["porta"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_streaming_tab_ftp_espaco; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo tamanho($dados_stm["espaco"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
            </table>
        </div>
      </div></td>
    </tr>
    <tr>
      <td height="5" colspan="3" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" colspan="3" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_streaming_tab_avisos_stm; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="880" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_status_erro_pequeno">
<?php
carregar_avisos_streaming_revenda($dados_stm["porta"],$dados_servidor["codigo"]);
?>
</td>
    </tr>
</table>
    </div>
      </div>
      </td>
    </tr>
    <tr>
      <td height="5" colspan="3" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" colspan="3" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_streaming_tab_avisos_manutencao; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
                    <table width="880" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_status_erro_pequeno">
<?php if($dados_servidor["status"] == "on") { ?>
<span class="texto_padrao"><?php echo lang_info_pagina_informacoes_streaming_tab_avisos_manutencao_sem_registros; ?></span>
<?php } else { ?>
<?php echo $dados_servidor["mensagem_manutencao"];?>
<?php } ?>    </td>
    </tr>
</table>
    </div>
      </div>
      </td>
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