<?php
$porta = query_string('1');
$modelo = (is_numeric(query_string('2'))) ? query_string('2') : "1";
//$rtmp_forcado = (query_string('3') == "rtmp" || query_string('2') == "rtmp") ? "sim" : "nao";
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

if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
$autoplay = "false";
}

$autoplay_html5 = ($autoplay == "true") ? "autoplay" : "";
$volume_inicial = ($volume_inicial) ? $volume_inicial : "1.0";

$check_protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/')));
$host_url = ($_SERVER['HTTP_HOST'] == "player.srvstm.com" && $check_protocol == "https") ? "https://player.srvstm.com" : "http://".$_SERVER['HTTP_HOST']."";

$array_lang = array("pt" => array("letra" => "Letra da Música", "ultimas_musicas" => "Últimas Músicas"),
					"en" => array("letra" => "Song Lyrics", "ultimas_musicas" => "Last Songs"), 
					"es" => array("letra" => "Letra da Musica", "ultimas_musicas" => "Últimas Canciones")
);

$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

if($_GET["fundo"]) {
$backgroud = $_GET["fundo"];
} else {
$backgroud = "".$host_url."/img/img-player-popup-responsivo-fundo-conteudo".$modelo.".jpg";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?></title>
<!-- OpenGraph -->
<meta property="og:title" content="<?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?>">
<meta property="og:url" content="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player-popup-responsivo/<?php echo $dados_stm["porta"]; ?>">
<meta property="og:type" content="music.radio_station">
<meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-play-facebook.jpg">
<meta property="og:description" content="<?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?>">
<meta property="og:site_name" content="<?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?>">
<script type="text/javascript" src="/inc/ajax-player-responsivo.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="/inc/jquery-popup.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<style>
body {
	background: #000000 url(<?php echo $backgroud; ?>);
	background-size: cover;
    background-repeat: no-repeat;
}
#musica_capa {
	position: relative;
	width:200px;
	height:170px;
	margin:0px auto;
	padding-top:10px;
	float:left;
	text-align:center;
}
#musica_titulo {
	position: relative;
	margin-top:5%;
	margin-bottom:5%;
	float:left;
	text-align:left;
	padding-left:5px;
}
#controles {
	background: rgba(0, 0, 0, 0.5);
	border-radius: 5px;
	width:100%;
	height:35px;
	margin:0px auto;
	text-align:left;
	float:left;
	clear:both
}
#controles_botoes {
	position: relative;
	height:30px;
	padding-left:5px;
	padding-right:15px;
	text-align:left;
	float:left;
	z-index:100;
}
#controles_volume {
	position: relative;
	height:30px;
	padding-top:13px;
	text-align:left;
	float:left;
	z-index:100;
}
#controles_icones {
	position: relative;
	height:30px;
	padding-top:5px;
	padding-right:5px;
	text-align:right;
	float:right;
	z-index:100;
}
#custom-handle {
    width: 20px;
    height: 15px;
    top: 50%;
    margin-top: -8px;
    text-align: center;
    line-height: 15px;
	color: #000000; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:9px;
	font-weight:normal;
}
.texto_padrao {
	color: #000000; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:normal;
}
.texto_musica {
	color: #FFFFFF; /* COR */
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:14px;
	font-weight:normal;
	text-shadow: black 1px 1px;
}
.menu li{display:block;width:100%}.menu{background:#fafafa;border-radius:2px;box-shadow:0 2px 4px 0 rgba(0,0,0,.16),0 2px 8px 0 rgba(0,0,0,.12);color:#757575;padding:16px 0;position:absolute;top:48px;transform:scale(0);transition:transform .2s;z-index:96}.menu.show{transform:scale(1)}.menu.menu--right{transform-origin:top right}.menu.menu--left{transform-origin:top left}.menu li{min-height:25px;line-height:16px;margin:8px 0;padding:0 16px}.menu li.menu-separator{background:#eee;height:1px;min-height:0;margin:12px 0;padding:0}.menu li:first-child{margin-top:0}.menu li:last-child{margin-bottom:0}.menu a{color:inherit;height:25px;line-height:25px;padding:0;text-decoration:none;white-space:nowrap}.menu a:hover{color:#444}

/* Magnific Popup CSS */
.mfp-bg{top:0;left:0;width:100%;height:100%;z-index:1042;overflow:hidden;position:fixed;background:#000;opacity:.8}.mfp-wrap{top:0;left:0;width:100%;height:100%;z-index:1043;position:fixed;outline:none!important;-webkit-backface-visibility:hidden}.mfp-container{text-align:center;position:absolute;width:100%;height:100%;left:0;top:0;padding:0 8px;box-sizing:border-box}.mfp-container:before{content:'';display:inline-block;height:100%;vertical-align:middle}.mfp-align-top .mfp-container:before{display:none}.mfp-content{position:relative;display:inline-block;vertical-align:middle;margin:0 auto;text-align:left;z-index:1045}.mfp-inline-holder .mfp-content,.mfp-ajax-holder .mfp-content{width:100%;cursor:auto}.mfp-ajax-cur{cursor:progress}.mfp-zoom-out-cur,.mfp-zoom-out-cur .mfp-image-holder .mfp-close{cursor:-moz-zoom-out;cursor:-webkit-zoom-out;cursor:zoom-out}.mfp-zoom{cursor:pointer;cursor:-webkit-zoom-in;cursor:-moz-zoom-in;cursor:zoom-in}.mfp-auto-cursor .mfp-content{cursor:auto}.mfp-close,.mfp-arrow,.mfp-preloader,.mfp-counter{-webkit-user-select:none;-moz-user-select:none;user-select:none}.mfp-loading.mfp-figure{display:none}.mfp-hide{display:none!important}.mfp-preloader{color:#CCC;position:absolute;top:50%;width:auto;text-align:center;margin-top:-.8em;left:8px;right:8px;z-index:1044}.mfp-preloader a{color:#CCC}.mfp-preloader a:hover{color:#FFF}.mfp-s-ready .mfp-preloader{display:none}.mfp-s-error .mfp-content{display:none}button.mfp-close,button.mfp-arrow{overflow:visible;cursor:pointer;background:transparent;border:0;-webkit-appearance:none;display:block;outline:none;padding:0;z-index:1046;box-shadow:none;touch-action:manipulation}button::-moz-focus-inner{padding:0;border:0}.mfp-close{width:44px;height:44px;line-height:44px;position:absolute;right:0;top:0;text-decoration:none;text-align:center;opacity:.65;padding:0 0 18px 10px;color:#FFF;font-style:normal;font-size:28px;font-family:Arial,Baskerville,monospace}.mfp-close:hover,.mfp-close:focus{opacity:1}.mfp-close:active{top:1px}.mfp-close-btn-in .mfp-close{color:#FFF}

</style>
</head>

<body>
<div id="musica_capa"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/spinner.gif" id="capa" /></div>
<div id="musica_titulo"><span id="musica_atual" class="texto_musica"></span></div>
<div id="controles">
<div id="controles_botoes"><audio id="player_html5" <?php echo $autoplay_html5; ?> src="http://<?php echo $servidor; ?>:<?php echo $porta; ?>/;">Seu navegador n&atilde;o tem suporte a HTML5</audio><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-player-html5-<?php if($autoplay == "true") { echo "pause.png"; } else { echo "play.png"; } ?>" width="35" height="35" align="middle" style="cursor:pointer" id="btn_play_pause" />&nbsp;&nbsp;<img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-volume.png" width="20" height="20" align="middle" onclick="document.getElementById('player_html5').muted = true;" style="cursor:pointer" title="Mute" id="mute" /></div>
<div id="controles_volume"><div id="controle_volume" style="width:90px; height:7px"><div id="custom-handle" class="ui-slider-handle"></div></div></div>
<div id="controles_icones"><a href="javascript:;" class="toggle" id="menu-toggle-share"><img src="<?php echo $host_url; ?>/img/icones/img-icone-share.png" width="24" height="24" border="0" align="absmiddle" /></a>&nbsp;<a href="javascript:;" class="toggle" id="menu-toggle-opcoes"><img src="<?php echo $host_url; ?>/img/icones/img-icone-3dots.png" width="24" height="24" border="0" align="absmiddle" /></a></div>
</div>
<ul class="menu" data-menu data-menu-toggle="#menu-toggle-opcoes">
    <li class="texto_padrao"><a href="<?php echo $host_url; ?>/player-popup-responsivo-letra/<?php echo $porta; ?>" class="popup"><?php echo $array_lang[$lang]["letra"]; ?></a></li>
	<li class="texto_padrao"><a href="<?php echo $host_url; ?>/player-popup-responsivo-musicas/<?php echo $porta; ?>" class="popup"><?php echo $array_lang[$lang]["ultimas_musicas"]; ?></a></li>
</ul>
<ul class="menu" data-menu data-menu-toggle="#menu-toggle-share">
    <li class="texto_padrao"><a href="#" onclick="abrir_janela('https://www.facebook.com/sharer/sharer.php?app_id=522557647825370&display=popup&redirect_uri=https://<?php echo $dominio_padrao; ?>/player-facebook/<?php echo $porta; ?>/fechar&u=https://<?php echo $dominio_padrao; ?>/player-facebook/<?php echo $porta; ?>',500,300);">Facebook</a></li>
	<li class="texto_padrao"><a href="#" onclick="abrir_janela('https://twitter.com/intent/tweet?text=<?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?>&url=http://srvstm.com/player-twitter/<?php echo $porta; ?>',500,300);">Twitter</a></li>
</ul>
<script type="text/javascript">
// Volume Inicial player HTML5
document.getElementById("player_html5").volume=<?php echo $volume_inicial; ?>;
// Slider controle de volume jquery
$( function() {
    var handle = $( "#custom-handle" );
$('#controle_volume').slider({
    value: <?php echo $volume_inicial; ?>,
	range: "min",
    min: 0,
    max: 1,
    step: .1,
    slide: function(event, ui) {
		document.getElementById("player_html5").muted = false;
        document.getElementById("player_html5").volume = ui.value;
		volume_atual = ui.value * 100;
		volume_atual = volume_atual+"%";
		handle.text( volume_atual );
    }
	});
	$( "#mute" ).on( "click", function() {
      $('#controle_volume').slider( "value", 0	 );
    });
});
// Botao play/pause
$(document).ready(function () {
    var is_play = <?php echo $autoplay; ?>;

    $('#btn_play_pause').click(function () {
        if (is_play){
            is_play  = false;
            $(this).attr("src", "http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-player-html5-play.png");
			document.getElementById('player_html5').pause();
        } else {
            $(this).attr("src", "http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/icones/img-icone-player-html5-pause.png");
			document.getElementById('player_html5').play();
			is_play  = true;
        }
    });
});

<?php if($autodj_prog_aovivo == "sim") { ?>
document.getElementById('musica_atual').innerHTML = "<?php echo $autodj_prog_aovivo_msg; ?>";
document.getElementById('musica_capa').innerHTML = "";
<?php } else { ?>
// Atualizar informações do streaming
musica_atual_players( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'musica_atual','38');
capa_musica_atual( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'capa', 150);
setInterval("musica_atual_players( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'musica_atual','38')",20000);
setInterval("capa_musica_atual( '<?php echo $servidor; ?>', <?php echo $porta; ?>,'capa', 150)",20000);
<?php } ?>
function abrir_popup_letra() {
window.open( "<?php echo $host_url; ?>/player-letra-musica/<?php echo $porta; ?>", "","width=650,height=500,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=no,resizable=NO" );
}

// Menus
!function(a){"use strict";function b(b,d){this.$el=a(b),this.opt=a.extend(!0,{},c,d),this.init(this)}var c={};b.prototype={init:function(b){a(document).on("click",function(c){var d=a(c.target);d.closest(b.$el.data("menu-toggle"))[0]?(d=d.closest(b.$el.data("menu-toggle")),b.$el.css(b.calcPosition(d)).toggleClass("show")):d.closest(b.$el)[0]||b.$el.removeClass("show"),c.preventDefault()})},calcPosition:function(b){var c,d,e;return c=a(window).width(),d=b.offset(),e={top:80},d.left>c/2?(this.$el.addClass("menu--right").removeClass("menu--left"),e.right=c-d.left-b.outerWidth()/2,e.left="auto"):(this.$el.addClass("menu--left").removeClass("menu--right"),e.left=d.left+b.outerWidth()/2,e.right="auto"),e}},a.fn.menu=function(c){return this.each(function(){a.data(this,"menu")||a.data(this,"menu",new b(this,c))})}}(window.jQuery);

$('[data-menu]').menu();

// Popup
$('.popup').magnificPopup({
  type: 'ajax',
  alignTop: true,
  marTop: 0,
  overflowY: 'scroll',
  removalDelay: 300,
});
</script>
</body>
</html>