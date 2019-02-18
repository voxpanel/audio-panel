<?php
require_once("inc/protecao-admin.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".code_decode(query_string('2'),"D")."'"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$shoutcast = "http://".strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"].":".$dados_stm["porta"]."/";

$modelo = str_replace("source","Modelo ",$dados_app["source"]);

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

<body onload="document.app.radio_nome.focus();">
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
      
      <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura&ccedil;&otilde;es</a></li>
      
      <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
    
    </ul>
  </div>
</div>
<div id="conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="770" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
  <form method="post" name="app" action="/admin/admin-app-finaliza" style="padding:0px; margin:0px" enctype="multipart/form-data">
    <table width="630" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
    <tr>
        <td width="230" rowspan="11" align="center">
        <img src="/<?php echo $dados_app["print"];?>" />
        <?php if($dados_app["status"] == "1" && $dados_app["play"] == "sim") { ?>
        <br /><br />
        <img src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=market://details?id=<?php echo $dados_app["package"]; ?>" width="150" height="150" />
        <?php } ?>        </td>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td width="110" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Porta
        <input name="codigo" type="hidden" id="codigo" value="<?php echo $dados_app["codigo"]; ?>" /></td>
        <td width="300" align="left" class="texto_padrao"><?php echo $dados_stm["porta"]; ?></td>
    </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Revenda</td>
        <td width="300" align="left" class="texto_padrao"><?php echo $dados_revenda["nome"]; ?> - <?php echo $dados_revenda["id"]; ?></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Nome </td>
        <td width="300" align="left"><input onclick="this.select()" onfocus="this.select()" style="width:250px;" value="<?php echo $dados_app["radio_nome"]; ?>" name="radio_nome" /></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td width="110" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Site</td>
        <td width="300" align="left"><input onclick="this.select()" style="width:250px;" value="<?php echo $dados_app["radio_site"]; ?>" /></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td width="110" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Facebook</td>
        <td width="300" align="left"><input onclick="this.select()" style="width:250px;" value="<?php echo $dados_app["radio_facebook"]; ?>" /></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Twitter</td>
        <td align="left"><input onclick="this.select()" style="width:250px;" value="<?php echo $dados_app["radio_twitter"]; ?>" /></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Package</td>
        <td align="left"><input name="package" id="package" style="width:250px;" onclick="this.select()" value="<?php echo $dados_app["package"]; ?>" /></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Shoutcast</td>
        <td align="left"><input onclick="this.select()" style="width:250px;" value="<?php echo $shoutcast; ?>" /></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Modelo</td>
        <td align="left" class="texto_padrao"><?php echo $modelo; ?></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Aviso</td>
        <td align="left" class="texto_padrao"><input name="aviso" id="aviso" style="width:250px;" value="" onkeyup="contar_caracteres(this.id,'70');" />
  &nbsp;<span id="total_caracteres" class="texto_padrao_pequeno">70</span></td>
      </tr>
      <tr>
        <td width="10" align="left" bgcolor="#FFFFFF" class="texto_padrao">&nbsp;</td>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Status</td>
        <td align="left" class="texto_padrao"><input name="status" type="radio" value="1" checked="checked" />
          &nbsp;OK&nbsp;
          <input name="status" type="radio" value="2" />
          &nbsp;Erro</td>
      </tr>
      <tr>
        <td height="45" align="center">&nbsp;</td>
        <td height="45" align="center">&nbsp;</td>
        <td height="45" colspan="2" align="center"><input type="submit" class="botao" value="Concluir" />
          <input type="button" class="botao" value="Editor" onclick="window.location = '/admin/admin-app-editar/<?php echo query_string('2'); ?>';" />
          <input type="button" class="botao" value="Compilar App" onclick="window.location = '/admin/admin-app-compilar/<?php echo query_string('2'); ?>';" />
          <?php if($dados_app["compilado"] == "sim") { ?>
          <input type="button" class="botao" value="Download" onclick="window.location = '/app_android/apps/<?php echo $dados_app["zip"]; ?>';" />
          <?php } ?>
          <input type="button" class="botao" value="Voltar" onclick="window.location = '/admin/admin-apps';" /></td>
      </tr>
    </table>
  </form>
  <br />
    <?php if($dados_app["log_build"]) { ?>
    <center><textarea rows="15" style="width:630px"><?php echo $dados_app["log_build"]; ?></textarea></center>
    <?php } ?>
	<br />
	<br />
</div>
</body>
</html>
