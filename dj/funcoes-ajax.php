<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("../admin/inc/classe.ssh.php");
require_once("../admin/inc/classe.ftp.php");
require_once("../admin/inc/classe.mail.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('2');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para remover um pedido de musica
if($acao == "remover_pedido_musical") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('3'),"D");

	mysql_query("Delete FROM pedidos_musicais where codigo = '".$codigo."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_pedidos_musicais_resultado_remover_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_pedidos_musicais_resultado_remover_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}
?>