<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 3600);

require_once("../admin/inc/conecta.php");

$query = mysql_query("SELECT * FROM playlists_agendamentos");
while ($dados_agendamento = mysql_fetch_array($query)) {

$verifica_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo = '".$dados_agendamento["codigo_stm"]."'"));
$verifica_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo = '".$dados_agendamento["codigo_playlist"]."'"));

if($verifica_stm == 0) {
mysql_query("Delete From playlists_agendamentos where codigo = '".$dados_agendamento["codigo"]."'");
}

if($verifica_playlist == 0) {
mysql_query("Delete From playlists_agendamentos where codigo = '".$dados_agendamento["codigo"]."'");
}

}

mysql_query("Delete From playlists_agendamentos where frequencia = '1' AND data < NOW()");

echo "[".date("d/m/Y H:i:s")."] Processo Concluído."
?>