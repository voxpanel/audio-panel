<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

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
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_app_painel_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo $lang['lang_info_streaming_app_painel_info']; ?>
    <br />
	<br />
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:100px">
      <tr>
        <td width="50%" height="35" align="center" class="texto_padrao_destaque" scope="col">Qr Code Google Play App</td>
        <td width="50%" align="center" class="texto_padrao_destaque" scope="col">Qr Code Google Play Web</td>
      </tr>
      <tr>
        <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=market://details?id=com.painel.streaming.movel" /></td>
        <td align="center"><img src="http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=https://play.google.com/store/apps/details?id=com.painel.streaming.movel" /></td>
      </tr>
      <tr>
        <td height="50" align="center"><img src="img/img-logo-google-play.png" alt="Google Play" width="150" height="44" /></td>
        <td height="50" align="center"><img src="img/img-logo-google-play.png" alt="Google Play" width="150" height="44" /></td>
      </tr>
    </table>
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