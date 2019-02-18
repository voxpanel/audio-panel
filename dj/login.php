<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="../admin/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#000000; filter:alpha(Opacity=90); -moz-opacity : 0.9; opacity: .9;">
<div style="width:360px; text-align:center; margin:0 auto;margin-top:15%">
<div id="quadro">
       	  <div id="quadro-topo"> <strong>Painel de Controle do DJ</strong></div>
	  <div class="texto_medio" id="quadro-conteudo">
      <form method="post" action="/dj/login-autentica" style="margin:0px; padding:0px;">
	    <table width="350" border="0" cellpadding="0" cellspacing="0">

          <tr>
            <td width="195" height="25" class="texto_padrao_destaque">Porta Streaming</td>
            <td width="155" rowspan="5" align="center" class="texto_padrao_destaque"><img src="img/img-login-dj.png" width="128" height="128" /></td>
          </tr>
          <tr>
            <td height="25"><input name="porta" type="number" id="porta" size="25" /></td>
          </tr>
          <tr>
            <td height="25" class="texto_padrao_destaque">Login DJ</td>
          </tr>
          <tr>
            <td height="25"><input name="login_dj" type="text" id="login_dj" size="25" /></td>
          </tr>
          <tr>
            <td height="25" class="texto_padrao_destaque">Senha DJ</td>
          </tr>
          <tr>
            <td height="25"><input name="senha_dj" type="password" id="senha_dj" size="25" /></td>
          </tr>
          <tr>
            <td height="35"><input name="submit" type="submit" class="botao" style="width:100px" value="Acessar" /></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><?php echo $_SESSION[status_login]; unset($_SESSION[status_login]); ?></td>
          </tr>
        </table>
        </form>
	  </div>
    </div>
</div>
</body>
</html>