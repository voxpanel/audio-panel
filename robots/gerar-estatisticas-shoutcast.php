<?php
ini_set("max_execution_time", 3600);

require_once("/home/painel/public_html/admin/inc/conecta.php");
require_once("/home/painel/public_html/admin/inc/funcoes.php");

$inicio_execucao = tempo_execucao();

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

$sql = mysql_query("SELECT * FROM streamings where status = '1' ORDER by porta ASC LIMIT ".$inicial.", ".$final."");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_pontos > 0) {

$sql_pontos = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
while ($dados_ponto = mysql_fetch_array($sql_pontos)) {

$xml_stats = @simplexml_load_string(utf8_encode(estatistica_streaming_shoutcast_robot($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"],$dados_ponto["id"])));

$total_registros = count($xml_stats->LISTENERS->LISTENER);

for($i=0;$i<=$total_registros;$i++){

$ip = $xml_stats->LISTENERS->LISTENER[$i]->HOSTNAME;
$tempo_conectado = $xml_stats->LISTENERS->LISTENER[$i]->CONNECTTIME;
$player = formatar_useragent($xml_stats->LISTENERS->LISTENER[$i]->USERAGENT);
$pais = pais_ip($ip,"nome");

$verifica_ip_rtmp = mysql_num_rows(mysql_query("SELECT * FROM servidores where ip = '".$ip."' AND tipo = 'aacplus'"));

if($verifica_ip_rtmp == 0) {

if($ip && $tempo_conectado) {

$verifica_ouvinte = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip."' AND data = '".date("Y-m-d")."'"));

if($verifica_ouvinte == 0) {

mysql_query("INSERT INTO estatisticas (codigo_stm,data,hora,ip,pais,tempo_conectado,player) VALUES ('".$dados_stm["codigo"]."',NOW(),NOW(),'".$ip."','".$pais."','".$tempo_conectado."','".$player."')") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["porta"]."][".$dados_ponto["id"]."][Shoutcast] Ouvinte: ".$ip." adicionado.\n";

} else {

mysql_query("Update estatisticas set tempo_conectado = '".$tempo_conectado."', player = '".$player."' where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip."' AND data = '".date("Y-m-d")."'") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["porta"]."][".$dados_ponto["id"]."][Shoutcast] Ouvinte: ".$ip." atualizado.\n";

} // if $verifica_ouvinte

} // if $ip && $tempo_conectado

} // if $verifica_ip_rtmp

} // for

} // while pontos

} else { // ponto

$xml_stats = @simplexml_load_string(utf8_encode(estatistica_streaming_shoutcast_robot($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"],1)));

$total_registros = count($xml_stats->LISTENERS->LISTENER);

for($i=0;$i<=$total_registros;$i++){

$ip = $xml_stats->LISTENERS->LISTENER[$i]->HOSTNAME;
$tempo_conectado = $xml_stats->LISTENERS->LISTENER[$i]->CONNECTTIME;
$player = formatar_useragent($xml_stats->LISTENERS->LISTENER[$i]->USERAGENT);
$pais = pais_ip($ip,"nome");

$verifica_ip_rtmp = mysql_num_rows(mysql_query("SELECT * FROM servidores where ip = '".$ip."' AND tipo = 'aacplus'"));

if($verifica_ip_rtmp == 0) {

if($ip && $tempo_conectado) {

$verifica_ouvinte = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip."' AND data = '".date("Y-m-d")."'"));

if($verifica_ouvinte == 0) {

mysql_query("INSERT INTO estatisticas (codigo_stm,data,hora,ip,pais,tempo_conectado,player) VALUES ('".$dados_stm["codigo"]."',NOW(),NOW(),'".$ip."','".$pais."','".$tempo_conectado."','".$player."')") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["porta"]."][Shoutcast] Ouvinte: ".$ip." adicionado.\n";

} else {

mysql_query("Update estatisticas set tempo_conectado = '".$tempo_conectado."', player = '".$player."' where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip."' AND data = '".date("Y-m-d")."'") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["porta"]."][Shoutcast] Ouvinte: ".$ip." atualizado.\n";

} // if $verifica_ouvinte

} // if $ip && $tempo_conectado

} // if $verifica_ip_rtmp

} // for

} // ponto

} // status servidor

} // while streamings

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo: ".$tempo_execucao." segundo(s);\n\n";
?>