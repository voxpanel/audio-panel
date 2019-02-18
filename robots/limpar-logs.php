<?php
ini_set("max_execution_time", 3600);

require_once("/home/painel/public_html/admin/inc/conecta.php");

$data1 = date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")-45, date("Y")));
$data2 = date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")-45, date("Y")));
$data3 = date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")-30, date("Y")));

mysql_query("Delete From logs WHERE data < '".$data1."'");

mysql_query("Delete From logs_streamings WHERE data < '".$data2."'");

mysql_query("Delete From logs_migracoes WHERE data < '".$data2."'");

mysql_query("Delete From playlists_agendamentos_logs WHERE data < '".$data3."'");

?>