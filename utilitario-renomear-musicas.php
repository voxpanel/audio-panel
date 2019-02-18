<?php
require_once("admin/inc/protecao-final.php");

if(isset($_POST["enviar"])) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$resultado = file_get_contents("http://".$dados_servidor["ip"].":555/renomear-musicas.php?porta=".$dados_stm["porta"]."");

if($resultado) {

list($total_musicas, $log) = explode("|",$resultado);

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_renomear_musicas_resultado_ok']."","ok");
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_renomear_musicas_resultado_alerta']." ".$total_musicas."","alerta");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_acao_renomear_musicas_resultado_erro']."","erro");

}

}
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
<form action="/utilitario-renomear-musicas" method="post">
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_renomear_musicas_info']; ?></td>
  </tr>
</table>
<table width="700" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo $lang['lang_info_renomear_musicas_tab_titulo']; ?></strong></div>
   		  <div class="texto_medio" id="quadro-conteudo">
          <?php if($resultado && $log) { ?>
   		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="40" align="center"><textarea style="width:98%; height:200px"><?php echo $log; ?></textarea></td>
                </tr>
            </table>
            <?php } else { ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="40" align="center"><input name="enviar" type="hidden" id="enviar" value="sim" />
                  <input type="submit" class="botao" value="<?php echo $lang['lang_info_renomear_musicas_botao_submit']; ?>" /></td>
                </tr>
            </table>
           <?php } ?>
   		  </div>
      </div>
      </td>
    </tr>
  </table>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_renomear_musicas_info_playlist']; ?></td>
    </tr>
  </table>
</form>
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