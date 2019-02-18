<?php
// Letra Música Players
$porta = query_string('1');
$dominio_padrao = "srvstm.com";

if(!is_numeric($porta)) {
die ("Error! Missing data.");
}

// Verifica se a conexão com mysql foi estabelecida para definir se irá usar os dados do banco de dados ou do cache no TXT
$dados_config = @mysql_fetch_array(@mysql_query("SELECT * FROM configuracoes"));

// Verifica a última modificação do cache para usa-lo ou atualiza-lo
$data_hora_cache = date ("Y-m-d H:i:s", @filemtime("cache/".$porta.".txt"));
$checagem_ultima_modificacao_cache = data_diff_horas( $data_hora_cache );

if(!empty($dados_config["dominio_padrao"]) && $checagem_ultima_modificacao_cache > 12) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$servidor = $dados_servidor["ip"];
$servidor_rtmp = $dados_servidor_aacplus["ip"];
$autoplay = $dados_stm["player_autoplay"];
$autodj_prog_aovivo = $dados_stm["autodj_prog_aovivo"];
$autodj_prog_aovivo_msg = $dados_stm["autodj_prog_aovivo_msg"];
$volume_inicial = $dados_stm["player_volume_inicial"];
$aacplus = $dados_stm["aacplus"];

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/".$porta.".txt","".$servidor."|".$servidor_rtmp."|".$autoplay."|".$autodj_prog_aovivo."|".$autodj_prog_aovivo_msg."|".$volume_inicial."|".$aacplus."");

} else { // Else -> Checagem conexão mysql -> Não conectado

list($servidor, $servidor_rtmp, $autoplay, $autodj_prog_aovivo, $autodj_prog_aovivo_msg, $volume_inicial, $aacplus) = explode("|",@file_get_contents("cache/".$porta.".txt"));

} // FIM -> Checagem conexão mysql
	
$musica = file_get_contents("http://".$servidor.":".$porta."/currentsong?sid=1");

$musica = str_replace("_"," ",$musica);
$musica = ucwords(strtolower($musica));
$musica_partes = explode("-",$musica);

$resultado = vagalumeapi('letra',$musica_partes[0],$musica_partes[1]);

$letra = ($resultado["status"] == "ok") ? $resultado["letra"] : $resultado["status_msg"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Player</title>
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
	text-align:center;
	float:left
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
<?php if($autodj_prog_aovivo == "sim") { ?>
<div class="texto_prog_aovivo"><?php echo $autodj_prog_aovivo_msg; ?></div>
<?php } else { ?>
<div id="topo"><br /><span class="texto_musica"><?php echo utf8_decode($musica); ?></span><br /><br /></div>
<div id="letra-original" class="texto_letra"><p><?php echo nl2br(utf8_decode($letra)); ?></p></div>
<div id="letra-traducao" class="texto_letra"><p><?php echo nl2br($resultado["traducao"]); ?></p></div>
<?php } ?>
</div>
</body>
</html>
