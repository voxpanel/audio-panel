<?php
// conexao banco de dados
$host = "localhost";//nome do host
$user = "painel";//nome de usuario do mysql
$pass = "Ira@1q2w3e4r"; //senha do mysql
$bd_streaming = "audioaju"; //nome do banco de dados

$conexao = mysql_connect($host,$user,$pass) or die(mysql_error());

mysql_select_db($bd_streaming,$conexao) or die(mysql_error());
?>
