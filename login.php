<?php
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "pt") {
$lang_titulo = "Gerenciamento";
$lang_login = "Porta";
$lang_senha = "Senha";
$lang_versao_movel = "Versão para Celular";
} elseif(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "en") {
$lang_titulo = "Management";
$lang_login = "Port";
$lang_senha = "Password";
$lang_versao_movel = "Mobile Version";
} elseif(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "es") {
$lang_titulo = "Administración";
$lang_login = "Puerto";
$lang_senha = "Contraseña";
$lang_versao_movel = "Versión Móvil";
} else {
$lang_titulo = "Gerenciamento";
$lang_login = "Porta";
$lang_senha = "Senha";
$lang_versao_movel = "Versão para Celular";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciamento de Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#000000; filter:alpha(Opacity=90); -moz-opacity : 0.9; opacity: .9;" onload="document.login.porta.focus();">
<div style="width:360px; text-align:center; margin:0 auto;margin-top:15%">
<div id="quadro">
       	  <div id="quadro-topo"> <strong><?php echo $lang_titulo; ?></strong></div>
	  <div class="texto_medio" id="quadro-conteudo">
      <form method="post" action="/login-autentica" style="margin:0px; padding:0px;" name="login">
	    <table width="350" border="0" cellpadding="0" cellspacing="0">

          <tr>
            <td width="195" height="25" class="texto_padrao_destaque"><?php echo $lang_login; ?></td>
            <td width="155" rowspan="8" align="center" class="texto_padrao_destaque"><img src="img/img-login-streaming.png" width="128" height="128" /></td>
          </tr>
          <tr>
            <td height="25"><input name="porta" type="text" id="porta" size="25" /></td>
          </tr>
          <tr>
            <td height="25" class="texto_padrao_destaque"><?php echo $lang_senha; ?></td>
          </tr>
          <tr>
            <td height="25"><input name="senha" type="password" id="senha" size="25" /></td>
          </tr>
          <tr>
            <td height="35"><input name="submit" type="submit" class="botao" style="width:100px" value="OK" /></td>
          </tr>
        </table>
        <?php echo $_SESSION["status_login"]; unset($_SESSION["status_login"]); ?>
        </form>
	  </div>
    </div>
    </div>
	<br />
<div style="width:360px; text-align:right; margin:0 auto;">
    <img src="img/icones/img-icone-celular-64x64.png" width="24" height="24" align="absmiddle" title="<?php echo $lang_versao_movel; ?>r" />&nbsp;<a href="/movel" class="texto_padrao_pequeno_branco"><?php echo $lang_versao_movel; ?></a>&nbsp;&nbsp;&nbsp;<img src="img/icones/img-icone-dj-64x64.png" width="24" height="24" align="absmiddle" title="Acessar Painel do DJ" />&nbsp;<a href="/dj" class="texto_padrao_pequeno_branco">Painel DJ</a>
</div>
</body>
</html>