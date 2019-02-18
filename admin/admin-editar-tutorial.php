<?php
require_once("inc/protecao-admin.php");

$tutorial_code = code_decode(query_string('2'),"D");

$dados_tutorial = mysql_fetch_array(mysql_query("SELECT * FROM tutoriais where codigo = '".$tutorial_code."'"));

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
<script type="text/javascript" src="/admin/inc/tinymce/tiny_mce.js"></script>
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
      <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura&ccedil;&otilde;es</a></li>
      <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
    </ul>
  </div>
</div>
<div id="conteudo">
<script language='JavaScript' type='text/javascript'>
tinyMCE.init({
  mode : 'exact',
  elements : 'tutorial',
  theme : "advanced",
  skin : "o2k7",
  skin_variant : "silver",
  plugins : "table,inlinepopups,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking",
  dialog_type : 'modal',
  force_br_newlines : true,
  force_p_newlines : false,
  theme_advanced_toolbar_location : 'top',
  theme_advanced_toolbar_align : 'left',
  theme_advanced_path_location : 'bottom',
  theme_advanced_buttons1 : 'newdocument,|,bold,italic,underline,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,undo,redo,|,link,unlink,image,media,|,code',
  theme_advanced_buttons2 : '',
  theme_advanced_buttons3 : '',
  theme_advanced_resize_horizontal : false,
  theme_advanced_resizing : false,
  valid_elements : "*[*]"
});
</script>

  <form method="post" action="/admin/admin-edita-tutorial" style="padding:0px; margin:0px">
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="105" height="35" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Titulo<input name="codigo" type="hidden" value="<?php echo $dados_tutorial["codigo"]; ?>" /></td>
        <td width="593" align="left"><input name="titulo" type="text" class="input" id="titulo" style="width:570px;" value="<?php echo $dados_tutorial["titulo"]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" colspan="2"><textarea id="tutorial" name="tutorial" rows="30" style="width:100%"><?php echo $dados_tutorial["tutorial"]; ?></textarea></td>
      </tr>
      <tr>
        <td height="40" colspan="2" align="center">
          <input type="submit" class="botao" value="Editar" />
        <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-tutoriais';" /></td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
