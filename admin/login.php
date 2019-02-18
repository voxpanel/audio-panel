<?php
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "pt") {
$lang_titulo = "Gerenciamento";
$lang_senha = "Senha";
} elseif(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "en") {
$lang_titulo = "Management";
$lang_senha = "Password";
} elseif(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "es") {
$lang_titulo = "Administración";
$lang_senha = "Contraseña";
} else {
$lang_titulo = "Gerenciamento";
$lang_senha = "Senha";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciamento</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#000000; filter:alpha(Opacity=90); -moz-opacity : 0.9; opacity: .9;" onload="document.login.email.focus();">
<div style="width:360px; text-align:center; margin:0 auto;margin-top:15%">
  <div id="quadro">
    <div id="quadro-topo"> <strong><?php echo $lang_titulo; ?></strong></div>
    <div class="texto_medio" id="quadro-conteudo">
      <form method="post" action="/admin/login-autentica" style="margin:0px; padding:0px;" name="login">
        <table width="350" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="195" height="25" class="texto_padrao_destaque">E-mail</td>
            <td width="155" rowspan="8" align="center" class="texto_padrao_destaque"><img src="/admin/img/img-login-admin.png" alt="" width="128" height="128" /></td>
          </tr>
          <tr>
            <td height="25"><input name="email" type="text" id="email" size="25" /></td>
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
</body>
</html>
