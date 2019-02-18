<?php
require_once("/home/painel/public_html/admin/inc/conecta.php");
require_once("/home/painel/public_html/admin/inc/classe.ssh.php");
require_once("/home/painel/public_html/admin/inc/funcoes.php");

$sql = mysql_query("SELECT * FROM servidores where status = 'on' ORDER by codigo ASC");
while ($dados_servidor = mysql_fetch_array($sql)) {

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$load = $ssh->executar("cat /proc/loadavg | awk {'print $1'}");
$trafego = $ssh->executar("vnstat -m | grep ".date("M")." | awk {'print $6\" \"$7'}");
$trafego_out = $ssh->executar("vnstat -tr | grep \"tx\" | awk {'print $2\" \"$3'}");

mysql_query("UPDATE `audioaju`.`servidores` SET `load` = '".$load."', `trafego` = '".str_replace("iB","b",$trafego)."', `trafego_out` = '".str_replace("it/s","/s",$trafego_out)."' WHERE `servidores`.`codigo` = '".$dados_servidor["codigo"]."'") or die(mysql_error());

}
?>