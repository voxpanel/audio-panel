<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 3600);

require_once("../admin/inc/conecta.php");

$query_stats = mysql_query("SELECT * FROM djs");
while ($dados_dj = mysql_fetch_array($query_stats)) {

$verifica_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo = '".$dados_dj["codigo_stm"]."'"));

if($verifica_stm == 0) {
mysql_query("Delete From djs where codigo = '".$dados_dj["codigo"]."'");
}

}

echo "[".date("d/m/Y H:i:s")."] Processo Concluído."
?>