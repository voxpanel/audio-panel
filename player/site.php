<?php
$porta = query_string('1');
$porta = str_replace("_","",$porta);
$dominio_padrao = "srvstm.com";

if(!is_numeric($porta)) {
die ("Error! Missing data.");
}

// Verifica se a conexão com mysql foi estabelecida para definir se irá usar os dados do banco de dados ou do cache no TXT
$dados_config = @mysql_fetch_array(@mysql_query("SELECT * FROM configuracoes"));

// Verifica a última modificação do cache para usa-lo ou atualiza-lo
$data_hora_cache1 = date ("Y-m-d H:i:s", @filemtime("cache/".$porta.".txt"));
$data_hora_cache2 = date ("Y-m-d H:i:s", @filemtime("cache/site-".$porta.".txt"));
$checagem_ultima_modificacao_cache1 = data_diff_horas( $data_hora_cache1 );
$checagem_ultima_modificacao_cache2 = data_diff_horas( $data_hora_cache2 );

if(!empty($dados_config["dominio_padrao"]) && ($checagem_ultima_modificacao_cache1 > 12 || $checagem_ultima_modificacao_cache2 > 12)) {

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

// Site
$dominio = $dados_stm["mini_site_dominio"];
$cor_fundo = $dados_stm["mini_site_cor_fundo"];
$cor_topo = $dados_stm["mini_site_cor_topo"];
$cor_texto_topo = $dados_stm["mini_site_cor_texto_topo"];
$cor_texto_padrao = $dados_stm["mini_site_cor_texto_padrao"];
$cor_texto_rodape = $dados_stm["mini_site_cor_texto_rodape"];
$mini_site_chat = $dados_stm["mini_site_exibir_chat"];
$mini_site_xat_id = $dados_stm["mini_site_exibir_xat_id"];
$url_facebook = $dados_stm["mini_site_url_facebook"];
$url_twitter = $dados_stm["mini_site_url_twitter"];

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/".$porta.".txt","".$servidor."|".$servidor_rtmp."|".$autoplay."|".$autodj_prog_aovivo."|".$autodj_prog_aovivo_msg."|".$volume_inicial."|".$aacplus."|".$chat."|".$pedidos_musicais."");

@file_put_contents("cache/site-".$porta.".txt","".$dominio."|".$cor_fundo."|".$cor_topo."|".$cor_texto_topo."|".$cor_texto_padrao."|".$cor_texto_rodape."|".$mini_site_chat."|".$mini_site_xat_id."|".$url_facebook."|".$url_twitter."");

} else { // Else -> Checagem conexão mysql -> Não conectado

list($servidor, $servidor_rtmp, $autoplay, $autodj_prog_aovivo, $autodj_prog_aovivo_msg, $volume_inicial, $aacplus, $chat, $pedidos_musicais) = explode("|",@file_get_contents("cache/".$porta.".txt"));

list($dominio, $cor_fundo, $cor_topo, $cor_texto_topo, $cor_texto_padrao, $cor_texto_rodape, $mini_site_chat, $mini_site_xat_id, $url_facebook, $url_twitter) = explode("|",@file_get_contents("cache/site-".$porta.".txt"));

} // FIM -> Checagem conexão mysql

$dados_servidor_shoutcast = shoutcast_info($servidor,$porta,1);
$encoder = ($dados_servidor_shoutcast["encoder"] == "audio/aacp") ? "aac" : 'mp3';

$autoplay_html5 = ($autoplay == "true") ? "autoplay" : "";
$volume_inicial = ($volume_inicial) ? $volume_inicial : "1.0";

