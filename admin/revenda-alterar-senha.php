<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastrar Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
</head>

<body>
<div id="sub-conteudo">
  <form method="post" action="/admin/revenda-altera-senha" style="padding:0px; margin:0px">
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
      <tr>
        <td width="30" height="30" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro_pequeno" scope="col">Aten&ccedil;&atilde;o! Para sua seguran&ccedil;a voc&ecirc; deve alterar sua senha para uma nova mais segura.</td>
      </tr>
    </table>
    <br />
    <table width="530" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"> Nova Senha</td>
        <td width="410" align="left"><input name="senha1" type="password" class="input" id="senha1" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Digite Novamente</td>
        <td align="left" class="texto_padrao_pequeno"><input name="senha2" type="password" class="input" id="senha2" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Alterar Senha" /></td>
      </tr>
    </table>
  </form>
</div>
</body>
</html>
