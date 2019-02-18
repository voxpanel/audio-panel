<?php
require_once("/home/painel/public_html/admin/inc/conecta.php");
require_once("/home/painel/public_html/admin/inc/classe.ssh.php");
require_once("/home/painel/public_html/admin/inc/funcoes.php");

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

$sql = mysql_query("SELECT * FROM streamings where status = '1' AND (relay = 'sim' AND relay_monitorar = 'sim') ORDER by porta ASC LIMIT ".$inicial.", ".$final."");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

// Verifica se foi configurado o IP e porta do streaming remoto
if(!empty($dados_stm["relay_ip"]) && !empty($dados_stm["relay_porta"])) {

// Verifica o status do relay, se esta ligado
$status_conexao_relay = status_relay($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);

// Se relay não estiver ligado, então reinicia o streaming
if($status_conexao_relay != "ligado") {

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

// Desliga o Streaming
$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");

// Liga o streaming
$total_multipoint = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_multipoint > 0) {

$sql_multipoint = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
while ($dados_ponto = mysql_fetch_array($sql_multipoint)) {

$config_streaming_multipoint .= "streamid_".$dados_ponto["id"]."=".$dados_ponto["id"]."\n";
$config_streaming_multipoint .= "streampath_".$dados_ponto["id"]."=".$dados_ponto["ponto"]."\n";
$config_streaming_multipoint .= "streammaxuser_".$dados_ponto["id"]."=".$dados_ponto["ouvintes"]."\n\n";

}
}

$senha_admin = ($dados_stm["senha_admin"]) ? $dados_stm["senha_admin"] : microtime();

$config_streaming = ";DADOS GERAIS\n";

if($total_multipoint == 0) {
$config_streaming .= "streamid=1\n";
}

$config_streaming .= "portbase=".$dados_stm["porta"]."\n";
$config_streaming .= "maxuser=".$dados_stm["ouvintes"]."\n";
$config_streaming .= "adminpassword=".$senha_admin."\n";
$config_streaming .= "password=".$dados_stm["senha"]."\n";
$config_streaming .= "srcip=any\n";
$config_streaming .= "destip=any\n";
$config_streaming .= "yport=80\n";
$config_streaming .= "namelookups=0\n";
$config_streaming .= "publicserver=".$dados_stm["publicserver"]."\n";
$config_streaming .= "allowrelay=".$dados_stm["allowrelay"]."\n";
$config_streaming .= "allowpublicrelay=1\n";
$config_streaming .= "metainterval=32768\n";
$config_streaming .= ";LOGS\n";
$config_streaming .= "logfile=/home/streaming/logs/log-".$dados_stm["porta"].".log\n";
$config_streaming .= "banfile=/home/streaming/configs/".$dados_stm["porta"].".ban\n";
$config_streaming .= "flashpolicyfile=/home/streaming/configs/crossdomain.xml\n";
$config_streaming .= "w3cenable=0\n";
$config_streaming .= "logclients=0\n";
$config_streaming .= "showlastsongs=".$dados_stm["showlastsongs"]."\n";

if($dados_stm["relay"] == "sim") {
$config_streaming .= ";RELAY\n";
$config_streaming .= "streamrelayurl=http://".$dados_stm["relay_ip"].":".$dados_stm["relay_porta"]."\n";
$config_streaming .= "relayreconnecttime=1\n";
}

if($total_multipoint > 0) {
$config_streaming .= "\n;MULTI POINT\n";
$config_streaming .= $config_streaming_multipoint;
}

$config_streaming = gerar_conf_streaming($dados_stm["porta"],$config_streaming);

$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_streaming."","/home/streaming/configs/".$config_streaming."",0777);

unlink("/home/painel/public_html/temp/".$config_streaming."");

$ssh->executar("/home/streaming/ligar_streaming /home/streaming/configs/".$config_streaming."");

echo "[".$dados_stm["porta"]."][Relay] Streaming reiniciado.\n";

}

}

}
?>