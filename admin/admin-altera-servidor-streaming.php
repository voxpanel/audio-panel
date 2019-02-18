<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["servidor_atual"]) or empty($_POST["servidor_novo"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$dados_servidor_atual = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$_POST["servidor_atual"]."'"));
$dados_servidor_novo = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$_POST["servidor_novo"]."'"));

$sql = mysql_query("SELECT * FROM streamings where codigo_servidor = '".$_POST["servidor_atual"]."'");
while ($dados_stm = mysql_fetch_array($sql)) {

mysql_query("UPDATE streamings set codigo_servidor = '".$dados_servidor_novo["codigo"]."' WHERE codigo = '".$dados_stm["codigo"]."'");

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Servidor alterado com sucesso para ".$dados_servidor_novo["nome"]."","ok");

header("Location: /admin/admin-streamings/resultado-servidor/".code_decode($_POST["servidor_atual"],"E")."");
?>