<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Recuperar Senha</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#000000; filter:alpha(Opacity=90); -moz-opacity : 0.9; opacity: .9;">
<div style="width:360px; text-align:center; margin:0 auto;margin-top:15%">
  <div id="quadro">
    <div id="quadro-topo"> <strong>Recuperar Senha</strong></div>
    <div class="texto_medio" id="quadro-conteudo">
      <form method="post" action="/admin/login-recupera-senha" style="margin:0px; padding:0px;">
        <table width="350" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="195" height="25" class="texto_padrao_destaque">Nova Senha
            <input name="passo2" type="hidden" id="passo2" value="passo2" />
            <input name="validacao" type="hidden" id="validacao" value="<?php echo query_string('2'); ?>" /></td>
            <td width="155" rowspan="8" align="center" class="texto_padrao_destaque"><img src="/admin/img/img-login-admin.png" alt="" width="128" height="128" /></td>
          </tr>
          <tr>
            <td height="25"><input name="nova_senha" type="password" id="nova_senha" size="25" /></td>
          </tr>
          <tr>
            <td width="195" height="25" class="texto_padrao_destaque">Nova Senha Confirma&ccedil;&atilde;o</td>
          </tr>
          <tr>
            <td height="25"><input name="nova_senha2" type="password" id="nova_senha2" size="25" /></td>
          </tr>
          
          <tr>
            <td height="35"><input name="submit" type="submit" class="botao" style="width:100px" value="Alterar" /></td>
          </tr>
        </table>
        </form>
    </div>
  </div>
</div>
<br />
</body>
</html>