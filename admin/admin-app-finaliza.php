<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".$_POST["codigo"]."'"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));

mysql_query("Update apps set aviso = '".$_POST["aviso"]."', status = '".$_POST["status"]."', package = '".$_POST["package"]."' where codigo = '".$_POST["codigo"]."'") or die(mysql_error());

// Remove source do App compilado
if($dados_app["hash"] != "") {
remover_source_app("../app_android/apps/".$dados_app["hash"]."");
}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Solicitação de App do streaming ".$dados_stm["porta"]." concluída com sucesso.","ok");

header("Location: /admin/admin-apps");

?>