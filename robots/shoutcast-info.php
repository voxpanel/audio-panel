<?php
// Gateway shoutcast para aliviar carga do servidor do painel de streaming

function xml_entity_decode($_string) {
    // Set up XML translation table
    $_xml=array();
    $_xl8=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT);
    while (list($_key,)=each($_xl8))
        $_xml['&#'.ord($_key).';']=$_key;
    return strtr($_string,$_xml);
}

// Função para capturar informações de um streaming
function shoutcast_info($ip,$porta,$recurso) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/stats?sid=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

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
} else {
return $xml->SERVERTITLE;
}

}

echo shoutcast_info($_GET["ip"],$_GET["porta"],$_GET["recurso"]);

?>
