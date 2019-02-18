<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
   <?php if($_POST["ip_ftp_remoto"] && $_POST["usuario_ftp_remoto"] && $_POST["senha_ftp_remoto"]) { ?>
   migrar_musicas_ftp_remoto( '<?php echo $dados_servidor["ip"]; ?>', '<?php echo $dados_stm["porta"]; ?>', '<?php echo $_POST["ip_ftp_remoto"]; ?>', '<?php echo $_POST["usuario_ftp_remoto"]; ?>', '<?php echo $_POST["senha_ftp_remoto"]; ?>' )
   <?php } ?>
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
<?php if($_POST["ip_ftp_remoto"] && $_POST["usuario_ftp_remoto"] && $_POST["senha_ftp_remoto"]) { ?>
  <table width="490" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="460" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_migrar_musicas_info_fechar_janela']; ?></td>
    </tr>
  </table>
  <table width="500" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo $lang['lang_info_migrar_musicas_tab_titulo']; ?></strong></div>
   		  <div class="texto_padrao_pequeno" id="quadro-conteudo">
		  <span id="log_migracao"></span>
          </div>
      </div>
      </td>
    </tr>
  </table>
<?php } else { ?>
<form action="/utilitario-migrar-musicas" method="post">
  <table width="500" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo $lang['lang_info_migrar_musicas_tab_titulo']; ?></strong></div>
   		  <div class="texto_medio" id="quadro-conteudo">
          <table width="477" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
            <tr>
              <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
              <td width="447" align="left" class="texto_padrao_destaque" scope="col"><span class="texto_padrao_destaque"><?php echo $lang['lang_info_migrar_musicas_info_dados_formulario']; ?></span></td>
            </tr>
          </table>
          <table width="477" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="147" height="30" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_migrar_musicas_ip_ftp_remoto']; ?></td>
                <td width="330"><input name="ip_ftp_remoto" type="text" class="input" id="ip_ftp_remoto" style="width:250px;" value="" /></td>
              </tr>
              <tr>
                <td height="30" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_migrar_musicas_usuario_ftp_remoto']; ?></td>
                <td><input name="usuario_ftp_remoto" type="text" class="input" id="usuario_ftp_remoto" style="width:250px;" value="" /></td>
              </tr>
              <tr>
                <td height="30" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_migrar_musicas_senha_ftp_remoto']; ?></td>
                <td><input name="senha_ftp_remoto" type="text" class="input" id="senha_ftp_remoto" style="width:250px;" value="" /></td>
              </tr>
              <tr>
                <td height="40" class="texto_padrao_destaque">&nbsp;</td>
                <td><input type="submit" class="botao" value="<?php echo $lang['lang_acao_migrar_musicas_botao_submit']; ?>" /></td>
              </tr>
            </table>
   		  </div>
      </div>
      </td>
    </tr>
  </table>
  </form>
  <?php } ?>
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