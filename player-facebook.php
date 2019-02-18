<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('1')."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$nome_radio = ($dados_stm["streamtitle"]) ? $dados_stm["streamtitle"] : "Ouça nossa Rádio no FaceBook";

if($dados_stm["idioma_painel"]) {
require_once("inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

$descricao = ($dados_stm["descricao"]) ? $dados_stm["descricao"] : $lang['lang_info_player_facebook_play'];
/*
// Grava acesso do ouvinte
$verifica_ouvinte = mysql_num_rows(mysql_query("SELECT * FROM estatisticas_redessociais where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$_SERVER['REMOTE_ADDR']."' AND data = '".date("Y-m-d")."'"));

if($verifica_ouvinte == 0) {

$pais = pais_ip($_SERVER['REMOTE_ADDR'],"nome");

mysql_query("INSERT INTO estatisticas_redessociais (codigo_stm,data,ip,pais,player) VALUES ('".$dados_stm["codigo"]."',NOW(),'".$_SERVER['REMOTE_ADDR']."','".$pais."','Facebook')") or die("Erro MySQL: ".mysql_error());
}
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:g="http://base.google.com/ns/1.0">
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta property="fb:app_id" content="1501566206837665" />
<meta property="og:locale" content="pt_BR" />
<meta property="og:site_name" content="<?php echo $nome_radio; ?>" />
<meta property="og:title" content="<?php echo $nome_radio; ?>" />
<meta property="og:description" content="<?php echo $descricao; ?>">
<meta property="og:type" content="video" />
<meta property="og:url" content="http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>" />
<meta property="og:image" content="http://<?php echo $dados_config["dominio_padrao"]; ?>/img/icones/img-icone-play-facebook.jpg" />
<meta property="og:image:width" content="150" />
<meta property="og:image:height" content="150" />
<?php if($dados_stm["aacplus"] == 'sim') { ?>
<meta property="og:video" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player-aacplus.swf?file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=true" />
<meta property="og:video:secure_url" content="https://<?php echo $dados_config["dominio_padrao"]; ?>/player-aacplus.swf?file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=true" />
<meta property="og:video:height" content="45" />
<?php } else { ?>
<meta property="og:video" content="http://<?php echo $dados_config["dominio_padrao"]; ?>/player.swf?file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&autostart=true" />
<meta property="og:video:secure_url" content="http://<?php echo $dados_config["dominio_padrao"]; ?>/player.swf?file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&autostart=true" />
<meta property="og:video:height" content="20" />
<?php } ?>
<meta property="og:video:width" content="380" />
<meta property="og:video:type" content="application/x-shockwave-flash" />
<title><?php echo $nome_radio; ?></title>
<?php if(query_string('2') == "fechar") { ?>

<?php } ?>
</head>
	  
<body oncontextmenu="return false" onkeydown="return false">
<?php echo $nome_radio; ?>
</body>
</html>