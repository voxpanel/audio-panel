<?php
require_once("inc/protecao-admin.php");

$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".code_decode(query_string('2'),"D")."'"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));

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
  <form method="post" name="app" action="/admin/admin-app-edita" style="padding:0px; margin:0px" enctype="multipart/form-data">
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" scope="col" style="padding-left:5px;" class="texto_padrao"><strong>Arquivo:</strong> /src/com/shoutcast/stm/<?php echo nome_app_play($dados_app["radio_nome"]);?>/data/information.java 
          <span class="texto_padrao_destaque" style="padding-left:5px;">
          <input name="codigo" type="hidden" id="codigo" value="<?php echo $dados_app["codigo"]; ?>" />
        </span></td>
      </tr>
      <tr>
        <td>
          <textarea name="information" id="information" style="width:800px; height:300px"><?php echo file_get_contents("../app_android/apps/".$dados_app["hash"]."/src/com/shoutcast/stm/".nome_app_play($dados_app["radio_nome"])."/data/information.java"); ?></textarea>
        </td>
      </tr>
      <tr>
        <td height="40" align="center">
          <input type="submit" class="botao" value="Editar" />
        </td>
      </tr>
    </table>
  </form>
  <br />
<form method="post" name="app" action="/admin/admin-app-edita" style="padding:0px; margin:0px" enctype="multipart/form-data">
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" scope="col" style="padding-left:5px;" class="texto_padrao"><strong>Arquivo:</strong> /res/values/strings.xml<span class="texto_padrao_destaque" style="padding-left:5px;">
          <input name="codigo" type="hidden" id="codigo" value="<?php echo $dados_app["codigo"]; ?>" />
        </span></td>
      </tr>
      <tr>
        <td>
          <textarea name="strings" id="strings" style="width:800px; height:300px"><?php echo file_get_contents("../app_android/apps/".$dados_app["hash"]."/res/values/strings.xml"); ?></textarea>
        </td>
      </tr>
      <tr>
        <td height="40" align="center">
          <input type="submit" class="botao" value="Editar" />
        </td>
      </tr>
    </table>
  </form>
  <br />
<form method="post" name="app" action="/admin/admin-app-edita" style="padding:0px; margin:0px" enctype="multipart/form-data">
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" scope="col" style="padding-left:5px;" class="texto_padrao"><strong>Arquivo:</strong> /AndroidManifest.xml<span class="texto_padrao_destaque" style="padding-left:5px;">
          <input name="codigo" type="hidden" id="codigo" value="<?php echo $dados_app["codigo"]; ?>" />
        </span></td>
      </tr>
      <tr>
        <td>
          <textarea name="manifest" id="manifest" style="width:800px; height:300px"><?php echo file_get_contents("../app_android/apps/".$dados_app["hash"]."/AndroidManifest.xml"); ?></textarea>
        </td>
      </tr>
      <tr>
        <td height="40" align="center">
          <input type="submit" class="botao" value="Editar" />
        </td>
      </tr>
    </table>
  </form>
</div>
</body>
</html>
