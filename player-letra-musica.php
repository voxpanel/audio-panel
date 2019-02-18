<?php
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('1')."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
if($dados_config["usar_cdn"] == "sim") {
	
$musica = file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&recurso=musica&ponto=1");
$musica_partes = explode("-",$musica);
	
} else {
	
$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
$musica_partes = explode("-",$info["musica"]);
$musica = $info["musica"];
	
}

$resultado = vagalumeapi('letra',$musica_partes[0],$musica_partes[1]);

$letra = ($resultado["status"] == "ok") ? $resultado["letra"] : $resultado["status_msg"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $dados_stm["streamtitle"];?></title>
<style>
body {
	background: #000000;
	margin: 0px auto;
	overflow: auto;
}
#topo {
	width:600px;
	margin:0px auto;
	text-align:center
}
#musica {
	width:600px;
	margin:0px auto;
	text-align:center
}
#letra-original {
	width:295px;
	margin:0px auto;
	text-align:right;
	float:left;
}
#letra-traducao {
	width:295px;
	margin:0px auto;
	text-align:left;
	float:right;
}
.texto_radio {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:16px;
	font-weight:bold;
}
.texto_musica {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:bold;
}
.texto_letra {
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
<div id="topo" class="texto_radio"><?php echo $dados_stm["streamtitle"]; ?><br /><br /><span class="texto_musica"><?php echo $musica; ?></span><br /><br /></div>
<div id="letra-original" class="texto_letra"><p><?php echo nl2br($letra); ?></p></div>
<div id="letra-traducao" class="texto_letra"><p><?php echo nl2br($resultado["traducao"]); ?></p></div>
<?php } ?>
</div>
</body>
</html>
