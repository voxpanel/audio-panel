<?php
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('1')."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

if(is_numeric(query_string('2'))) {
$ponto = query_string('2');
list($player, $extensao) = explode(".",query_string('3'));
} else {
$ponto = "1";
list($player, $extensao) = explode(".",query_string('2'));
}

if($player == "mediaplayer") {

// Extensão: .asx

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.asx?sid=".$ponto."");
exit();

} elseif($player == "winamp") {

// Extensão: .pls

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.pls?sid=".$ponto."");
exit();

} elseif($player == "vlc") {

// Extensão: .m3u

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.m3u?sid=".$ponto."");
exit();

} elseif($player == "itunes") {

// Extensão: .pls

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.pls?sid=".$ponto."");
exit();

} elseif($player == "realplayer") {

// Extensão: .rm

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.ram?sid=".$ponto."");
exit();

} elseif($player == "quicktime") {

// Extensão: .qtl

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.qtl?sid=".$ponto."");
exit();

} elseif($player == "iphone") {

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.m3u?sid=".$ponto."");
exit();

} elseif($player == "android") {

header("Location: rtsp://".dominio_servidor($dados_servidor_aacplus["nome"])."/".$dados_stm["porta"]."/".$dados_stm["porta"].".stream");
exit();

} else {

// Extensão: .pls

header("Location: http://".dominio_servidor($dados_servidor["nome"]).":".$dados_stm["porta"]."/listen.pls?sid=".$ponto."");
exit();

}


?> 