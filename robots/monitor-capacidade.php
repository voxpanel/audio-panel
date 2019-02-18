<?php
require_once("/home/painel/public_html/admin/inc/conecta.php");

// Verifica se ha espaчo nos servidores, se nуo houver notifica o admin
$total_srv_disponivel = 0;

$query1 = mysql_query("SELECT * FROM servidores WHERE tipo = 'streaming'");
while ($dados_srv = mysql_fetch_array($query1)) {

$total_stm_srv = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_srv["codigo"]."'"));

if($total_stm_srv < $dados_srv["limite_streamings"]) {
$total_srv_disponivel++;
}

}

if($total_srv_disponivel == 0) {

$mensagem = 'Atenчуo! Atenчуo! Atenчуo!\n\nCapacidade dos servidores excedida!!!\n\nData: '.date("d/m/Y H:i:s").'\n\n';

mail('contato@site.com.br','[STREAMING-AUDIO] Alerta de capacidade excedida!',$mensagem);

}

// Atualiza servidor atual
$query = mysql_query("SELECT * FROM servidores WHERE tipo = 'streaming' AND status = 'on' ORDER by RAND() LIMIT 1");
while ($dados_servidor = mysql_fetch_array($query)) {

$total_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

if($total_stm < $dados_servidor["limite_streamings"]) {

mysql_query("Update configuracoes set codigo_servidor_atual = '".$dados_servidor["codigo"]."'");

}

}

?>