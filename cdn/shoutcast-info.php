<?php
// Gateway shoutcast para aliviar carga do servidor do painel de streaming
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

function xml_entity_decode($_string) {
    // Set up XML translation table
    $_xml=array();
    $_xl8=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT);
    while (list($_key,)=each($_xl8))
        $_xml['&#'.ord($_key).';']=$_key;
    return strtr($_string,$_xml);
}

// Função para capturar informações de um streaming
function shoutcast_info($ip,$porta,$recurso,$ponto) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/stats?sid=".$ponto."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "Erro ao se conectar.";
} else {

$resultado = xml_entity_decode($resultado);

$xml = simplexml_load_string(utf8_encode($resultado));

if($recurso == "ouvintes-total") {
return $xml->CURRENTLISTENERS;
} elseif($recurso == "ouvintes") {
return $xml->MAXLISTENERS;
} elseif($recurso == "bitrate") {
return $xml->BITRATE;
} elseif($recurso == "encoder") {
return $xml->CONTENT;
} elseif($recurso == "musica") {
return $xml->SONGTITLE;
} elseif($recurso == "pico_ouvintes") {
return $xml->PEAKLISTENERS;
} elseif($recurso == "proxima_musica") {
return $xml->NEXTTITLE;
} elseif($recurso == "genero") {
return $xml->SERVERGENRE;
} else {
return $xml->CURRENTLISTENERS."|".$xml->MAXLISTENERS."|".$xml->BITRATE."|".$xml->CONTENT."|".$xml->SONGTITLE."|".$xml->PEAKLISTENERS."|".$xml->NEXTTITLE."|".$xml->SERVERGENRE;
}

}
}

echo shoutcast_info($_GET["ip"],$_GET["porta"],$_GET["recurso"],$_GET["ponto"]);

?>
