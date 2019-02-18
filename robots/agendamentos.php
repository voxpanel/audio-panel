<?php
ini_set("memory_limit", "256M");
ini_set("max_execution_time", 3600);

require_once("/home/painel/public_html/admin/inc/conecta.php");
require_once("/home/painel/public_html/admin/inc/funcoes.php");
require_once("/home/painel/public_html/funcoes-ajax.php");

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

echo "[".date("d/m/Y H:i:s")."] Processo Iniciado.\n";

$hora_atual_servidor = date("H:i");

$query1 = mysql_query("SELECT * FROM playlists_agendamentos ORDER by codigo ASC LIMIT ".$inicial.", ".$final."");
while ($dados_agendamento = mysql_fetch_array($query1)) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_agendamento["codigo_stm"]."'"));
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_agendamento["codigo_playlist"]."'"));

$hora_inicio = $dados_agendamento["hora"].":".$dados_agendamento["minuto"];
$hora_atual = formatar_data("H:i", $hora_atual_servidor, $dados_stm["timezone"]);
$data_atual = date("Y-m-d");

if($dados_stm["status"] == 1) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

//////////////////////////////////////////////////////////////
//// Frequкncia 1 -> Executar em data especнfica(uma vez) ////
//////////////////////////////////////////////////////////////

if($dados_agendamento["frequencia"] == 1) {

// Verifica se a data especнfica й hoje e se esta na hora de iniciar
if($dados_agendamento["data"] == $data_atual && $hora_inicio == $hora_atual) {

echo "[0x01][".$dados_stm["porta"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_inicio."] Iniciando playlist ".$dados_playlist["nome"]." em ".$dados_agendamento["data"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Copia a playlist a ser iniciada para a playlist atual e recarrega a playlist no AutoDJ
$ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls;/home/streaming/recarregar_playlist_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");

// Atualiza a ъltima playlist tocada
mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");

// Loga a aзгo executada
mysql_query("INSERT INTO playlists_agendamentos_logs (codigo_agendamento,codigo_stm,data,playlist) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'".$dados_playlist["nome"]."')");

// Remove o agendamento
mysql_query("Delete From playlists_agendamentos where codigo = '".$dados_agendamento["codigo"]."'");

} // FIM -> Verifica se esta na hora de iniciar / Frequкncia 1

} elseif($dados_agendamento["frequencia"] == 2) { // Else -> frequencia 2

//////////////////////////////////////////////
//// Frequкncia 2 -> Executar Diariamente ////
//////////////////////////////////////////////

// Verifica se esta na hora de iniciar
if($hora_inicio == $hora_atual) { 

echo "[0x02][".$dados_stm["porta"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_inicio."] Iniciando playlist ".$dados_playlist["nome"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Copia a playlist a ser iniciada para a playlist atual e recarrega a playlist no AutoDJ
$ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls;/home/streaming/recarregar_playlist_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");

// Atualiza a ъltima playlist tocada
mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");

// Loga a aзгo executada
mysql_query("INSERT INTO playlists_agendamentos_logs (codigo_agendamento,codigo_stm,data,playlist) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'".$dados_playlist["nome"]."')");

} // FIM -> Verifica se esta na hora de iniciar


} else { // Else -> frequencia 3

///////////////////////////////////////////////
/// Frequкncia 3 -> Executar Dias da Semana ///
///////////////////////////////////////////////

$dia_semana = date("N");
$array_dias = explode(",",substr($dados_agendamento["dias"], 0, -1));

// Verifica se esta na hora de iniciar
if(in_array($dia_semana, $array_dias) === true && $hora_inicio == $hora_atual) { 

echo "[0x03][".$dados_stm["porta"]."][".date_default_timezone_get()."][".$dados_stm["timezone"]."][".$hora_atual_servidor."][".$hora_inicio."] Iniciando playlist ".$dados_playlist["nome"]." as ".$hora_inicio."\n";

// Conexгo SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Copia a playlist a ser iniciada para a playlist atual e recarrega a playlist no AutoDJ
$ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls;/home/streaming/recarregar_playlist_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");

// Atualiza a ъltima playlist tocada
mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");

// Loga a aзгo executada
mysql_query("INSERT INTO playlists_agendamentos_logs (codigo_agendamento,codigo_stm,data,playlist) VALUES ('".$dados_agendamento["codigo"]."','".$dados_stm["codigo"]."',NOW(),'".$dados_playlist["nome"]."')");

} // FIM -> Verifica se o dia da semana й o atual e se esta na hora de iniciar

} // FIM -> frequencia

} // FIM -> Verifica se o servidor esta ON/OFF

} // FIM -> Verifica se o streaming esta ON/OFF

} // FIM -> while

echo "\n[".date("d/m/Y H:i:s")."] Processo Concluнdo.\n\n";

?>