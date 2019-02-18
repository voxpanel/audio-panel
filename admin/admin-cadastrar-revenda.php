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
  <form method="post" action="/admin/admin-cadastra-revenda" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Nome</td>
        <td width="380" align="left"><input name="nome" type="text" class="input" id="nome" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">ID Único</td>
        <td width="380" align="left"><input name="id" type="text" class="input" id="id" style="width:250px;" value="<?php echo gera_id(); ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">E-mail</td>
        <td align="left"><input name="email" type="text" class="input" id="email" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha</td>
        <td align="left"><input name="senha" type="text" class="input" id="senha" style="width:250px; vertical-align:middle" value="" />
        &nbsp;<img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Gerar Senha" width="16" height="16" align="absmiddle" onclick="gerar_senha('senha');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Plano</td>
        <td align="left">
        <select name="plano" class="input" id="plano" style="width:255px;" onchange="configuracao_plano(this.value,'revenda');">
        <option value="" selected="selected" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione um plano</option>
        <option value="0|50|5000|128|200000" >Revenda Bronze</option>
        <option value="5|999999|10000|128|700000" >Revenda Prata</option>
        <option value="5|999999|15000|128|1000000" >Revenda Ouro</option>
        <option value="10|999999|25000|128|1000000" >Revenda Platina</option>
        <option value="20|999999|999999|128|10000000" >Revenda Ilimitado</option>
        </select>
        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Sub Revendas</td>
        <td align="left"><input name="subrevendas" type="text" class="input" id="subrevendas" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Streamings</td>
        <td align="left"><input name="streamings" type="text" class="input" id="streamings" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Ouvintes</td>
        <td align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Bitrate</td>
        <td align="left">
        <select name="bitrate" class="input" id="bitrate" style="width:255px;">
          <option value="" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione uma op&ccedil;&atilde;o</option>
          <?php
		   foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
		    echo "<option value=\"".$bitrate."\">".$bitrate." Kbps</option>\n";
		   }
		  ?>
         </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Espaço</td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="text" class="input" id="espaco" style="width:250px;" /> 
        (em megabytes)</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Ativar AAC+</td>
        <td align="left" class="texto_padrao">
        <input type="radio" name="aacplus" id="aacplus" value="sim" onclick="configurar_aacplus(this.value);" />&nbsp;Sim
        <input type="radio" name="aacplus" id="aacplus" value="nao" checked="checked" onclick="configurar_aacplus(this.value);" />&nbsp;N&atilde;o
        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Cadastrar" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-revendas';" />        </td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
