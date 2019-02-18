<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".code_decode(query_string('2'),"D")."'"));

$nome_apk = nome_app_apk($dados_app["radio_nome"]);

// Compila o App
$resultado = shell_exec("cd /home/painel/public_html/app_android/apps/".$dados_app["hash"].";/opt/ant/bin/ant release 2>&1");

if(preg_match('/BUILD SUCCESSFUL/i',$resultado)) {

copy("../app_android/apps/".$dados_app["hash"]."/bin/".$nome_apk."-release.apk","../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/App-".$nome_apk.".apk");

// Cria o zip com o conteudo para publicação no google play
$zip = new ZipArchive();
if ($zip->open("../app_android/apps/".$dados_app["hash"].".zip", ZIPARCHIVE::CREATE)!==TRUE) {
    die("Não foi possível criar o arquivo ZIP: ".$dados_app["hash"].".zip");
}

$zip->addEmptyDir("".$dados_app["hash"]."");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/App-".$nome_apk.".apk","".$dados_app["hash"]."/App-".$nome_apk.".apk");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/img-play-icone.png","".$dados_app["hash"]."/img-play-icone.png");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/img-play-destaque.jpg","".$dados_app["hash"]."/img-play-destaque.jpg");
$zip->addFile("../app_android/apps/".$dados_app["hash"]."/arquivos_google_play/img-play-app.png","".$dados_app["hash"]."/img-play-app.png");
$status=$zip->getStatusString();
$zip->close();

if(!file_exists("../app_android/apps/".$dados_app["hash"].".zip")) {
shell_exec("cd ../app_android/apps/;/usr/bin/zip -1 ".$dados_app["hash"].".zip ".$dados_app["hash"].";/usr/bin/zip -1 ".$dados_app["hash"].".zip ".$dados_app["hash"]."/arquivos_google_play/*");
}

mysql_query("Update apps set apk = 'App-".$nome_apk.".apk', compilado = 'sim', zip = '".$dados_app["hash"].".zip' where codigo = '".$dados_app["codigo"]."'") or die(mysql_error());

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("App compilado com sucesso!","ok");

} else {

$resultado_build .= "Path: cd /home/painel/public_html/app_android/apps/".$dados_app["hash"]."\n";
$resultado_build .= "Cmd: /opt/ant/bin/ant release\n";
$resultado_build .= $resultado;

mysql_query("Update apps set log_build = '".addslashes($resultado_build)."' where codigo = '".$dados_app["codigo"]."'");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Não foi possível compilar o App! Verifique o log.","erro");
}

header("Location: /admin/admin-app-detalhes/".query_string('2')."");

?>