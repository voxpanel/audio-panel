<?php
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where ip = '".query_string('2')."'"));

$sql = mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."' ORDER by porta ASC");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));

echo $dados_stm["porta"]."\n";

}

echo "playlists";

?>