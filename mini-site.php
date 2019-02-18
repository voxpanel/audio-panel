<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

if(isset($_POST["alterar"])) {

mysql_query("Update streamings set mini_site_dominio = '".$_POST["mini_site_dominio"]."', mini_site_cor_fundo = '".$_POST["mini_site_cor_fundo"]."', mini_site_cor_topo = '".$_POST["mini_site_cor_topo"]."', mini_site_cor_texto_topo = '".$_POST["mini_site_cor_texto_topo"]."', mini_site_cor_texto_padrao = '".$_POST["mini_site_cor_texto_padrao"]."', mini_site_cor_texto_rodape = '".$_POST["mini_site_cor_texto_rodape"]."', mini_site_exibir_chat = '".$_POST["mini_site_exibir_chat"]."', mini_site_exibir_xat_id = '".$_POST["mini_site_exibir_xat_id"]."', mini_site_url_facebook = '".$_POST["mini_site_url_facebook"]."', mini_site_url_twitter = '".$_POST["mini_site_url_twitter"]."' where codigo = '".$dados_stm["codigo"]."'");

if(!mysql_error()) {

// Atualiza Cache
@file_get_contents("http://player.audiocast.ml/atualizar-cache-mini-site/".$dados_stm["porta"]."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_mini_site_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_mini_site_resultado_erro']." ".mysql_error()."","erro");

}

header("Location: /mini-site");
exit();

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/spectrum.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/spectrum.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form method="post" action="/configuracoes-painel" style="padding:0px; margin:0px" name="config_painel" onsubmit="selectAll('atalhos');">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_mini_site_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
  	  <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_mini_site_info']; ?></td>
     </tr>
    </table>    </td>
  </tr>
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_mini_site_aba_configuracoes']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_dominio']; ?></td>
            <td width="540" align="left"><input name="mini_site_dominio" type="text" class="input" id="mini_site_dominio" style="width:250px;" value="<?php echo $dados_stm["mini_site_dominio"]; ?>" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_url_facebook']; ?></td>
            <td align="left"><input name="mini_site_url_facebook" type="text" class="input" id="mini_site_url_facebook" style="width:250px;" value="<?php echo $dados_stm["mini_site_url_facebook"]; ?>" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_url_twitter']; ?></td>
            <td align="left"><input name="mini_site_url_twitter" type="text" class="input" id="mini_site_url_twitter" style="width:250px;" value="<?php echo $dados_stm["mini_site_url_twitter"]; ?>" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_chat']; ?></td>
            <td align="left" class="texto_padrao"><input name="chat" type="radio" value="sim" style="vertical-align:middle" onclick="document.getElementById('xat_id').style.display = 'none';"<?php if($dados_stm["publicserver"] == "always") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;
              <input name="chat" type="radio" value="nao" style="vertical-align:middle" onclick="document.getElementById('xat_id').style.display = 'none';"<?php if($dados_stm["publicserver"] == "never") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<input name="chat" type="radio" value="xat" style="vertical-align:middle" onclick="document.getElementById('xat_id').style.display = 'block';"<?php if($dados_stm["publicserver"] == "never") { echo ' checked="checked"'; } ?> />
              &nbsp;<?php echo $lang['lang_info_mini_site_chat_xat']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_mini_site_chat_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr id="xat_id">
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_url_twitter']; ?></td>
            <td align="left"><input name="mini_site_exibir_xat_id" type="text" class="input" id="mini_site_exibir_xat_id" style="width:250px;" value="<?php echo $dados_stm["mini_site_exibir_xat_id"]; ?>" /></td>
          </tr>
         <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_mini_site_cor_fundo']; ?></td>
            <td align="left"><input type="text" id="mini_site_cor_fundo" name="mini_site_cor_fundo" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_mini_site_cor_topo']; ?></td>
            <td align="left"><input type="text" id="mini_site_cor_topo" name="mini_site_cor_topo" /></td>
         </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_mini_site_cor_texto_topo']; ?></td>
            <td align="left"><input type="text" id="mini_site_cor_texto_topo" name="mini_site_cor_texto_topo" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_cor_texto_padrao']; ?></td>
            <td align="left"><input type="text" id="mini_site_cor_texto_padrao" name="mini_site_cor_texto_padrao" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_mini_site_cor_texto_rodape']; ?></td>
            <td align="left"><input type="text" id="mini_site_cor_texto_rodape" name="mini_site_cor_texto_rodape" /></td>
          </tr>
        </table>
   	  </div>
      </div></td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_alterar_config']; ?>" />
      <input name="alterar" type="hidden" id="alterar" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
      </div>
</form>
</div>
<script>
$("#mini_site_cor_fundo").spectrum({
    chooseText: "OK",
	color: "#FFFFFF"
});
$("#mini_site_cor_topo").spectrum({
    chooseText: "OK",
	color: "#E9E9E9"
});
$("#mini_site_cor_texto_topo").spectrum({
    chooseText: "OK",
	color: "#000000"
});
$("#mini_site_cor_texto_padrao").spectrum({
    chooseText: "OK",
	color: "#000000"
});
$("#mini_site_cor_texto_rodape").spectrum({
    chooseText: "OK",
	color: "#000000"
});
</script>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>