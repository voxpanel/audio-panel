<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ssh.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

// Conex�o SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->baixar_arquivo("/home/streaming/logs/log-".$dados_stm["porta"].".log", "temp/log-".$dados_stm["porta"].".log");

$log = file_get_contents("temp/log-".$dados_stm["porta"].".log");

unlink("temp/log-".$dados_stm["porta"].".log");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_logs_shoutcast_info']; ?></td>
  </tr>
</table>
<table width="700" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo $lang['lang_info_logs_shoutcast_tab_titulo']; ?></strong></div>
   		  <div class="texto_medio" id="quadro-conteudo">
   		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="40" align="center">
                  <textarea readonly="readonly" style="width:670px; height:300px; font-size:11px"><?php echo $log; ?></textarea>
                </td>
              </tr>
            </table>
   		  </div>
      </div>
      </td>
    </tr>
  </table>
</div>
<!-- In�cio div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>