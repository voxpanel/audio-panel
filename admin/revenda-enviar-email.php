<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/tinymce/tiny_mce.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="770" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<?php if($dados_revenda["smtp_servidor"] && $dados_revenda["smtp_porta"] && $dados_revenda["smtp_email"] && $dados_revenda["smtp_senha"]) { ?>
<script language='JavaScript' type='text/javascript'>
tinyMCE.init({
  mode : 'exact',
  elements : 'mensagem',
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
<?php if(query_string('2')) { ?>
<?php $dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'")); ?>
<?php if($dados_stm["email"]) { ?>
  <form method="post" action="/admin/revenda-envia-email" style="padding:0px; margin:0px">
    <table width="530" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_enviar_email_para; ?></td>
        <td width="410" align="left" class="texto_padrao">
<?php echo $dados_stm["porta"]; ?> - <?php echo $dados_stm["email"]; ?>
<input type="hidden" name="porta" id="porta" value="<?php echo query_string('2'); ?>" />
<input type="hidden" name="email" id="email" value="<?php echo $dados_stm["email"]; ?>" />

        </td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_enviar_email_assunto; ?></td>
        <td width="410" align="left"><input name="assunto" type="text" class="input" id="assunto" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left"><textarea id="mensagem" name="mensagem" rows="20" style="width:100%"></textarea></td>
      </tr>
      <tr>
        <td height="40" colspan="2" align="center">
        <input type="submit" class="botao" value="<?php echo lang_info_pagina_enviar_email_botao_enviar; ?>" /></td>
      </tr>
    </table>
  </form>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro_pequeno" scope="col"><?php echo lang_info_pagina_enviar_email_info_sem_email; ?></td>
    </tr>
</table>
<?php } ?>
<?php } else { ?>
<form method="post" action="/admin/revenda-envia-email" style="padding:0px; margin:0px">
    <table width="530" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_enviar_email_para; ?><br />
          <span class="texto_padrao_pequeno"><?php echo lang_info_pagina_enviar_email_para_info; ?></span></td>
        <td width="410" align="left" class="texto_padrao">
<select name="portas[]" multiple style="width:255px; height:100px;">

<?php
$sql = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."' AND email != '' ORDER by porta ASC");
while ($dados_stm = mysql_fetch_array($sql)) {
?>	

<option value="<?php echo $dados_stm["email"]; ?>"><?php echo $dados_stm["porta"]; ?> - <?php echo $dados_stm["email"]; ?></option>

<?php } ?>	

</select>
        </td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_enviar_email_assunto; ?></td>
        <td width="410" align="left"><input name="assunto" type="text" class="input" id="assunto" style="width:250px;" /></td>
      </tr>
      <tr>
        <td height="30" colspan="2" align="left"><textarea id="mensagem" name="mensagem" rows="20" style="width:100%"></textarea></td>
      </tr>
      <tr>
        <td height="40" colspan="2" align="center">
        <input type="submit" class="botao" value="<?php echo lang_info_pagina_enviar_email_botao_enviar; ?>" /></td>
      </tr>
    </table>
  </form>
<?php } ?>  
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro_pequeno" scope="col"><?php echo lang_info_pagina_enviar_email_info_sem_smtp; ?></td>
    </tr>
</table>
<?php } ?>
</div>
</body>
</html>