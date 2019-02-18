<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Recuperar Senha</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="inc/estilo.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#000000; filter:alpha(Opacity=90); -moz-opacity : 0.9; opacity: .9;">
<div style="width:360px; text-align:center; margin:0 auto;margin-top:15%">
  <div id="quadro">
    <div id="quadro-topo"> <strong>Recuperar Senha</strong></div>
    <div class="texto_medio" id="quadro-conteudo">
      <form method="post" action="/admin/login-recupera-senha" style="margin:0px; padding:0px;">
        <table width="350" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="195" height="25" class="texto_padrao_destaque">E-mail
            <input name="passo1" type="hidden" id="passo1" value="passo1" /></td>
            <td width="155" rowspan="7" align="center" class="texto_padrao_destaque"><img src="img/img-login-admin.png" alt="" width="128" height="128" /></td>
          </tr>
          <tr>
            <td height="25"><input name="email" type="text" id="email" size="25" /></td>
          </tr>
          <tr>
            <td height="40" class="texto_padrao_pequeno">A senha ser&aacute; enviada para seu e-mail.</td>
          </tr>
          
          <tr>
            <td height="35"><input name="submit" type="submit" class="botao" style="width:100px" value="Recuperar" /></td>
          </tr>
        </table>
        <?php echo $_SESSION["status_login"]; unset($_SESSION["status_login"]); ?>
      </form>
    </div>
  </div>
</div>
<br />
</body>
</html>