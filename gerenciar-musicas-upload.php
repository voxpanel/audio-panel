<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 300);

require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<title>Streaming <?php echo $_SESSION["porta_logada"]; ?> - Upload de Músicas</title>
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming-upload.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
   window.onunload = function() {
    	window.opener.carregar_musicas_pasta( '<?php echo code_decode($_SESSION["porta_logada"],"E"); ?>','<?php echo query_string('1'); ?>');
		window.opener.carregar_pastas( '<?php echo code_decode($_SESSION["porta_logada"],"E"); ?>');
   };
</script>
</head>

<body>
<table width="360" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
<tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
      <td width="330" align="left" style="color: #333333;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:12px;font-weight:bold;" scope="col"><?php echo $lang['lang_info_gerenciador_musicas_info_upload_multiplo']; ?></td>
  </tr>
  </table>
<form id="upload" method="post" action="http://<?php echo strtolower($dados_servidor["nome"]); ?>.<?php echo $dados_config["dominio_padrao"]; ?>:555/upload-musicas.php" enctype="multipart/form-data">
<input name="porta" type="hidden" value="<?php echo $_SESSION["porta_logada"]; ?>" />
<input name="pasta" type="hidden" value="<?php echo query_string('1'); ?>" />
			<div id="drop">
				<a><?php echo $lang['lang_info_gerenciador_musicas_botao_selecionar_musicas']; ?></a>
				<input type="file" name="upl" multiple />
			</div>

			<ul>
				<!-- The file uploads will be shown here -->
			</ul>

</form>
<!-- JavaScript Includes -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="/inc/jquery.knob.js"></script>

<!-- jQuery File Upload Dependencies -->
<script src="/inc/jquery.ui.widget.js"></script>
<script src="/inc/jquery.iframe-transport.js"></script>
<script src="/inc/jquery.fileupload.js"></script>
		
<!-- Our main JS file -->
<script src="/inc/ajax.upload.js"></script>
</body>
</html>
