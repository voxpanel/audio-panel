<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}

$sql = mysql_query("SELECT * FROM apps WHERE compilado = 'nao' AND status = '0'");
while ($dados_app = mysql_fetch_array($sql)) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));

$nome_apk = nome_app_apk($dados_app["radio_nome"]);

// Compila o App
$resultado = shell_exec("cd /home/painel/public_html/app_android/apps/".$dados_app["hash"].";/opt/ant/bin/ant release 2>&1");

if(preg_match('/BUILD SUCCESSFUL/i',$resultado)) {

if($dados_app["play"] == "sim") {

copy("../app_android/apps/".$dados_app["hash"]."/bin/".$nome_apk."-release.apk","../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/".$nome_apk.".apk");

// Cria o zip com o conteudo para publica��o no google play
$zip = new ZipArchive();
if ($zip->open("../app_android/apps/".$dados_app["hash"].".zip", ZIPARCHIVE::CREATE)!==TRUE) {
    die("N�o foi poss�vel criar o arquivo ZIP: ".$dados_app["hash"].".zip");
}

$zip->addEmptyDir("".$dados_app["hash"]."");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/".$nome_apk.".apk","".$dados_app["hash"]."/".$nome_apk.".apk");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/img-play-icone.png","".$dados_app["hash"]."/img-play-icone.png");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/img-play-destaque.jpg","".$dados_app["hash"]."/img-play-destaque.jpg");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/img-play-app.png","".$dados_app["hash"]."/img-play-app.png");
$status=$zip->getStatusString();
$zip->close();

if(!file_exists("../app_android/apps/".$dados_app["hash"].".zip")) {
shell_exec("cd ../app_android/apps/;/usr/bin/zip -1 ".$dados_app["hash"].".zip ".$dados_app["hash"].";/usr/bin/zip -1 ".$dados_app["hash"].".zip ".$dados_app["hash"]."/arquivos_google_play/*");
}

} else {
copy("../app_android/apps/".$dados_app["hash"]."/bin/".$nome_apk."-release.apk","../app_android/apps/".$nome_apk.".apk");
}

mysql_query("Update apps set apk = '".$nome_apk.".apk', compilado = 'sim', zip = '".$dados_app["hash"].".zip' where codigo = '".$dados_app["codigo"]."'") or die(mysql_error());

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("App ".$nome_apk."-release.apk da porta ".$dados_stm["porta"]."compilado com sucesso!","ok");

} else {

$resultado_build .= "Path: cd /home/painel/public_html/app_android/apps/".$dados_app["hash"]."\n";
$resultado_build .= "Cmd: /opt/ant/bin/ant release\n";
$resultado_build .= $resultado;

mysql_query("Update apps set log_build = '".addslashes($resultado_build)."' where codigo = '".$dados_app["codigo"]."'");

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("N�o foi poss�vel compilar o Appd da porta ".$dados_stm["porta"]."","erro");
}

}

header("Location: /admin/admin-apps");

?>