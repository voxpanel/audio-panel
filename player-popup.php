<?php
$porta = query_string('1');

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$dominio = (!empty($dados_revenda["dominio_padrao"])) ? $dados_revenda["dominio_padrao"] : "srvstm.com";
$modelo = (query_string('2') != "") ? query_string('2') : "1";

header("Location: http://player.".$dominio."/player-popup/".$porta."/".$modelo."");
exit();
?>