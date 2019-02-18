<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("inc/protecao-revenda.php");
require_once("inc/classe.ssh.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

// Proteção contra usuario não logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["porta"]) or empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["senha"])) {
die ("<script> alert(\"".lang_info_campos_vazios."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm_atual = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_POST["porta"]."'"));

// Verifica os limites da revenda
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
$dados_subrevenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode($_POST["codigo_subrevenda"],"D")."') AND tipo = '2'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$ouvintes_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

// Verifica se excedeu o limite de ouvintes do cliente
$total_ouvintes_revenda = $ouvintes_revenda["total"]+$ouvintes_subrevenda_revenda["total"];
$total_ouvintes_revenda = $total_ouvintes_revenda+$_POST["ouvintes"];
$total_ouvintes_revenda = $total_ouvintes_revenda-$dados_stm_atual["ouvintes"];

if($total_ouvintes_revenda > $dados_revenda["ouvintes"] && $dados_revenda["ouvintes"] != 999999) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_ouvintes."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de ouvintes do cliente
$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"];
$total_espaco_revenda = $total_espaco_revenda+$_POST["espaco"];
$total_espaco_revenda = $total_espaco_revenda-$dados_stm_atual["espaco"];

if($total_espaco_revenda > $dados_revenda["espaco"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_espaco_autodj."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de bitrate do cliente
if($_POST["bitrate"] > $dados_revenda["bitrate"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_bitrate."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

if($_POST["aacplus"] == 'sim' && $_POST["encoder_aacplus"] == 'sim') {

$encoder = "aacp";
$aacplus = "sim";
$servidor_aacplus = ($dados_stm_atual["codigo_servidor_aacplus"] == '0') ? $dados_config["codigo_servidor_aacplus_atual"] : $dados_stm_atual["codigo_servidor_aacplus"];

} else {

$encoder = "mp3";
$aacplus = "nao";
$servidor_aacplus = 0;

}

$encoder_mp3 = ($_POST["encoder_mp3"] == "sim") ? $_POST["encoder_mp3"] : "nao";
$encoder_aacplus = ($_POST["encoder_aacplus"]) ? $_POST["encoder_aacplus"] : "nao";


if($_POST["senha"] == $_POST["senha_admin"]) {
die ("<script> alert(\"".lang_info_config_stm_senha_admin_info_ajuda."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("Update streamings set codigo_servidor_aacplus = '".$servidor_aacplus."', ouvintes = '".$_POST["ouvintes"]."', bitrate = '".$_POST["bitrate"]."', encoder_mp3 = '".$encoder_mp3."', encoder_aacplus = '".$encoder_aacplus."', encoder = '".$encoder."', espaco = '".$_POST["espaco"]."', senha = '".$_POST["senha"]."', senha_admin = '".$_POST["senha_admin"]."', encoder = '".$encoder."', identificacao = '".$_POST["identificacao"]."', aacplus = '".$aacplus."', idioma_painel = '".$_POST["idioma_painel"]."', email = '".$_POST["email"]."', exibir_app_android = '".$_POST["exibir_app_android"]."', exibir_mini_site = '".$_POST["exibir_mini_site"]."', permitir_alterar_senha = '".$_POST["permitir_alterar_senha"]."', autodj = '".$_POST["autodj"]."' where porta = '".$_POST["porta"]."'") or die(mysql_error());

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm_atual["codigo_servidor"]."'"));

// Ativa/Desativa o relay no servidor aacplus
if($dados_stm_atual["aacplus"] != $_POST["aacplus"]) {

if($_POST["aacplus"] == 'sim' && $_POST["encoder_aacplus"] == 'sim') {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$servidor_aacplus."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/ativar-aacplus ".$_POST["porta"]." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");

} else {

if($dados_stm_atual["codigo_servidor_aacplus"] != 0) {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm_atual["codigo_servidor_aacplus"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$_POST["porta"]."");

}

}

}

// Atualiza o limite de ouvintes no relay no servidor aacplus
if($dados_stm_atual["ouvintes"] != $_POST["ouvintes"]) {

if($_POST["aacplus"] == 'sim' && $_POST["encoder_aacplus"] == 'sim') {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$servidor_aacplus."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$_POST["porta"]."");
$ssh->executar("/usr/local/WowzaMediaServer/ativar-aacplus ".$_POST["porta"]." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");

}

}

// Loga a ação executada
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('alterar_configuracoes_streaming',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração nas configurações do streaming ".$dados_stm["porta"]." pela revenda.')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao(sprintf(lang_info_pagina_configurar_streaming_resultado_ok,$_POST["porta"]),"ok");
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_configurar_streaming_resultado_alerta,"alerta");

//header("Location: /admin/revenda-streaming-informacoes/".code_decode($_POST["porta"],"E")."");

echo '<script type="text/javascript">top.location = "/admin/revenda/'.code_decode($_POST["porta"],"E").'"</script>';
?>