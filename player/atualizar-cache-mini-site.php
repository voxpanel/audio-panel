<?php
$porta = query_string('1');

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));

$dominio = $dados_stm["mini_site_dominio"];
$cor_fundo = $dados_stm["mini_site_cor_fundo"];
$cor_topo = $dados_stm["mini_site_cor_topo"];
$cor_texto_topo = $dados_stm["mini_site_cor_texto_topo"];
$cor_texto_padrao = $dados_stm["mini_site_cor_texto_padrao"];
$cor_texto_rodape = $dados_stm["mini_site_cor_texto_rodape"];
$mini_site_chat = $dados_stm["mini_site_exibir_chat"];
$mini_site_xat_id = $dados_stm["mini_site_exibir_xat_id"];
$url_facebook = $dados_stm["mini_site_url_facebook"];
$url_twitter = $dados_stm["mini_site_url_twitter"];

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/site-".$porta.".txt","".$dominio."|".$cor_fundo."|".$cor_topo."|".$cor_texto_topo."|".$cor_texto_padrao."|".$cor_texto_rodape."|".$mini_site_chat."|".$mini_site_xat_id."|".$url_facebook."|".$url_twitter."");

echo "OK";
?>