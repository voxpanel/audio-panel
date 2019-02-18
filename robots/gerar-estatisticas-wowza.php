<?php
ini_set("max_execution_time", 3600);

require_once("/home/painel/public_html/admin/inc/conecta.php");
require_once("/home/painel/public_html/admin/inc/funcoes.php");

$inicio_execucao = tempo_execucao();

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

echo "\n\n--------------------------------------------------------------------\n\n";

// Grava cache com o XML do wowza de todos os servidores RTMP
$sql_servidores = mysql_query("SELECT * FROM servidores where status = 'on' AND tipo = 'aacplus' ORDER by ordem ASC");
while ($dados_servidor_aacplus = mysql_fetch_array($sql_servidores)) {

$xml_wowza = @simplexml_load_string(utf8_encode(estatistica_streaming_aacplus_robot($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["senha"])));

$array_xml["stats"][$dados_servidor_aacplus["codigo"]] = $xml_wowza;


echo "Servidor Wowza: ".$dados_servidor_aacplus["nome"]."\n";

}

echo "\n--------------------------------------------------------------------\n\n";

// Gera as estatisticas
$sql = mysql_query("SELECT * FROM streamings where status = '1' AND aacplus = 'sim' ORDER by porta ASC LIMIT ".$inicial.", ".$final."");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

if($dados_servidor["status"] == "on") {

if($dados_servidor_aacplus["status"] == "on") {

$xml_stats_wowza = $array_xml["stats"][$dados_servidor_aacplus["codigo"]];

$total_registros_wowza = count($xml_stats_wowza->VHost->Application);

if($total_registros_wowza > 0) {

for($i=0;$i<$total_registros_wowza;$i++){

if($xml_stats_wowza->VHost->Application[$i]->Name == $dados_stm["porta"]) {

$total_ouvintes_wowza = count($xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client);

for($ii=0;$ii<$total_ouvintes_wowza;$ii++){

$ip_wowza = $xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;
$tempo_conectado_wowza = $xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->TimeRunning;
$player = formatar_useragent($xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->FlashVersion);
$pais_wowza = pais_ip($ip_wowza,"nome");


$verifica_ouvinte_wowza = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip_wowza."' AND data = '".date("Y-m-d")."'"));

if($verifica_ouvinte_wowza == 0) {

mysql_query("INSERT INTO estatisticas (codigo_stm,data,hora,ip,pais,tempo_conectado,player) VALUES ('".$dados_stm["codigo"]."',NOW(),NOW(),'".$ip_wowza."','".$pais_wowza."','".$tempo_conectado_wowza."','".$player."')") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["porta"]."][Wowza] Ouvinte: ".$ip_wowza." adicionado.\n";

} else {

mysql_query("Update estatisticas set tempo_conectado = '".$tempo_conectado_wowza."', player = '".$player."' where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip_wowza."' AND data = '".date("Y-m-d")."'") or die("Erro MySQL: ".mysql_error());

echo "[".$dados_stm["porta"]."][Wowza] Ouvinte: ".$ip_wowza." atualizado.\n";

}

}

break;

}

}

}

}

}

}

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo: ".$tempo_execucao." segundo(s);\n\n";
?>