<?php
$porta = query_string('1');
$modelo = (is_numeric(query_string('2'))) ? query_string('2') : "1";
$rtmp_forcado = (query_string('3') == "rtmp" || query_string('2') == "rtmp") ? "sim" : "nao";
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
$chat = $dados_stm["player_exibir_chat"];
$pedidos_musicais = $dados_stm["player_exibir_pedido_musical"];

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/".$porta.".txt","".$servidor."|".$servidor_rtmp."|".$autoplay."|".$autodj_prog_aovivo."|".$autodj_prog_aovivo_msg."|".$volume_inicial."|".$aacplus."|".$chat."|".$pedidos_musicais."");

} else { // Else -> Checagem conexão mysql -> Não conectado

list($servidor, $servidor_rtmp, $autoplay, $autodj_prog_aovivo, $autodj_prog_aovivo_msg, $volume_inicial, $aacplus, $chat, $pedidos_musicais) = explode("|",@file_get_contents("cache/".$porta.".txt"));

} // FIM -> Checagem conexão mysql

$dados_servidor_shoutcast = shoutcast_info($servidor,$porta,1);
$encoder = ($dados_servidor_shoutcast["encoder"] == "audio/aacp") ? "aac" : 'mp3';

$autoplay_html5 = ($autoplay == "true") ? "autoplay" : "";
$volume_inicial = ($volume_inicial) ? $volume_inicial : "1.0";

$cor_fundo = ($cor_fundo) ? $cor_fundo : '000000';

if($cor_fundo == "FFFFFF") {
$cor_texto = "000000";
} elseif($cor_fundo == "FFCC00") {
$cor_texto = "000000";
} elseif($cor_fundo == "00FF00") {
$cor_texto = "000000";
} elseif($cor_fundo == "FF00FF") {
$cor_texto = "000000";
} else {
$cor_texto = "FFFFFF";
}

$check_protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/')));
$host_url = ($_SERVER['HTTP_HOST'] == "player.srvstm.com" && $check_protocol == "https") ? "https://player.srvstm.com" : "http://".$_SERVER['HTTP_HOST']."";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?></title>
<script type="text/javascript" src="/inc/ajax-player.js"></script>
<style>
body {
	background: #000000;
	margin: 0px auto;
	overflow: hidden;
}
#player {
	width:400px;
	height:300px;
	margin:0px auto;
}
#player-topo {
	background-color:#000000;
	width:100%;
	height:30px;
	margin:0px auto;
}
#player-links {
	width:100%;
	height:30px;
	margin:0px auto;
	text-align:right;
	float:right;
}
#player-conteudo {
	background:url(<?php echo $host_url; ?>/img/img-player-popup-fundo-conteudo<?php echo $modelo; ?>.jpg) repeat-x;
	width:100%;
	height:190px;
	margin:0px auto;
	text-align:center;
}
#player-conteudo-capa {
	width:100%;
	height:160px;
	margin:0px auto;
	text-align:center;
}
#player-conteudo-musica {
	width:100%;
	height:30px;
	margin:0px auto;
	text-align:center;
}
#player-controles {
	background-color:#000000;
	width:150px;
	height:30px;
	margin:0px auto;
	text-align:center;
	float:left
}
#player-vu-meter {
	background-color:#000000;
	width:250px;
	height:30px;
	margin:0px auto;
	text-align:center;
	float:right
}
.marquee {
    width:100%;
    height:25px; 
    overflow:hidden;
    white-space:nowrap;
    padding-top:5px;
}

.marquee span {
    display: inline-block;
    padding-left: 100%;
    animation: marquee 15s linear infinite;
}

@keyframes marquee {
    0%   { transform: translate(0, 0); }
    100% { transform: translate(-100%, 0); }
}
.texto_titulo {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:13px;
	font-weight:normal;
}
.texto_url_site {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:10px;
	font-weight:normal;
}
.texto_musica {
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:normal;
	text-shadow: #000000 1px 1px 1px;
	cursor:pointer;
}
.texto_padrao {
	color: #000000;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:normal;
}
</style>
</head>

<body>
<div id="player">
<div id="player-topo">
<div id="player-links">
<a href="javascript:abrir_popup_letra();"><img src="<?php echo $host_url; ?>/img/icones/img-icone-letra-musica.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $host_url; ?>/player/<?php echo $porta; ?>/winamp.pls" target="_blank"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-winamp.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $host_url; ?>/player/<?php echo $porta; ?>/mediaplayer.asx"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-mediaplayer.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $host_url; ?>/player/<?php echo $porta; ?>/realplayer.rm"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-realplayer.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;<a href="<?php echo $host_url; ?>/player/<?php echo $porta; ?>/iphone.m3u"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-iphone.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;<a href="javascript:abrir_janela('https://www.facebook.com/sharer/sharer.php?app_id=522557647825370&display=popup&redirect_uri=https://<?php echo $dominio_padrao; ?>/player-facebook/<?php echo $porta; ?>/fechar&u=https://<?php echo $dominio_padrao; ?>/player-facebook/<?php echo $porta; ?>',500,300);"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-facebook.png" width="24" height="24" border="0" align="absmiddle" /></a>
<?php if($chat == 'sim') { ?>
&nbsp;<a href="javascript:abrir_janela('<?php echo $host_url; ?>/chat/<?php echo $porta; ?>',750,465);"><img src="<?php echo $host_url; ?>/img/icones/img-icone-chat.png" width="24" height="24" border="0" align="absmiddle" /></a>
<?php } ?>
<?php if($pedidos_musicais == 'sim') { ?>
&nbsp;<a href="javascript:abrir_janela('<?php echo $host_url; ?>/pedido/<?php echo $porta; ?>',505,180);"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-pedido-musical.png" width="24" height="24" border="0" align="absmiddle" /></a>
<?php } ?>
<?php if($aacplus == 'sim') { ?>
<a href="rtsp://<?php echo $servidor_rtmp; ?>/<?php echo $porta; ?>/<?php echo $porta; ?>.stream"><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-android.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;
<?php } ?>

