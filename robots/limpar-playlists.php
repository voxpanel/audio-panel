<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 3600);

require_once("../admin/inc/conecta.php");

$query_playlists = mysql_query("SELECT * FROM playlists");
while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
$verifica_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo = '".$dados_playlist["codigo_stm"]."'"));

if($verifica_stm == 0) {
mysql_query("Delete From playlists where codigo = '".$dados_playlist["codigo"]."'");
mysql_query("Delete From playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");

}

}

$query_playlists_musica = mysql_query("SELECT * FROM playlists_musicas");
while ($dados_playlist_musica = mysql_fetch_array($query_playlists_musica)) {

$verifica_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo = '".$dados_playlist_musica["codigo_playlist"]."'"));

if($verifica_playlist == 0) {
mysql_query("Delete From playlists_musicas where codigo = '".$dados_playlist_musica["codigo"]."'");
}

}

echo "[".date("d/m/Y H:i:s")."] Processo Concluído."
?>