<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$porta_code = code_decode($dados_stm["porta"],"E");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_api_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo $lang['lang_info_streaming_api_info']; ?>
    <br />
    <br />
    <span class="texto_padrao_destaque">API XML:</span><br />
  <input type="text" value="<?php echo "http://".$_SERVER['HTTP_HOST']."/api/".$porta_code.""; ?>" style="width:90%; height:30px"  onclick="this.select()" readonly="readonly" />
<br />
<br />
<textarea readonly="readonly" style="width:90%; height:510px"  onclick="this.select()">
$xml = simplexml_load_file("<?php echo "http://".$_SERVER['HTTP_HOST']."/api/".$porta_code.""; ?>");

echo $xml->status; // Mostra o status da rádio
echo "<br>";
echo $xml->porta; // Mostra a porta da rádio
echo "<br>";
echo $xml->porta_dj; // Mostra a porta DJ da rádio
echo "<br>";
echo $xml->ip; // Mostra o endereço do servidor da rádio
echo "<br>";
echo $xml->ouvintes_conectados; // Mostra total de ouvintes conectados
echo "<br>";
echo $xml->titulo; // Mostra o nome da rádio
echo "<br>";
echo $xml->plano_ouvintes; // Mostra o limite de ouvintes do plano
echo "<br>";
echo $xml->plano_ftp; // Mostra o limite de espaço do AutoDJ do plano
echo "<br>";
echo $xml->plano_bitrate; // Mostra o bitrate do plano
echo "<br>";
echo $xml->musica_atual; // Mostra a música atual
echo "<br>";
echo $xml->proxima_musica; // Mostra a próxima música do AutoDJ(não é valido para transmissão ao vivo)
echo "<br>";
echo $xml->genero; // Mostra o genero da rádio
echo "<br>";
echo $xml->shoutcast; // Mostra a URL do shoutcast
echo "<br>";
echo $xml->rtmp; // Mostra a URl do RTMP para uso em players próprios(se tiver RTMP)
echo "<br>";
echo $xml->rtsp; // Mostra a URl do RTSP para uso em players próprios(se tiver RTMP)
echo "<br>";
echo $xml->capa_musica; // Mostra a URL da imagem JPG da capa do album da música
</textarea>
<br />
<br />
<span class="texto_padrao_destaque">API Json (jQuery/Javascript):</span><br />
  <input type="text" value="<?php echo "http://".$_SERVER['HTTP_HOST']."/api-json/".$porta_code.""; ?>" style="width:90%; height:30px"  onclick="this.select()" readonly="readonly" />
<br />
<br />
<textarea readonly="readonly" style="width:90%; height:370px"  onclick="this.select()">
<script type="text/javascript">
$.getJSON('<?php echo "http://".$_SERVER['HTTP_HOST']."/api-json/".$porta_code.""; ?>', function(data) {

var status = data.status; // Mostra o status da rádio
var porta = data.porta; // Mostra a porta da rádio
var porta_dj = data.porta_dj; // Mostra a porta DJ da rádio
var ip = data.ip; // Mostra o endereço do servidor da rádio
var ouvintes_conectados = data.ouvintes_conectados; // Mostra total de ouvintes conectados
var titulo = data.titulo; // Mostra o nome da rádio
var plano_ouvintes = data.plano_ouvintes; // Mostra o limite de ouvintes do plano
var plano_ftp = data.plano_ftp; // Mostra o limite de espaço do AutoDJ do plano
var plano_bitrate = data.plano_bitrate; // Mostra o bitrate do plano
var musica_atual = data.musica_atual; // Mostra a música atual
var proxima_musica = data.proxima_musica; // Mostra a próxima música do AutoDJ(não é valido para transmissão ao vivo)
var genero = data.genero; // Mostra o genero da rádio
var shoutcast = data.shoutcast; // Mostra a URL do shoutcast
var rtmp = data.rtmp; // Mostra a URl do RTMP para uso em players próprios(se tiver RTMP)
var rtsp = data.rtsp; // Mostra a URl do RTSP para uso em players próprios(se tiver RTMP)
var capa_musica = data.capa_musica; // Mostra a URL da imagem JPG da capa do album da música

});
</script>
</textarea>
<br />
<br /></td>
    </tr>
</table>
    </div>
      </div>
<br />
<br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