</div>
</div>
<div id="player-conteudo">
<div id="player-conteudo-capa"><br /><img src="<?php echo $host_url; ?>/img/spinner.gif" id="capa"></div>
<div id="player-conteudo-musica">
<div class="marquee"><span id="musica_atual" class="texto_musica" onclick="abrir_popup_letra();"></span></div>
</div>
</div>

<?php if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) { ?>

<div id="player-controles">
<audio id="player_html5" <?php echo $autoplay_html5; ?> src="http://<?php echo $servidor; ?>:<?php echo $porta; ?>/;">Seu navegador n&atilde;o tem suporte a HTML5</audio><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-play.png" width="35" height="35" align="middle" onclick="document.getElementById('player_html5').play();" style="cursor:pointer" />&nbsp;<img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-pause.png" width="30" height="30" align="middle" onclick="document.getElementById('player_html5').pause();" style="cursor:pointer" />&nbsp;<img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-mais.png" width="18" height="45" align="middle" onclick="document.getElementById('player_html5').volume += 0.1;" style="cursor:pointer" />&nbsp;<img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-menos.png" width="18" height="45" align="middle" onclick="document.getElementById('player_html5').volume -= 0.1;" style="cursor:pointer" />
</div>
<div id="player-vu-meter"><img src="<?php echo $host_url; ?>/img/img-player-vu-meter-grande.gif" width="245" height="30" /></div>
<script type="text/javascript">
// Volume Inicial player HTML5
document.getElementById("player_html5").volume=<?php echo $volume_inicial; ?>;
</script>
<?php } ?>

<?php if((strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') || strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) && ($rtmp_forcado == 'nao' && !strpos($_SERVER['HTTP_USER_AGENT'], 'Android'))) { ?>

<div id="player-controles">
<audio id="player_html5" <?php echo $autoplay_html5; ?> src="http://<?php echo $servidor; ?>:<?php echo $porta; ?>/;">Seu navegador n&atilde;o tem suporte a HTML5</audio><img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-play.png" width="35" height="35" align="middle" onclick="document.getElementById('player_html5').play();" style="cursor:pointer" />&nbsp;<img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-pause.png" width="30" height="30" align="middle" onclick="document.getElementById('player_html5').pause();" style="cursor:pointer" />&nbsp;<img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-mais.png" width="18" height="45" align="middle" onclick="document.getElementById('player_html5').volume += 0.1;" style="cursor:pointer" />&nbsp;<img src="<?php echo $host_url; ?>/img/icones/img-icone-player-html5-menos.png" width="18" height="45" align="middle" onclick="document.getElementById('player_html5').volume -= 0.1;" style="cursor:pointer" />
</div>
<div id="player-vu-meter"><img src="<?php echo $host_url; ?>/img/img-player-vu-meter-grande.gif" width="245" height="30" /></div>
<script type="text/javascript">
// Volume Inicial player HTML5
document.getElementById("player_html5").volume=<?php echo $volume_inicial; ?>;
</script>
<?php } ?>

<?php if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') && $rtmp_forcado == 'nao') { ?>

<div id="player-controles" class="texto_musica">Este player não é compátivel com Internet Explorer.</div>
<?php } ?>

<?php if($rtmp_forcado == 'sim') { ?>

<div id="player-controles">
<embed src="<?php echo $host_url; ?>/player-topo.swf" width="90" height="30" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" flashvars="servidor=http://<?php echo $servidor; ?>:<?php echo $porta; ?>/&rtmp=rtmp://<?php echo $servidor_rtmp; ?>/<?php echo $porta; ?>&autostart=<?php echo $autoplay; ?>" type="application/x-shockwave-flash" style="padding-top:5px;" /></embed>
</div>
<div id="player-vu-meter"><img src="<?php echo $host_url; ?>/img/img-player-vu-meter-grande.gif" width="245" height="30" /></div>
<?php } ?>

</div>
</div>
<script type="text/javascript">
function abrir_popup_letra() {
window.open( "<?php echo $host_url; ?>/player-letra-musica/<?php echo $porta; ?>", "","width=650,height=500,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=no,resizable=NO" );
}

<?php if($autodj_prog_aovivo == "sim") { ?>
document.getElementById('musica_atual').innerHTML = "<?php echo $autodj_prog_aovivo_msg; ?>";
document.getElementById('player-conteudo-capa').innerHTML = "";
<?php } else { ?>
// Atualizar informações do streaming
musica_atual_players( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'musica_atual','60');

var flag = true;
function wrapper_musica_radio() {
  if(flag) {
  	document.getElementById("musica_atual").innerHTML = "<img src='https://"+get_host()+"/img/spinner.gif' />";
	document.getElementById("musica_atual").innerHTML = "<strong><?php echo $dados_servidor_shoutcast["titulo"]; ?></strong>";
  } else {
    musica_atual_players( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'musica_atual','60');
  }
  flag = !flag;
}

setInterval("wrapper_musica_radio()",20000);

capa_musica_atual( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'capa', 126);
setInterval("capa_musica_atual( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'capa', 126)",20000);
<?php } ?>
</script>
</body>
</html>