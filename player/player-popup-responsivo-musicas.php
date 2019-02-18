<?php
// Letra Música Players
$porta = query_string('1');
$dominio_padrao = "srvstm.com";

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Músicas/Songs</title>
<style>
body {
	background: #000000;
	margin: 0px auto;
	overflow: auto;
}
#musica {
	width:100%;
	margin:0px auto;
	text-align:center;
	float:left
}
#musicas {
	width:100%;
	margin:0px auto;
	text-align:center;
	padding-top:35px;
	float:left
}
.texto_musica {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:normal;
}
.texto_prog_aovivo {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:20px;
	font-weight:bold;
	text-align:center;
	padding:50px;
}
</style>
</head>

<body>
<div id="musica">
<?php if($dados_stm["autodj_prog_aovivo"] == "sim") { ?>
<div class="texto_prog_aovivo"><?php echo $dados_stm["autodj_prog_aovivo_msg"]; ?></div>
<?php } else { ?>
<div id="musicas" class="texto_musica"><?php echo shoutcast_last_songs($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"]); ?></div>
<?php } ?>
</div>
</body>
</html>
