<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('1')."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$nome_radio = ($dados_stm["streamtitle"]) ? $dados_stm["streamtitle"] : "OuÃ§a nossa RÃ¡dio no FaceBook";

$host_sources = ($dados_config["usar_cdn"] == "sim") ? $dados_config["dominio_cdn"] : $_SERVER['HTTP_HOST'];

if($dados_stm["idioma_painel"]) {
require_once("inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

$descricao = ($dados_stm["descricao"]) ? $dados_stm["descricao"] : $lang['lang_info_player_facebook_play'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta name="twitter:card" content="player">
<meta name="twitter:domain" content="http://<?php echo $dados_config["dominio_padrao"]; ?>/player-twitter/<?php echo $dados_stm["porta"]; ?>">
<meta name="twitter:url" content="http://<?php echo $dados_config["dominio_padrao"]; ?>/player-twitter/<?php echo $dados_stm["porta"]; ?>">
<meta name="twitter:title" content="<?php echo $nome_radio; ?>">
<meta name="twitter:description" content="<?php echo $descricao; ?>">
<meta name="twitter:image:src" content="http://<?php echo $dados_config["dominio_cdn"]; ?>/img/icones/img-icone-play-facebook.jpg?<?php echo time(); ?>">
<meta name="twitter:image:width" content="150">
<meta name="twitter:image:height" content="150">

<?php if($dados_stm["aacplus"] == 'sim') { ?>

<meta name="twitter:player" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player-aacplus.swf?file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=false">
<meta name="twitter:player:stream" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player-aacplus.swf?file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=false">
<meta name="twitter:player:height" content="33">

<?php } else { ?>

<meta name="twitter:player" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player.swf?file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&autostart=false">
<meta name="twitter:player:stream" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player.swf?file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&autostart=false">
<meta name="twitter:player:height" content="20">

<?php } ?>
<meta name="twitter:player:width" content="380">
<meta name="twitter:player:stream:content_type" content="video/mp4; codecs=&quot;avc1.42E01E1, mp4a.40.2&quot;">
<title><?php echo $nome_radio; ?></title>
<?php if(query_string('2') == "fechar") { ?>
<script type="text/javascript">janela = window.open(window.location, "_self");janela.close();</script>
<?php } ?>
</head>
	  
<body oncontextmenu="return false" onkeydown="return false">
<?php if($dados_stm["aacplus"] == 'sim') { ?>
<center><?php echo $nome_radio; ?><br />
<embed src="http://<?php echo $host_sources; ?>/player-aacplus.swf" width="280" height="20" allowscriptaccess="always" allowfullscreen="true" flashvars="file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=false" type="application/x-shockwave-flash" /></embed></center>
<?php } else { ?>
<center><?php echo $nome_radio; ?><br /><embed height="17" width="260" flashvars="file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&volume=100&bufferlength=0" allowscriptaccess="always" quality="high" src="http://<?php echo $host_sources; ?>/player.swf" type="application/x-shockwave-flash"></embed></center>
<?php } ?>
</body>
</html>