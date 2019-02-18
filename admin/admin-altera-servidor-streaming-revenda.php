<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["codigo_cliente"]) or empty($_POST["servidor_novo"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

foreach($_POST["servidor_novo"] as $porta => $servidor){

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$servidor."'"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings WHERE porta = '".$porta."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_POST["codigo_cliente"]."'"));

mysql_query("UPDATE streamings set codigo_servidor = '".$dados_servidor["codigo"]."' WHERE porta = '".$dados_stm["porta"]."'");

if($dados_stm["aacplus"] == "sim") {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/sincronizar-aacplus ".$dados_stm["porta"]." ".$dados_servidor["ip"]." ".$dados_stm["ouvintes"]."");

}

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Servidor dos streamings da revenda ".$dados_revenda["nome"]." alterado com sucesso para ".$dados_servidor["nome"]."","ok");

header("Location: /admin/admin-revendas/resultado/".$dados_revenda["nome"]."");
?>