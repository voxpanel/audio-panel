<?php
$porta = query_string('1');

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$servidor = $dados_servidor["ip"];
$servidor_rtmp = $dados_servidor_aacplus["ip"];
$autoplay = $dados_stm["player_autoplay"];
$autodj_prog_aovivo = $dados_stm["autodj_prog_aovivo"];
$autodj_prog_aovivo_msg = $dados_stm["autodj_prog_aovivo_msg"];
$volume_inicial = $dados_stm["player_volume_inicial"];
$aacplus = $dados_stm["aacplus"];
$chat = $dados_stm["player_exibir_chat"];
$pedidos_musicais = $dados_stm["player_exibir_pedido_musical"];

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/".$porta.".txt","".$servidor."|".$servidor_rtmp."|".$autoplay."|".$autodj_prog_aovivo."|".$autodj_prog_aovivo_msg."|".$volume_inicial."|".$aacplus."|".$chat."|".$pedidos_musicais."");

echo "OK";
?>