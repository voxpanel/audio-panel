<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$porta_code = code_decode($dados_stm["porta"],"E");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_dados_conexao_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_dados_conexao_aba_streaming']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor/Server</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Porta/Port</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["porta"]; ?></td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Senha/Password</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["senha"]; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Bitrate</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["bitrate"]; ?> Kbps</td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">ShoutCast</td>
            <td align="left" class="texto_padrao_vermelho_destaque">v2</td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">SID</td>
            <td align="left" class="texto_padrao_pequeno">1 <?php echo $lang['lang_info_streaming_dados_conexao_streaming_info_sid']; ?></td>
          </tr>
        </table>
      </div>
      <?php if($dados_stm["autodj"] == "sim") { ?>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_dados_conexao_aba_autodj']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor/Server</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Porta/Port</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["porta_dj"]; ?></td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Senha/Password</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $lang['lang_info_streaming_dados_conexao_autodj_info_senha']; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">ShoutCast</td>
            <td align="left" class="texto_padrao_vermelho_destaque">v1</td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
            <td align="left" class="texto_padrao_pequeno">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
            <td align="left" class="texto_padrao_pequeno">&nbsp;</td>
          </tr>
        </table>
      </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_dados_conexao_aba_ftp']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor/Server/Host</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Usu&aacute;rio/User/Login</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["porta"]; ?></td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Senha/Password</td>
            <td align="left" class="texto_padrao_pequeno"><?php echo $dados_stm["senha"]; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Porta/Port</td>
            <td align="left" class="texto_padrao_pequeno">21 <?php echo $lang['lang_info_streaming_dados_conexao_ftp_info_porta']; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">&nbsp;</td>
            <td align="left" class="texto_padrao_pequeno">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">&nbsp;</td>
            <td align="left" class="texto_padrao_pequeno">&nbsp;</td>
          </tr>
        </table>
      </div>
      <?php } ?>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_dados_conexao_aba_shoutcast_admin']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">URL</td>
            <td align="left" class="texto_padrao_pequeno"><a href="javascript:abrir_janela('http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>',720,500);">http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?></a></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_streaming_dados_conexao_shoutcast_admin_usuario']; ?></td>
            <td align="left" class="texto_padrao_pequeno">admin</td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_streaming_dados_conexao_shoutcast_admin_senha']; ?></td>
            <td align="left" class="texto_padrao_pequeno">
			<?php
            if($dados_stm["senha_admin"]) {
			echo $dados_stm["senha_admin"];
			} else {
			echo $lang['lang_info_streaming_dados_conexao_shoutcast_admin_senha_info'];
			}
			?></td>
          </tr>
        </table>
      </div>
    </div>
    </td>
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
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>