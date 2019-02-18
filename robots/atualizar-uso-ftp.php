<?php
ini_set("max_execution_time", 3600);

require_once("/home/painel/public_html/admin/inc/conecta.php");
require_once("/home/painel/public_html/admin/inc/funcoes.php");

$inicio_execucao = tempo_execucao();

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

$sql = mysql_query("SELECT * FROM streamings where status = '1' AND autodj = 'sim' ORDER by porta ASC LIMIT ".$inicial.", ".$final."");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($dados_servidor["status"] == "on") {

$resultado = @file_get_contents("http://".$dados_servidor["ip"].":555/uso-ftp.php?porta=".$dados_stm["porta"]."");

if($resultado === FALSE) {
sleep(2);
$resultado = @file_get_contents("http://".$dados_servidor["ip"].":555/uso-ftp.php?porta=".$dados_stm["porta"]."");
}

if($resultado) {

$tamanho = ($resultado > 0) ? tamanho($resultado) : '0 MB';

mysql_query("Update streamings set espaco_usado = '".$resultado."' where codigo = '".$dados_stm["codigo"]."'");
echo "[".$dados_stm["porta"]."] ".$tamanho."\n";
} else {
echo "[".$dados_stm["porta"]."] ERRO!\n";
}

} // FIM -> Status servidor ON/OFF

} // FIM -> While

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo de Execussão: ".$tempo_execucao." segundo(s);\n\n";
?>