<?php
require_once("inc/protecao-revenda.php");

$dados_revenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

if($_POST["alterar_dados"]) {

$dominio_padrao = str_replace("http://","",$_POST["dominio_padrao"]);
$dominio_padrao = str_replace("www.","",$dominio_padrao);

if($_POST["stm_exibir_tutoriais"] == "url" && filter_var($_POST["url_tutoriais"], FILTER_VALIDATE_URL) === false) {
die ("<script> alert(\"URL Inválida\\n\\nInvalid URL\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_POST["url_downloads"] != "") {
if(filter_var($_POST["url_downloads"], FILTER_VALIDATE_URL) === false) {

die ("<script> alert(\"URL Download Inválida\\n\\nInvalid Download URL\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}
}

if($_POST["senha"]) {

mysql_query("Update revendas set senha = PASSWORD('".$_POST["senha"]."'), url_suporte = '".$_POST["url_suporte"]."', dominio_padrao = '".$dominio_padrao."', smtp_servidor = '".$_POST["smtp_servidor"]."', smtp_porta = '".$_POST["smtp_porta"]."', smtp_email = '".$_POST["smtp_email"]."', smtp_senha = '".$_POST["smtp_senha"]."', stm_exibir_tutoriais = '".$_POST["stm_exibir_tutoriais"]."', url_tutoriais = '".$_POST["url_tutoriais"]."', url_downloads = '".$_POST["url_downloads"]."', stm_exibir_app_android = '".$_POST["stm_exibir_app_android"]."', stm_exibir_mini_site = '".$_POST["stm_exibir_mini_site"]."', stm_exibir_app_android_painel = '".$_POST["stm_exibir_app_android_painel"]."', idioma_painel = '".$_POST["idioma_painel"]."' where codigo = '".$_SESSION["code_user_logged"]."'") or die(mysql_error());

} else {

mysql_query("Update revendas set url_suporte = '".$_POST["url_suporte"]."', dominio_padrao = '".$dominio_padrao."', smtp_servidor = '".$_POST["smtp_servidor"]."', smtp_porta = '".$_POST["smtp_porta"]."', smtp_email = '".$_POST["smtp_email"]."', smtp_senha = '".$_POST["smtp_senha"]."', stm_exibir_tutoriais = '".$_POST["stm_exibir_tutoriais"]."', url_tutoriais = '".$_POST["url_tutoriais"]."', url_downloads = '".$_POST["url_downloads"]."', stm_exibir_app_android = '".$_POST["stm_exibir_app_android"]."', stm_exibir_mini_site = '".$_POST["stm_exibir_mini_site"]."', stm_exibir_app_android_painel = '".$_POST["stm_exibir_app_android_painel"]."', idioma_painel = '".$_POST["idioma_painel"]."' where codigo = '".$_SESSION["code_user_logged"]."'") or die(mysql_error());

}

// Verifica se o app android foi ativado/desativado
if($dados_revenda_atual["stm_exibir_app_android"] != $_POST["stm_exibir_app_android"]) {
mysql_query("Update streamings set exibir_app_android ='".$_POST["stm_exibir_app_android"]."' where codigo_cliente = '".$_SESSION["code_user_logged"]."'");
}

// Verifica se o mini site foi ativado/desativado
if($dados_revenda_atual["stm_exibir_mini_site"] != $_POST["stm_exibir_mini_site"]) {
mysql_query("Update streamings set stm_exibir_mini_site ='".$_POST["stm_exibir_mini_site"]."' where codigo_cliente = '".$_SESSION["code_user_logged"]."'");
}

// Insere a ação executada no registro de logs.
logar_acao("revenda","".$dados_revenda_atual["codigo"]."","Configurações da revenda alteradas com sucesso.");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao(lang_info_pagina_config_revenda_resultado_ok,"ok");

header("Location: /admin/revenda-configuracoes");
exit();
}

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form method="post" action="/admin/revenda-configuracoes" style="padding:0px; margin:0px">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo lang_info_pagina_config_revenda_tab_titulo; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo lang_info_pagina_config_revenda_tab_config_geral; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
                <tr>
                  <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_config_geral_senha; ?></td>
                  <td width="740" align="left" class="texto_padrao_pequeno"><input name="senha" type="password" class="input" id="senha" style="width:250px;" value="" />
                  <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_config_geral_senha_ajuda; ?>');" style="cursor:pointer" /></td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_config_geral_idioma; ?></td>
                  <td align="left">
                  <select name="idioma_painel" class="input" id="idioma_painel" style="width:255px;">
          			<option value="pt-br" <?php if($dados_revenda["idioma_painel"] == "pt-br") { echo 'selected="selected"';} ?>>Português(Brasil)</option>
		  			<option value="es" <?php if($dados_revenda["idioma_painel"] == "es") { echo 'selected="selected"';} ?>>Español</option>
          			<option value="en-us" <?php if($dados_revenda["idioma_painel"] == "en-us") { echo 'selected="selected"';} ?>>English(USA)</option>		  
         		  </select>
                  <span class="texto_padrao_pequeno"><img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_config_geral_idioma_ajuda; ?>');" style="cursor:pointer" /></span></td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_config_geral_url_suporte; ?></td>
                  <td align="left" class="texto_padrao_pequeno"><input name="url_suporte" type="text" class="input" id="url_suporte" style="width:250px;" value="<?php echo $dados_revenda["url_suporte"]; ?>" />
                  <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_config_geral_url_suporte_ajuda; ?>');" style="cursor:pointer" /></td>
                </tr>
              </table>
   	  </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo lang_info_pagina_config_revenda_tab_recursos; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
                <tr>
                  <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_recursos_tutoriais; ?></td>
                  <td width="740" align="left" class="texto_padrao_pequeno">
                  <input name="stm_exibir_tutoriais" type="radio" value="sim" onclick="document.getElementById('div_tutoriais_url').style.display = 'none';" <?php if($dados_revenda["stm_exibir_tutoriais"] == "sim") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_sim; ?>
                  <input name="stm_exibir_tutoriais" type="radio" value="nao" onclick="document.getElementById('div_tutoriais_url').style.display = 'none';" <?php if($dados_revenda["stm_exibir_tutoriais"] == "nao") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_nao; ?>
                  <input name="stm_exibir_tutoriais" type="radio" value="url" onclick="document.getElementById('div_tutoriais_url').style.display = 'block';" <?php if($dados_revenda["stm_exibir_tutoriais"] == "url") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_info_pagina_config_revenda_recursos_tutoriais_url; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_recursos_tutoriais_ajuda; ?>');" style="cursor:pointer" />
                      <span id="div_tutoriais_url" style="display:<?php if($dados_revenda["stm_exibir_tutoriais"] == "url") { echo "block"; } else { echo "none"; } ?>"><br /><input name="url_tutoriais" type="text" class="input" id="url_tutoriais" style="width:250px;" value="<?php echo $dados_revenda["url_tutoriais"]; ?>" /></span>                      </td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_recursos_downloads_url; ?></td>
                  <td align="left" class="texto_padrao_pequeno"><input name="url_downloads" type="text" class="input" id="url_downloads" style="width:250px;" value="<?php echo $dados_revenda["url_downloads"]; ?>" />
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_recursos_downloads_url_info; ?>');" style="cursor:pointer" /></td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_recursos_app_android; ?></td>
                  <td align="left" class="texto_padrao_pequeno">
                  <input name="stm_exibir_app_android" type="radio" value="sim" <?php if($dados_revenda["stm_exibir_app_android"] == "sim") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_sim; ?>
                  <input name="stm_exibir_app_android" type="radio" value="nao" <?php if($dados_revenda["stm_exibir_app_android"] == "nao") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_nao; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_recursos_app_android_info; ?>');" style="cursor:pointer" /></td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_recursos_app_android_painel; ?></td>
                  <td align="left" class="texto_padrao_pequeno">
                  <input name="stm_exibir_app_android_painel" type="radio" value="sim" <?php if($dados_revenda["stm_exibir_app_android_painel"] == "sim") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_sim; ?>
                  <input name="stm_exibir_app_android_painel" type="radio" value="nao" <?php if($dados_revenda["stm_exibir_app_android_painel"] == "nao") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_nao; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_recursos_app_android_painel_info; ?>');" style="cursor:pointer" /></td>
                </tr>
                <tr>
                  <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_recursos_mini_site; ?></td>
                  <td width="740" align="left" class="texto_padrao_pequeno">
                  <input name="stm_exibir_mini_site" type="radio" value="sim" <?php if($dados_revenda["stm_exibir_mini_site"] == "sim") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_sim; ?>
                  <input name="stm_exibir_mini_site" type="radio" value="nao" <?php if($dados_revenda["stm_exibir_mini_site"] == "nao") { echo 'checked="checked"';} ?> />&nbsp;<?php echo lang_opcao_nao; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_recursos_mini_site_ajuda; ?>');" style="cursor:pointer" /></td>
                </tr>
              </table>
      </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo lang_info_pagina_config_revenda_tab_config_dominio; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
       			 <tr>
                  <td height="30" colspan="2" align="left" style="padding:5px;">
                  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color: #C1E0FF; border: #006699 1px solid">
                <tr>
                  <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
                  <td width="855" align="left" class="texto_padrao_pequeno" scope="col"><?php echo sprintf(lang_info_pagina_config_revenda_config_dominio_info,$dados_config["dominio_padrao"]); ?></td>
                </tr>
              </table>
              </td>
                  </tr>
                <tr>
                  <td width="150" height="50" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_config_revenda_config_dominio_dominio; ?></td>
                <td width="740" align="left" class="texto_padrao_pequeno"><input name="dominio_padrao" type="text" class="input" id="dominio_padrao" style="width:250px;" value="<?php echo $dados_revenda["dominio_padrao"]; ?>" />
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_config_revenda_config_dominio_ajuda; ?>');" style="cursor:pointer" /><br />
                    <a href="#" onclick="abrir_log_sistema();window.open('/admin/revenda-dominio','conteudo');"><?php echo lang_info_pagina_config_revenda_config_dominio_link; ?></a></td>
                </tr>
              </table>
      </div>
    </div>
    </td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo lang_botao_titulo_alterar_config; ?>" />
      <input name="alterar_dados" type="hidden" id="alterar_dados" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
      </div>
</form>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</div>
</body>
</html>
