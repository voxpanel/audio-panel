<?php
$porta = code_decode(query_string('1'),"D");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

if($dados_revenda["dominio_padrao"]) {
$servidor = strtolower($dados_servidor["nome"]).".".$dados_revenda["dominio_padrao"];
} else {
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$servidor = strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];
}

if($dados_revenda["dominio_padrao"]) {
$servidor_aacplus = strtolower($dados_servidor_aacplus["nome"]).".".$dados_revenda["dominio_padrao"];
} else {
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$servidor_aacplus = strtolower($dados_servidor_aacplus["nome"]).".".$dados_config["dominio_padrao"];
}

// Verifica se arquivo XML existe no cache, se não existir cria ele, se existir usa o cache
$cachefile = "cache/api-".$dados_stm["porta"].".xml";
$cachetime = 30;

if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) { // Usa cache...

header('Content-type: text/xml');

echo @file_get_contents($cachefile);

} else { // Não usa cache...

$xml = new XMLWriter;
$xml->openMemory();
$xml->startDocument('1.0','iso-8859-1');

$xml->startElement("info");

if($dados_stm["status"] == 1) {

$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);

$musica_atual = $info["musica"];
$genero = $info["genero"];
$proxima_musica = $info["proxima_musica"];

############################ Capa Album ############################

$musica_completa = str_replace(" e "," & ",$musica_atual);
$musica_completa = str_replace(" E "," & ",$musica_completa);

$musica_partes = explode("-",$musica_completa);

$artista = str_replace("_"," ",$musica_partes[0]);
$artista = rtrim($artista, " \t.");
	
$musica = $musica_partes[1];

$resultado_api_lastfm_vagalume = lastfm('artist',$artista);
	
if($resultado_api_lastfm_vagalume["status"] == "ok") {
$imagem = $resultado_api_lastfm_vagalume["imagem"];
} else {
$resultado_api_lastfm_vagalume = vagalumeapi('capa2',$artista,$musica);
}

if($resultado_api_lastfm_vagalume["status"] == "ok") {
$imagem = $resultado_api_lastfm_vagalume["imagem"];
} else {
$resultado_api_lastfm_vagalume = vagalumeapi('capa1',$artista,'');
}

if($resultado_api_lastfm_vagalume["status"] == "ok") {
$capa_musica = $resultado_api_lastfm_vagalume["imagem"];
} else {
$capa_musica = "http://".$dados_config["dominio_cdn"]."/img/img-capa-artista-padrao.png";
}


//$capa_musica = "http://".$dados_config["dominio_cdn"]."/img/img-capa-artista-padrao.png";

############################ Status do Streaming ############################

$status_conexao = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
$status_conexao_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
$status_conexao_relay = status_relay($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);

if($status_conexao_relay == "ligado") {
$status_streaming = "Relay";
}

if($status_conexao_autodj == "ligado") {
$status_streaming = "AutoDJ";
}

if($status_conexao == "ligado") {
$status_streaming = "Ligado";
}

############################ Ouvintes Conectados ############################


if($dados_stm["aacplus"] == 'sim' && $dados_servidor_aacplus["ip"] != '') {

$stats_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
$stats_aacplus = stats_ouvintes_aacplus($dados_stm["porta"],$dados_servidor_aacplus["ip"],$dados_servidor_aacplus["senha"]);
$ouvintes_conectados = $stats_shoutcast["ouvintes_total"]+$stats_aacplus["ouvintes"];

if($ouvintes_conectados == 0) {
$ouvintes_conectados = $ouvintes_conectados;
} else {
$ouvintes_conectados = $ouvintes_conectados-1;
}

} else {

$stats_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
$ouvintes_conectados = $stats_shoutcast["ouvintes_total"];

}

#############################################################################
$xml->writeElement("status", $status_streaming);
$xml->writeElement("porta", $dados_stm["porta"]);
$xml->writeElement("porta_dj", $dados_stm["porta_dj"]);
$xml->writeElement("ip", $servidor);
$xml->writeElement("ouvintes_conectados", $ouvintes_conectados);
$xml->writeElement("titulo", $dados_stm["streamtitle"]);
$xml->writeElement("plano_ouvintes", $dados_stm["ouvintes"]);
$xml->writeElement("plano_ftp", tamanho($dados_stm["espaco"]));
$xml->writeElement("plano_bitrate", $dados_stm["bitrate"]."Kbps");
$xml->writeElement("musica_atual", $musica_atual);

if(isset($proxima_musica)) {
$xml->writeElement("proxima_musica", $proxima_musica);
}

$xml->writeElement("genero", $genero);
$xml->writeElement("shoutcast", "http://".$servidor.":".$dados_stm["porta"]."");

if($dados_stm["aacplus"] == 'sim') {
$xml->writeElement("rtmp", "rtmp://".$servidor_aacplus."/".$dados_stm["porta"]."");
$xml->writeElement("rtsp", "rtsp://".$servidor_aacplus."/".$dados_stm["porta"]."/".$dados_stm["porta"].".stream");
}

$xml->writeElement("capa_musica", $capa_musica);

} else {

$xml->writeElement("status", "Desligado");

}

$xml->endElement();

@file_put_contents($cachefile, $xml->outputMemory());

header('Content-type: text/xml');

echo @file_get_contents($cachefile);

}

?>