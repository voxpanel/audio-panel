<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

require_once("inc/protecao-admin.php");
require_once("inc/classe.mail.php");
require_once("inc/classe.ssh.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

// Proteção contra usuario não logados
if(empty($_SESSION["code_user_logged"])) {
die("<span class='texto_status_erro'>0x005 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["codigo_revenda"]) or empty($_POST["nome"]) or empty($_POST["email"]) or empty($_POST["streamings"]) or empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["espaco"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_revenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_POST["codigo_revenda"]."'"));

if($_POST["senha"]) {

mysql_query("Update revendas set id = '".$_POST["id"]."', nome = '".$_POST["nome"]."', email = '".$_POST["email"]."', senha = PASSWORD('".$_POST["senha"]."'), subrevendas = '".$_POST["subrevendas"]."', streamings = '".$_POST["streamings"]."', ouvintes = '".$_POST["ouvintes"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', aacplus = '".$_POST["aacplus"]."', dominio_padrao = '".$_POST["dominio_padrao"]."' where codigo = '".$_POST["codigo_revenda"]."'");

} else {

mysql_query("Update revendas set id = '".$_POST["id"]."', nome = '".$_POST["nome"]."', email = '".$_POST["email"]."', subrevendas = '".$_POST["subrevendas"]."', streamings = '".$_POST["streamings"]."', ouvintes = '".$_POST["ouvintes"]."', bitrate = '".$_POST["bitrate"]."', espaco = '".$_POST["espaco"]."', aacplus = '".$_POST["aacplus"]."', dominio_padrao = '".$_POST["dominio_padrao"]."' where codigo = '".$_POST["codigo_revenda"]."'");

}

if($_POST["aacplus"] == 'nao' && $dados_revenda_atual["aacplus"] == 'sim') {

$sql_stm = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda_atual["codigo"]."'");
while ($dados_stm = mysql_fetch_array($sql_stm)) {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$dados_stm["porta"]."");

mysql_query("Update streamings set aacplus = 'nao' where codigo = '".$dados_stm["codigo"]."'");

}

}

// Altera o bitrate dos streamings de acordo com o novo bitrate caso tenha sido alterado
if($dados_revenda_atual["bitrate"] != $_POST["bitrate"]) {

$sql_stm = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda_atual["codigo"]."'");
while ($dados_stm = mysql_fetch_array($sql_stm)) {

if($dados_stm["bitrate"] > $_POST["bitrate"]) {
mysql_query("Update streamings set bitrate = '".$_POST["bitrate"]."' where codigo = '".$dados_stm["codigo"]."'") or die(mysql_error());
}

}

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Configurações da revenda ".$_POST["nome"]." alteradas com sucesso.","ok");

header("Location: /admin/admin-revendas/resultado/".$_POST["id"]."");
?>