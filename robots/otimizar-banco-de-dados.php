<?php
ini_set("memory_limit", "256M");
ini_set("max_execution_time", 3600);

require_once("/home/painel/public_html/admin/inc/conecta.php");

echo "[".date("d/m/Y H:i:s")."] Processo iniciado.<br>";

mysql_query("OPTIMIZE TABLE  `administradores` ,  `avisos` ,  `avisos_desativados` ,  `configuracoes` ,  `dicas_rapidas` ,  `dicas_rapidas_acessos` ,  `djs` ,  `dominios_bloqueados` ,  `estatisticas` ,  `logs` ,  `logs_migracoes` ,  `migracoes` ,  `playlists` ,  `playlists_agendamentos` , `playlists_agendamentos_logs` ,  `playlists_musicas` ,  `revendas` ,  `servidores` ,  `streamings`") or die("Erro ao executar otimização:<br> ".mysql_error()."");

echo "[".date("d/m/Y H:i:s")."] Processo concluído.";
?>