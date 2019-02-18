<?php
// Players Windows/Linux/Android Link
$porta = query_string('1');

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

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/".$porta.".txt","".$servidor."|".$servidor_rtmp."|".$autoplay."|".$autodj_prog_aovivo."|".$autodj_prog_aovivo_msg."|".$volume_inicial."|".$aacplus."");

} else { // Else -> Checagem conexão mysql -> Não conectado

list($servidor, $servidor_rtmp, $autoplay, $autodj_prog_aovivo, $autodj_prog_aovivo_msg, $volume_inicial, $aacplus) = explode("|",@file_get_contents("cache/".$porta.".txt"));

} // FIM -> Checagem conexão mysql

if(is_numeric(query_string('2'))) {
$ponto = query_string('2');
list($player, $extensao) = explode(".",query_string('3'));
} else {
$ponto = "1";
list($player, $extensao) = explode(".",query_string('2'));
}

if($player == "mediaplayer") {

// Extensão: .asx

header("Location: http://".$servidor.":".$porta."/listen.asx?sid=".$ponto."");
exit();

} elseif($player == "winamp") {

// Extensão: .pls

header("Location: http://".$servidor.":".$porta."/listen.pls?sid=".$ponto."");
exit();

} elseif($player == "vlc") {

// Extensão: .m3u

header("Location: http://".$servidor.":".$porta."/listen.m3u?sid=".$ponto."");
exit();

} elseif($player == "itunes") {

// Extensão: .pls

header("Location: http://".$servidor.":".$porta."/listen.pls?sid=".$ponto."");
exit();

} elseif($player == "realplayer") {

// Extensão: .rm

header("Location: http://".$servidor.":".$porta."/listen.ram?sid=".$ponto."");
exit();

} elseif($player == "quicktime") {

// Extensão: .qtl

header("Location: http://".$servidor.":".$porta."/listen.qtl?sid=".$ponto."");
exit();

} elseif($player == "iphone") {

header("Location: http://".$servidor.":".$porta."/listen.m3u?sid=".$ponto."");
exit();

} elseif($player == "android") {

header("Location: rtsp://".$servidor_rtmp."/".$porta."/".$porta.".stream");
exit();

} else {

// Extensão: .pls

header("Location: http://".$servidor.":".$porta."/listen.pls?sid=".$ponto."");
exit();

}


?> 