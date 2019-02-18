<?php
////////////////////////////////////////
//////////// Funções Gerais ////////////
////////////////////////////////////////

// Função para gerenciar query string
function query_string($posicao='0') {

$gets = explode("/",str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
array_shift($gets);

return utf8_decode(urldecode($gets[$posicao]));

}

// Função para codificar e decodificar strings
function code_decode($texto, $tipo = "E") {

  if($tipo == "E") {
  
  $sesencoded = $texto;
  $num = mt_rand(0,3);
  for($i=1;$i<=$num;$i++)
  {
     $sesencoded = base64_encode($sesencoded);
  }
  $alpha_array = array('0','D','5','R','7','Y','8','M','A','T','Z','X','A','E','Y','4','8','1','D','J','L');
  $sesencoded =
  $sesencoded."+".$alpha_array[$num];
  $sesencoded = base64_encode($sesencoded);
  return $sesencoded;
  
  } else {
  
   $alpha_array = array('0','D','5','R','7','Y','8','M','A','T','Z','X','A','E','Y','4','8','1','D','J','L');
   $decoded = base64_decode($texto);
   list($decoded,$letter) = explode("+",$decoded);
   for($i=0;$i<count($alpha_array);$i++)
   {
   if($alpha_array[$i] == $letter)
   break;
   }
   for($j=1;$j<=$i;$j++)
   {
      $decoded = base64_decode($decoded);
   }
   return $decoded;
  }
}

function xml_entity_decode($_string) {
    // Set up XML translation table
    $_xml=array();
    $_xl8=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT);
    while (list($_key,)=each($_xl8))
        $_xml['&#'.ord($_key).';']=$_key;
    return strtr($_string,$_xml);
}

// Função para capturar informações de um streaming no shoutcast
function shoutcast_info($ip,$porta,$ponto) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/stats?sid=".$ponto."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

return array("ouvintes_total" => $xml->CURRENTLISTENERS, "ouvintes" => $xml->MAXLISTENERS, "bitrate" => $xml->BITRATE, "encoder" => $xml->CONTENT, "musica" => $xml->SONGTITLE, "titulo" => $xml->SERVERTITLE, "pico_ouvintes" => $xml->PEAKLISTENERS, "proxima_musica" => $xml->NEXTTITLE, "genero" => $xml->SERVERGENRE);
}

// Função para capturar as últimas musicas tocadas de um streaming no shoutcast
function shoutcast_last_songs($ip,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/admin.cgi?sid=1&mode=viewxml&page=4&pass=".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_musicas = count($xml->SONGHISTORY->SONG);

if($total_musicas > 0) {

for($i=0;$i<$total_musicas;$i++){
$musicas .= "[".date("d/m/Y H:i:s",intval($xml->SONGHISTORY->SONG[$i]->PLAYEDAT))."] ".str_replace("_", " ",$xml->SONGHISTORY->SONG[$i]->TITLE)."<br>";
}

return substr($musicas,0,-1);

}

}

// Função para capturar informações dos pontos de um streaming
function shoutcast_multipoint_info($ip,$porta) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/statistics");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_pontos = $xml->STREAMSTATS->TOTALSTREAMS;

if($total_pontos > 1) {

$pontos = "";

for($i=0;$i<$total_pontos;$i++){
$id = $i+1;
$pontos .= $xml->STREAMSTATS->STREAM[$i]->BITRATE."Kbps|";
}

return substr($pontos,0,-1);

} else {
return false;
}

}

// Função de integração com api LastFM
function lastfm($tipo,$chave) {

$chave = urlencode($chave);
$url = "https://ws.audioscrobbler.com/2.0/?method=".$tipo.".getinfo&".$tipo."=".$chave."&user=advancehostbr&api_key=70ecb546f2e36b9858b1bbf14343a120";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);
                
if(!$resultado) {
return;  // Artist lookup failed.
}

$xml = new SimpleXMLElement($resultado);

if($xml->artist->image[2]) {
return array("status" => "ok", "imagem" => $xml->artist->image[2]);
} else {
return array("status" => "ok", "imagem" => $xml->artist->similar->artist->image[2]);
}
     
}

// Função de integração com api LastFM
function vagalumeapi($tipo,$chave1,$chave2) {

$chave1 = urlencode(trim($chave1));
$chave2 = urlencode(trim($chave2));

if($tipo == "capa1") {
$url = "https://api.vagalume.com.br/search.php?art=".$chave1."&extra=artpic&nolyrics=1";
} elseif($tipo == "capa2") {
$url = "https://api.vagalume.com.br/search.php?art=".$chave1."&extra=alb&mus=".$chave2."&nolyrics=1";
} else {
$url = "https://api.vagalume.com.br/search.php?art=".$chave1."&mus=".$chave2."";
}

$resultado = @file_get_contents($url);

if(!$chave1) {
return array("status" => "erro", "status" => "Letra não dispoível no momento.<br>Lyrics not available at this time.");
}
             
if(!$resultado) {
return array("status" => "erro", "status" => "Não foi possível conectar-se ao servidor de informações sobre a música.<br>Could not connect to the server to get the information of the song.");
}

$resultado = json_decode($resultado);

if($resultado->type == "notfound") {
return array("status" => "erro", "status" => "Letra não dispoível no momento.<br>Lyrics not available at this time.");
}

if($tipo == "capa1") {

return array("status" => "ok", "imagem" => $resultado->art->pic_medium);

} elseif($tipo == "capa2") {

return array("status" => "ok", "imagem" => $resultado->mus[0]->alb->img);

} else {

return array("status" => "ok", "letra" => $resultado->mus[0]->text, "traducao" => $resultado->mus[0]->translate[0]->text);

}
                
}

function data_diff_horas( $data_hora ) {

$time1 = strtotime($data_hora);
$time2 = strtotime(date ("Y-m-d H:i:s"));

$diff = $time2-$time1;
$diff = round($diff/3600);

return $diff;

}
?>