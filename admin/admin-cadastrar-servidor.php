<?php
require_once("inc/protecao-admin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
</div>
<div id="menu">
<div id="menu-links">
  	<ul>
      <li style="width:150px">&nbsp;</li>
  		<li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
  		<li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
        <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
        <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
        <li><em></em><a href="/admin/admin-avisos" class="texto_menu">Avisos</a></li>
        <li><em></em><a href="/admin/admin-tutoriais" class="texto_menu">Tutoriais</a></li>
        <li><em></em><a href="/admin/admin-apps" class="texto_menu">Apps</a></li>
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configurações</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
  <form method="post" action="/admin/admin-cadastra-servidor" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Nome</td>
        <td width="380" align="left"><input name="nome" type="text" class="input" id="nome" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">IP</td>
        <td align="left"><input name="ip" type="text" class="input" id="ip" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha Root</td>
        <td align="left"><input name="senha" type="password" class="input" id="senha" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Porta SSH</td>
        <td align="left"><input name="porta_ssh" type="text" class="input" id="porta_ssh" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Porta Programete</td>
        <td align="left"><input name="portapro" type="text" class="input" id="porta_ssh" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Limite Streamings</td>
        <td align="left"><input name="limite_streamings" type="text" class="input" id="limite_streamings" style="width:250px;" value="200" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Tipo</td>
        <td align="left" class="texto_padrao">
          <input name="tipo" type="radio" id="tipo" value="streaming" checked="checked" />
          &nbsp;Streaming
          <input type="radio" name="tipo" id="tipo" value="aacplus" />
          &nbsp;RTMP/AAC+
        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Gráfico Tráfego</td>
        <td align="left"><input name="grafico_trafego" type="text" class="input" id="grafico_trafego" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Cadastrar" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-servidores';" />        </td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
