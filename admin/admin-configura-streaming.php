<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

require_once("inc/protecao-admin.php");
require_once("inc/classe.ssh.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

// Prote��o contra usuario n�o logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

if(empty($_POST["porta"]) or empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["senha"])) {
die ("<script> alert(\"Voc� deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm_atual = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_POST["porta"]."'"));

if($_POST["aacplus"] == 'sim' && $_POST["servidor_aacplus"] == 0) {
die ("<script> alert(\"Voc� n�o selecionou o servidor AAC+\\n \\nPor favor volte e selecione.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Desativa o relay no servidor aacplus antigo
if($dados_stm_atual["codigo_servidor_aacplus"] != $_POST["servidor_aacplus"]) {

$dados_servidor_aacplus_antigo = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm_atual["codigo_servidor_aacplus"]."'"));

// Conex�o SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus_antigo["ip"],$dados_servidor_aacplus_antigo["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus_antigo["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$_POST["porta"]."");

}

mysql_query("Update streamings set codigo_cliente = '".$_POST["codigo_cliente"]."', codigo_servidor = '".$_POST["servidor"]."', codigo_servidor_aacplus = '".$_POST["servidor_aacplus"]."', ouvintes = '".$_POST["ouvintes"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', senha = '".$_POST["senha"]."', senha_admin = '".$_POST["senha_admin"]."', encoder = '".$_POST["encoder"]."', aacplus = '".$_POST["aacplus"]."', email = '".$_POST["email"]."', autodj = '".$_POST["autodj"]."', programetes = '".$_POST["programetes"]."' where porta = '".$_POST["porta"]."'");

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$_POST["servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$_POST["servidor_aacplus"]."'"));

// Ativa o relay no servidor aacplus
if($dados_stm_atual["aacplus"] != $_POST["aacplus"]) {

if($_POST["aacplus"] == 'sim') {

// Conex�o SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/sincronizar-aacplus ".$_POST["porta"]." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");

} else {

// Conex�o SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$_POST["porta"]."");


}
}

// Atualiza o limite de ouvintes no relay no servidor aacplus
if($dados_stm_atual["ouvintes"] != $_POST["ouvintes"]) {

if($_POST["aacplus"] == 'sim') {

// Conex�o SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/sincronizar-aacplus ".$_POST["porta"]." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");

}

}

if($dados_stm_atual["codigo_servidor"] != $_POST["servidor"]) {

@file_get_contents("http://player.audiocast.ml/atualizar-cache-player/".$dados_stm_atual["porta"]."");
@file_get_contents("http://player.audiocast.ml/atualizar-cache-player/".$dados_stm_atual["porta"]."");
@file_get_contents("http://player.audiocast.ml/atualizar-cache-player/".$dados_stm_atual["porta"]."");

if($_POST["aacplus"] == 'sim') {

// Conex�o SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/sincronizar-aacplus ".$_POST["porta"]." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");

}


}

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("Configura��es do streaming ".$_POST["porta"]." alteradas com sucesso.","ok");
$_SESSION["status_acao"] .= status_acao("Agora voc� precisa desligar e ligar novamente o streaming para aplicar as altera��es.","alerta");

header("Location: /admin/admin-streamings/resultado/".$_POST["porta"]."");
?>