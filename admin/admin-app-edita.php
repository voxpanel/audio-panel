<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".$_POST["codigo"]."'"));

if(isset($_POST["information"])) {

file_put_contents("../app_android/apps/".$dados_app["hash"]."/src/com/shoutcast/radio/".nome_app_play($dados_app["radio_nome"])."/data/information.java",$_POST["information"]);

}

if(isset($_POST["strings"])) {

file_put_contents("../app_android/apps/".$dados_app["hash"]."/res/values/strings.xml",$_POST["strings"]);

}

if(isset($_POST["manifest"])) {

file_put_contents("../app_android/apps/".$dados_app["hash"]."/AndroidManifest.xml",$_POST["manifest"]);

}

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Informa��es do App editadas com sucesso.","ok");

header("Location: /admin/admin-app-detalhes/".code_decode($dados_app["codigo"],"E")."");

?>