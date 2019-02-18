<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");
require_once("inc/getid3/getid3.php");
require_once("inc/classe.mp3.php");

if(isset($_POST["audio"])) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

$data = substr($_POST['data'], strpos($_POST['data'], ",") + 1);
$decodedData = base64_decode($data);
$filename = urldecode($_POST['audio']);
$fp = fopen('temp/'.$filename, 'wb');
fwrite($fp, $decodedData);
fclose($fp);

// Corrige duração do audio
$getID3 = new getID3;

$musica_info = $getID3->analyze("temp/".$filename);
$duracao = round($musica_info['playtime_seconds']);
$nova_duracao = $duracao/2;

// Corta o audio desnecessário
$mp3 = new mp3;
$mp3->cut_mp3("temp/".$filename."", "temp/".$filename."-novo", 0, $nova_duracao, 'second', false);

unlink("temp/".$filename);
rename("temp/".$filename."-novo","temp/".$filename."");

//shell_exec("/usr/local/bin/sox /home/painel/public_html/temp/".$filename." /home/painel/public_html/temp/".$filename."-novo trim 0 ".$nova_duracao."");
//unlink("/home/painel/public_html/temp/".$filename);
//rename("/home/painel/public_html/temp/".$filename."-novo","/home/painel/public_html/temp/".$filename."");

// Conexão FTP
$ftp = new FTP();
$ftp->conectar($dados_servidor["ip"]);
$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);

$ftp->enviar_arquivo("temp/".$filename,"/".$filename);
unlink("temp/".$filename);

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="inc/recordmp3.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
</head>

<body>
<div id="sub-conteudo">
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gravador_info']; ?></td>
  </tr>
</table>
<table width="700" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo $lang['lang_info_gravador_tab_titulo']; ?></strong></div>
   		  <div class="texto_medio" id="quadro-conteudo">
   		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="70" align="center">
                <span id="botao"><img id="botao" src="img/icones/img-icone-gravar-64x64.png" width="64" height="64" onclick="iniciar_gravacao();" style="cursor:pointer" title="Gravar/Record" /></span></td>
              </tr>
              <tr>
                <td height="20" align="center"><span id="icone_gravando" style="display:none"><img src="img/icones/img-icone-rec-animado.gif" width="16" height="16" /></span></td>
              </tr>
              <tr>
                <td height="220" align="center" valign="top">
                <pre id="log" style="width:70%; height:210px; border:#CCCCCC 1px solid; margin:0px auto"></pre>
                </td>
              </tr>
            </table>
   		  </div>
      </div>
      </td>
    </tr>
  </table>
  <br />
</div>
<script>
function iniciar_gravacao() {
startRecording();
document.getElementById('botao').innerHTML = '<img id="botao" src="img/icones/img-icone-stop-64x64.png" width="64" height="64" onclick="finalizar_gravacao();" style="cursor:pointer" title="Parar/Stop" />';
document.getElementById('icone_gravando').style.display = "block";
}

function finalizar_gravacao() {
stopRecording();
document.getElementById('icone_gravando').style.display = "none";
document.getElementById('botao').innerHTML = '<img id="botao" src="img/icones/img-icone-gravar-64x64.png" width="64" height="64" onclick="iniciar_gravacao();" style="cursor:pointer" title="Gravar/Record" />';
}

function __log(e, data) {
    log.innerHTML += "\n" + e + " " + (data || '');
}

  var audio_context;
  var recorder;

  function startUserMedia(stream) {
    var input = audio_context.createMediaStreamSource(stream);
    __log('<?php echo $lang['lang_info_gravador_log4']; ?>' );
    
    input.connect(audio_context.destination);
    
    recorder = new Recorder(input);
    __log('<?php echo $lang['lang_info_gravador_log5']; ?>');
  }

  function startRecording() {
    recorder && recorder.record();
    __log('<?php echo $lang['lang_info_gravador_log6']; ?>');
  }

  function stopRecording() {
    recorder && recorder.stop();
    __log('<?php echo $lang['lang_info_gravador_log7']; ?>');
    
    recorder.clear();
  }
  
  function stopRecording() {
    recorder && recorder.stop();
    __log('Gravação finalizada.');
    
    createDownloadLink();
    
    recorder.clear();
  }

  function createDownloadLink() {
    recorder && recorder.exportWAV(function(blob) {
    });
  }
  
  window.onload = function init() {
  fechar_log_sistema();
    try {
      // webkit shim
      window.AudioContext = window.AudioContext || window.webkitAudioContext;
      navigator.getUserMedia = ( navigator.getUserMedia ||
                       navigator.webkitGetUserMedia ||
                       navigator.mozGetUserMedia ||
                       navigator.msGetUserMedia);
      window.URL = window.URL || window.webkitURL;
      
      audio_context = new AudioContext;
      __log('<?php echo $lang['lang_info_gravador_log1']; ?>');
	  __log('<?php echo $lang['lang_info_gravador_log2']; ?>');
    } catch (e) {
      alert('<?php echo $lang['lang_info_gravador_log3']; ?>');
    }
    
    navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
      __log('<?php echo $lang['lang_info_gravador_log8']; ?> ' + e);
    });
  };
  </script>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>