<?php
require_once("inc/protecao-admin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
</div>
<div id="menu">
  <div id="menu-links">
    <ul>
      <li style="width:210px">&nbsp;</li>
      <li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
      <li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
      <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
      <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
      <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura&ccedil;&otilde;es</a></li>
      <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
    </ul>
  </div>
</div>
<div id="conteudo">
  <form method="post" action="/admin/admin-cadastra-dica" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Titulo</td>
        <td width="380" align="left"><input name="titulo" type="text" class="input" id="titulo" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Mensagem</td>
        <td align="left"><textarea name="mensagem" id="mensagem" style="width:250px;" rows="5"></textarea></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Exibir</td>
        <td align="left" class="texto_padrao"><input name="exibir" type="radio" value="sim" checked />&nbsp;Sim&nbsp;<input name="exibir" type="radio" value="nao" />&nbsp;Não</td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Cadastrar" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-dicas';" /></td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
