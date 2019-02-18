<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="inc/estilo-movel.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#000000; filter:alpha(Opacity=90); -moz-opacity : 0.9; opacity: .9;">
<div style="width:280px; text-align:center; margin:0 auto;margin-top:150px;margin-bottom:150px;">
  <div id="quadro">
    <div id="quadro-topo"> <strong>Gerenciamento de Streaming</strong></div>
    <div class="texto_medio" id="quadro-conteudo">
      <form method="post" action="/movel/login-autentica" style="margin:0px; padding:0px;">
        <table width="270" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="130" height="25" class="texto_padrao_destaque">Porta</td>
            <td width="140" rowspan="8" align="center" class="texto_padrao_destaque"><img src="img/img-login-streaming.png" width="128" height="128" /></td>
          </tr>
          <tr>
            <td height="25"><input name="porta" type="number" id="porta" style="width:100px" /></td>
          </tr>
          <tr>
            <td height="25" class="texto_padrao_destaque">Senha</td>
          </tr>
          <tr>
            <td height="25"><input name="senha" type="password" id="senha" style="width:100px" /></td>
          </tr>
          <tr>
            <td height="35"><input name="submit" type="submit" class="botao" style="width:100px" value="Acessar" /></td>
          </tr>
        </table>
        <?php echo $_SESSION["status_login"]; unset($_SESSION["status_login"]); ?>
      </form>
    </div>
  </div>
  <br />
<div style="width:280px; text-align:right; margin:0 auto;">
    <img src="img/icones/img-icone-pc-64x64.png" width="24" height="24" align="absmiddle" title="Acessar Versão Normal do Painel" />&nbsp;<a href="/" class="texto_padrao_pequeno_branco">Versão Normal</a>&nbsp;&nbsp;&nbsp;<img src="img/icones/img-icone-dj-64x64.png" width="24" height="24" align="absmiddle" title="Acessar Painel do DJ" />&nbsp;<a href="/dj" class="texto_padrao_pequeno_branco">Painel do DJ</a>
</div>
</div>	
</body>
</html>