$cor_fundo = ($cor_fundo) ? $cor_fundo : 'FFFFFF';
$cor_topo = ($cor_topo) ? $cor_topo : 'E9E9E9';
$cor_texto_topo = ($cor_texto_topo) ? $cor_texto_topo : '000000';
$cor_texto_padrao = ($cor_texto_padrao) ? $cor_texto_padrao : '000000';
$cor_texto_rodape = ($cor_texto_rodape) ? $cor_texto_rodape : '000000';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?></title>
<script type="text/javascript" src="/inc/ajax-player.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<style>
body {
	background: #<?php echo $cor_fundo; ?>; /* COR */
	margin: 0px auto;
}
#topo {
	background: #<?php echo $cor_topo; ?>; /* COR */
	width:100%;
	height:70px;
	margin:0px auto;
	border-bottom:#999999 4px solid;
}
#topo-conteudo {
	width:800px;
	height:70px;
	margin:0px auto;
	text-align:left;
}
#topo-conteudo-titulo {
	width:400px;
	height:60px;
	margin:0px auto;
	padding-top:10px;
	text-align:left;
	float:left
}
#topo-conteudo-icones {
	width:400px;
	height:55px;
	margin:0px auto;
	padding-top:15px;
	text-align:right;
	float:right
}
#player {
	width:800px;
	height:30px;
	margin:0px auto;
	text-align:left;
}
#conteudo {
	width:800px;
	height:245px;
	margin:0px auto;
	text-align:center;
}
#conteudo-coluna-esquerda {
	width:395px;
	height:245px;
	margin:0px auto;
	text-align:left;
	float:left
}
#conteudo-coluna-direita {
	width:395px;
	height:245px;
	margin:0px auto;
	text-align:left;
	float:right
}
#chat {
	width:800px;
	height:500px;
	margin:0px auto;
	text-align:left;
}
#rodape {
	width:100%;
	height:20px;
	margin:0px auto;
	padding-top:5px;
	padding-bottom:5px;
	text-align:center;
	clear:both
}
#quadro {
	background: #FFFFFF;
	width:100%;
	margin:0px auto;
}
#quadro-topo {
	background: url(../img/img-fundo-quadro.gif) repeat-x;
	border:1px solid #CCCCCC;
	text-align:left;
	height:30px;
	line-height:30px;
	padding-left:5px;
}
#quadro-topo strong {
	color: #333333;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:15px;
	font-weight:bold;
}
#quadro-topo span {
	float:right;
	padding-right:5px;
	text-align:center;
}
#quadro-conteudo {
	border:1px solid #CCCCCC;
	text-align:left;
	padding:5px;
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
    animation: marquee 10s linear infinite;
}

@keyframes marquee {
    0%   { transform: translate(0, 0); }
    100% { transform: translate(-100%, 0); }
}
.texto_topo {
	color: #<?php echo $cor_texto_topo; ?>; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:25px;
	font-weight:normal;
}
.texto_topo_genero {
	color: #<?php echo $cor_texto_topo; ?>; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:normal;
}
.texto_padrao {
	color: #<?php echo $cor_texto_padrao; ?>; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:normal;
}
.texto_rodape {
	color: #<?php echo $cor_texto_rodape; ?>; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:normal;
}
</style>
</head>

<body>
<div id="topo">
<div id="topo-conteudo">
<div class="texto_topo" id="topo-conteudo-titulo">
<?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?><br /><span class="texto_topo_genero"><?php echo ucwords(strtolower($dados_servidor_shoutcast["genero"])); ?></span></div>
<div id="topo-conteudo-icones">
<?php if($url_facebook) { ?>
<a href="<?php echo $url_facebook; ?>" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-facebook-64x64.png" alt="FaceBook" title="FaceBook" width="32" height="32" border="0" /></a>
<?php } ?>
<?php if($url_twitter) { ?>
<a href="<?php echo $url_twitter; ?>" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-player-twitter.png" alt="Twitter" width="32" height="32" border="0" /></a>
<?php } ?>
</div>
</div>
</div>
<br />
<div id="conteudo">
<div id="conteudo-coluna-esquerda">
<div id="quadro">
	<div id="quadro-topo"><strong>Player</strong></div>
	<div class="texto_padrao" id="quadro-conteudo" style="height:210px">
    <div style="width:100%; height:140px; margin:0px auto; text-align:center"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/spinner.gif" id="capa" /></div>
    <div class="marquee"><span id="musica_atual" class="texto_padrao"></span></div>
	<?php if($aacplus == 'sim' && !strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) { ?>
    <div style="background-color:#000000; width:98%; margin:0px auto; text-align:left; border-radius: 10px; border:#000000 1px solid; padding-left:5px">
        <link href="/inc/estilo-player-rtmp.css" rel="stylesheet" />
        <script src="http://vjs.zencdn.net/5.0/video.min.js"></script>
        <video id="player_rtmp" class="video-js vjs-default-skin" controls autoplay width="135" height="30" data-setup='{ "inactivityTimeout": 0, "autoplay": <?php echo $autoplay; ?> }'>
          <source src="rtmp://<?php echo $servidor_rtmp; ?>/<?php echo $porta; ?>/<?php echo $porta; ?>.stream" type='audio/stream'>
        </video>
        <script>
var myPlayer = videojs('player_rtmp', {}, function(){
      var player = this;
      player.on("pause", function () {
        player.one("play", function () {
          player.load();
          player.play();
        });
      });
    })
    videojs('player_rtmp', {}, function() {
      this.volume(<?php echo $volume_inicial; ?>);
    });
  </script>
  </div>
        <?php } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Android') || strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) { ?>
        <div style="background-color:#E9E9E9; width:99%; margin:0px auto; text-align:left; border-radius: 10px; border:#CCCCCC 1px solid">
        <audio id="player_html5" <?php echo $autoplay_html5; ?> src="http://<?php echo $servidor; ?>:<?php echo $porta; ?>/;">Seu navegador n&atilde;o tem suporte a HTML5</audio>
      <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-player-html5-play.png" width="35" height="35" align="middle" onclick="document.getElementById('player_html5').play();" style="cursor:pointer" title="Play" />&nbsp;<img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-player-html5-pause.png" width="30" height="30" align="middle" onclick="document.getElementById('player_html5').pause();" style="cursor:pointer" title="Pause" />&nbsp;<div style="width:80px; padding-right:210px; padding-top:12px; float:right"><div id="controle_volume" style="width:80px;"></div></div>
