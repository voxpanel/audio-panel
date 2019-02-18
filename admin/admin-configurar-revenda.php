<?php
require_once("inc/protecao-admin.php");

$revenda_code = code_decode(query_string('2'),"D");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$revenda_code."'"));

// Estatísticas
$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_streamings = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

$stat_subrevendas_descricao = "".$total_subrevendas." / ".$dados_revenda["subrevendas"]."";
$stat_streamings_descricao = "".$total_streamings." / ".$dados_revenda["streamings"]."";
$stat_ouvintes_descricao = "".$ouvintes["total"]." / ".$dados_revenda["ouvintes"]."";
$stat_espaco_descricao = "".tamanho(($espaco_subrevendas["total"]+$espaco_streamings["total"]))." / ".tamanho($dados_revenda["espaco"])."";

$limite_subrevendas = ($dados_revenda["subrevendas"] == 999999) ? ''.$total_subrevendas.' / <span class="texto_ilimitado">ILIMITADO</span>' : $stat_subrevendas_descricao;

$limite_streamings = ($dados_revenda["streamings"] == 999999) ? ''.$total_streamings.' / <span class="texto_ilimitado">ILIMITADO</span>' : $stat_streamings_descricao;

$limite_ouvintes = ($dados_revenda["ouvintes"] == 999999) ? ''.$ouvintes["total"].' / <span class="texto_ilimitado">ILIMITADO</span>' : $stat_ouvintes_descricao;

$limite_espaco_ftp = $stat_espaco_descricao;

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
  <form method="post" action="/admin/admin-configura-revenda" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Código</td>
        <td width="380" align="left"><input name="id" type="text" class="input" id="id" style="width:250px;" value="<?php echo $dados_revenda["id"]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Nome</td>
        <td align="left"><input name="nome" type="text" class="input" id="nome" style="width:250px;" value="<?php echo $dados_revenda["nome"]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">E-mail</td>
        <td align="left"><input name="email" type="text" class="input" id="email" style="width:250px;" value="<?php echo $dados_revenda["email"]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha</td>
        <td align="left"><input name="senha" type="text" class="input" id="senha" style="width:250px;" />&nbsp;<img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Gerar Senha" width="16" height="16" align="absmiddle" onclick="gerar_senha('senha');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Domínio Servidores</td>
        <td align="left"><input name="dominio_padrao" type="text" class="input" id="dominio_padrao" style="width:250px;" value="<?php echo $dados_revenda["dominio_padrao"]; ?>" /></td>
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
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">M&aacute;ximo Sub Revendas</td>
        <td align="left"><input name="subrevendas" type="text" class="input" id="subrevendas" style="width:250px;" value="<?php echo $dados_revenda[subrevendas]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">M&aacute;ximo Streamings</td>
        <td align="left"><input name="streamings" type="text" class="input" id="streamings" style="width:250px;" value="<?php echo $dados_revenda[streamings]; ?>" /></td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">M&aacute;ximo Ouvintes</td>
        <td width="380" align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" value="<?php echo $dados_revenda[ouvintes]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Bitrate</td>
        <td align="left"><select name="bitrate" class="input" id="bitrate" style="width:255px;">
           <?php
		   foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
		   
		   if($dados_revenda[bitrate] == $bitrate) {
		    echo "<option value=\"".$bitrate."\" selected=\"selected\">".$bitrate." Kbps</option>";
		   } else {
		    echo "<option value=\"".$bitrate."\">".$bitrate." Kbps</option>";
		   }
		   
		   }
		  ?>
          </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Espa&ccedil;o</td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="text" class="input" id="espaco" style="width:250px;" value="<?php echo $dados_revenda[espaco]; ?>" />
          (em megabytes)</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Ativar AAC+</td>
        <td align="left" class="texto_padrao">
          <input type="radio" name="aacplus" id="aacplus" value="sim"<?php if($dados_revenda["aacplus"] == "sim") { echo ' checked="checked"'; } ?> onclick="configurar_aacplus(this.value);" />&nbsp;Sim
          <input type="radio" name="aacplus" id="aacplus" value="nao"<?php if($dados_revenda["aacplus"] == "nao") { echo ' checked="checked"'; } ?> onclick="configurar_aacplus(this.value);" />&nbsp;Não
          </td>
      </tr>
      <tr>
        <td height="40">
        <input name="codigo_revenda" type="hidden" id="codigo_revenda" value="<?php echo $dados_revenda[codigo]; ?>" />
        </td>
        <td align="left">
          <input type="submit" class="botao" value="Alterar Dados" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-revendas';" /></td>
      </tr>
    </table>
    <br />
    <table width="340" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid; margin-top:10px; margin-bottom:5px;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque">Estat&iacute;stica de Uso do Plano</td>
      </tr>
    </table>
    <table width="340" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="100" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Sub Revendas</td>
        <td width="240" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo "".$limite_subrevendas.""; ?></td>
      </tr>
      <tr>
        <td width="100" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Streamings</td>
        <td width="240" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo "".$limite_streamings.""; ?></td>
      </tr>
      <tr>
        <td height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Ouvintes</td>
        <td align="left" class="texto_padrao" style="padding-left:5px;"><?php echo "".$limite_ouvintes.""; ?></td>
      </tr>
      <tr>
        <td height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Espa&ccedil;o AutoDJ</td>
        <td align="left" class="texto_padrao" style="padding-left:5px;"><?php echo "".$limite_espaco_ftp.""; ?></td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
