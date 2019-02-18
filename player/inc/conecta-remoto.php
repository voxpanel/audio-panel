<?php
// conexao banco de dados
$host = "";//nome do host
$user = "";//nome de usuario do mysql
$pass = ""; //senha do mysql
$bd_streaming = ""; //nome do banco de dados

$conexao = @mysql_connect($host,$user,$pass);

@mysql_select_db($bd_streaming,$conexao);
?>