<script type="text/javascript">
// Volume Inicial player HTML5
document.getElementById("player_html5").volume=<?php echo $volume_inicial; ?>;
// Slider controle de volume jquery
$('#controle_volume').slider({
    value: <?php echo $volume_inicial; ?>,
    min: 0,
    max: 1,
    step: .1,
    slide: function(event, ui) {
        document.getElementById("player_html5").volume = ui.value;
    }
});
</script>
</div>
        <?php } else { ?>
        <div style="background-color:#000000; width:98%; margin:0px auto; text-align:left; border-radius: 10px; border:#000000 1px solid; padding-left:5px">
        <link href="/inc/estilo-player-rtmp.css" rel="stylesheet" />
        <script src="http://vjs.zencdn.net/5.0/video.min.js"></script>
        <video id="player_rtmp" class="video-js vjs-default-skin" controls autoplay width="135" height="30" data-setup='{ "inactivityTimeout": 0, "autoplay": <?php echo $autoplay; ?> }'>
          <source src="http://<?php echo $servidor; ?>:<?php echo $porta; ?>/;" type='audio/<?php echo $encoder; ?>'>
        </video>
        <script>
var myPlayer = videojs('player_rtmp', {}, function(){
      var player = this;
      player.on("pause", function () {
        player.one("play", function () {
          player.load();
          player.play();
        });
      });
    })
    videojs('player_rtmp', {}, function() {
      this.volume(<?php echo $volume_inicial; ?>);
    });
  </script>
  </div>
        <?php } ?>
    </div>
</div>
</div>
<div id="conteudo-coluna-direita">
<div id="quadro">
	<div id="quadro-topo"><strong>Pedir Música</strong></div>
	<div class="texto_padrao" id="quadro-conteudo" style="height:210px">
    <iframe src="<?php echo "http://".$_SERVER['HTTP_HOST']."/pedido/".$porta."/site"; ?>" frameborder="0" width="100%" height="190"></iframe>
    </div>
</div>
</div>
</div>
</div>
<?php if($mini_site_chat == 'sim') { ?>
<br />
<div id="chat">
<div id="quadro">
	<div id="quadro-topo"><strong>Chat</strong></div>
	<div class="texto_padrao" id="quadro-conteudo">
	<iframe src="<?php echo "http://".$_SERVER['HTTP_HOST']."/chat/".$porta.""; ?>" frameborder="0" width="100%" height="465"></iframe>
    </div>
</div>
</div>
<?php } ?>
<?php if($mini_site_chat == 'xat') { ?>
<embed wmode="transparent" src="http://www.xatech.com/web_gear/chat/chat.swf" quality="high" width="790" height="465" name="chat" FlashVars="id=<?php echo $mini_site_xat_id; ?>" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://xat.com/update_flash.php" />
<?php } ?>
<br />
<div class="texto_rodape" id="rodape">Copyright &copy; <?php echo date("Y"); ?> - <?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?></div>
<script type="text/javascript">
<?php if($autodj_prog_aovivo == "sim") { ?>
document.getElementById('musica_atual').innerHTML = "<?php echo $autodj_prog_aovivo_msg; ?>";
document.getElementById('player-conteudo-capa').innerHTML = "http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/img-capa-artista-padrao.png";
<?php } else { ?>
// Atualizar informações do streaming
musica_atual_players( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'musica_atual','50');
capa_musica_atual( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'capa', 126);
setInterval("musica_atual_players( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'musica_atual','50')",20000);
setInterval("capa_musica_atual( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'capa', 126)",20000);
<?php } ?>
</script>
</body>
</html>