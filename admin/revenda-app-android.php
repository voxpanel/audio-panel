<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

if(isset($_POST["enviar"])) {

if(!isset($_POST["porta"])) {
die ("<script> alert(\"Ooops!\\n\\nVocê não selecione a porta do streaming desejado.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_POST["porta"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_app_criado = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo_stm = '".$dados_stm["codigo"]."'"));

if(isset($dados_app_criado["codigo"]) && $dados_app_criado["status"] < 2) {
die ("<script> alert(\"Ooops!\\n\\nVocê já solicitou um app.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

// Remove a requisição atual
mysql_query("Delete From apps where codigo = '".$dados_app_criado["codigo"]."' AND status = '2'");

require_once("inc/wideimage/WideImage.php");

// Valida extensão
if($_FILES["logo"]["type"] != "image/png") {
die ("<script> alert(\"".lang_info_streaming_app_android_resultado_erro_formato_logo."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($_FILES["icone"]["type"] != "image/png") {
die ("<script> alert(\"".lang_info_streaming_app_android_resultado_erro_formato_logo."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if(strlen($_POST["radio_nome"]) > 30) {
die ("<script> alert(\"".lang_info_streaming_app_android_resultado_erro_max_caracter_nome_radio."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if(empty($_POST["radio_nome"])) {
die ("<script> alert(\"".lang_info_streaming_app_android_resultado_erro_campo_vazio_nome_radio."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

// Verifica se o primeiro caracter é numérico
if(preg_match('/^\d/',$_POST["radio_nome"])) {
die ("<script> alert(\"".lang_info_streaming_app_android_resultado_erro_nome_radio_numero."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

$radio_nome = $_POST["radio_nome"];

$ponto = ($_POST["ponto"]) ? $_POST["ponto"] : 1;

$source = "source";

$endereco_site = $_POST["radio_site"];

$hash = nome_app_play($radio_nome)."_".md5($radio_nome);
$package = "com.shoutcast.stm.".nome_app_play($radio_nome)."";
$package_path = str_replace(".","/",$package);

$verifica_package = mysql_num_rows(mysql_query("SELECT * FROM apps where package = '".$package."'"));

if($verifica_package > 0) {
die ("<script> alert(\"Ooops!\\n\\nO nome da rádio já esta em uso.\\n\\nCada App deve ter um nome próprio.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

$servidor_stm = strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];

$patch_dir_apps = "../app_android/apps";
$patch_app = "../app_android/apps/".$hash."";
$patch_tmp = "../app_android/apps/tmp";

@copy($_FILES["logo"]["tmp_name"],"".$patch_tmp."/logo_".$hash.".png");
@copy($_FILES["icone"]["tmp_name"],"".$patch_tmp."/icone_".$hash.".png");

// Valida a dimensão(largura x altura) das imagens
list($logo_width, $logo_height, $logo_type, $logo_attr) = getimagesize("".$patch_tmp."/logo_".$hash.".png");
list($icone_width, $icone_height, $icone_type, $icone_attr) = getimagesize("".$patch_tmp."/icone_".$hash.".png");

if($logo_width != 300 || $logo_height != 300) {
die ("<script> alert(\"Ooops!\\n\\nA logomarca esta com dimensão inválida!\\n\\nEnvie uma logomarca com 235 pixels de largura e 235 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

if($icone_width != 144 || $icone_height != 144) {
die ("<script> alert(\"Ooops!\\n\\nO ícone esta com dimensão inválida!\\n\\nEnvie um ícone com 144 pixels de largura e 144 pixels de altura.\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

// Copia o source do app para o novo app
copiar_source("../app_android/".$source."/", $patch_app);

// Muda nome do package do source para o nome do package da radio
rename("".$patch_app."/src/com/shoutcast/stm/radio_nome","".$patch_app."/src/com/shoutcast/stm/".nome_app_play($radio_nome)."");

// Copia o ícone
$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(144, 144);
$icone->saveToFile("".$patch_app."/res/drawable-xxhdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(96, 96);
$icone->saveToFile("".$patch_app."/res/drawable-xhdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(72, 72);
$icone->saveToFile("".$patch_app."/res/drawable-hdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(48, 48);
$icone->saveToFile("".$patch_app."/res/drawable-mdpi/ic_launcher.png");

$icone = WideImage::load("".$patch_tmp."/icone_".$hash.".png");
$icone = $icone->resize(36, 36);
$icone->saveToFile("".$patch_app."/res/drawable-ldpi/ic_launcher.png");

// Copia a logo
$logo = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$logo = $logo->resize(235, 235);
$logo->saveToFile("".$patch_app."/res/drawable-mdpi/radio_logo.png");

// Cria icone para o Play
$play_icone = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$play_icone = $play_icone->resize(512, 512);
$play_icone->saveToFile("".$patch_app."/arquivos_google_play/img-icone-play.png");

// Cria a imagem de destaque para o Play com a logo da radio
$destaque = WideImage::load("".$patch_app."/arquivos_google_play/img-play-destaque.jpg");
$logo_destaque = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$play_destaque = $destaque->merge($logo_destaque, 'center', 'center', 100);
$play_destaque->saveToFile("".$patch_app."/arquivos_google_play/img-play-destaque.jpg");

// Cria o print do app para o Play com a logo da radio
$printapp_base = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app.png");
$printapp_logo = WideImage::load("".$patch_tmp."/logo_".$hash.".png");

$play_printapp = $printapp_base->merge($printapp_logo, 'center', 'center', 100);
$play_printapp->saveToFile("".$patch_app."/arquivos_google_play/img-play-app.png");

// Escreve nome da radio no print do app
$printapp = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app.png");
$printapp_canvas = $printapp->getCanvas();
$printapp_canvas->useFont("".$patch_app."/assets/fonts/font.otf", 30, $printapp->allocateColor(255, 255, 255));
$printapp_canvas->writeText("center", "215", formatar_nome_radio($radio_nome));
$printapp->saveToFile("".$patch_app."/arquivos_google_play/img-play-app.png");

// Modifica o app source com os dados da radio // http://stm2.srvstm.com:8004/stream/2/
replace("".$patch_app."/res/values/strings.xml","URLSHOUTCAST","http://".$servidor_stm.":".$dados_stm["porta"]."/stream/".$ponto."/");
replace("".$patch_app."/res/values/strings.xml","URLSITE","".$endereco_site."");
replace("".$patch_app."/res/values/strings.xml","URLAPPPLAY","".$package."");
replace("".$patch_app."/res/values/strings.xml","radio_nome",utf8_encode($radio_nome));

replace("".$patch_app."/src/".$package_path."/DataManager.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/HeadsetReceiver.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/IcyStreamMeta.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/KillNotificationsService.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/LoadingAnimation.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/MainActivity.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/MainScreen.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/MusicPlayer.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/NoSwipeableViewPager.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/NotificationPanel.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/PhoneCallListener.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/RadioListElement.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/RadioList.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/RadiosScreen.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/TabPagerAdapter.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/".$package_path."/ToastCreator.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/com/cover/CircleImageView.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/com/cover/CoverBlur.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/com/cover/CoverGenerator.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/com/cover/ImageDownloader.java","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/src/com/cover/UrlGenerator.java","com.shoutcast.stm.radio_nome",$package);

replace("".$patch_app."/res/layout/activity_main.xml","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/res/layout/fragment_radios.xml","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/res/layout/viewpager_layout.xml","com.shoutcast.stm.radio_nome",$package);

replace("".$patch_app."/AndroidManifest.xml","com.shoutcast.stm.radio_nome",$package);
replace("".$patch_app."/.project","radio_nome",formatar_nome_radio($radio_nome));
replace("".$patch_app."/build.xml","radio_nome",nome_app_apk($radio_nome));

if($_POST["versao"] == '1.0') {
$codigo_versao = 1;
} elseif($_POST["versao"] == '1.1') {
$codigo_versao = 2;
} elseif($_POST["versao"] == '1.2') {
$codigo_versao = 3;
} elseif($_POST["versao"] == '1.3') {
$codigo_versao = 4;
} elseif($_POST["versao"] == '1.4') {
$codigo_versao = 5;
} elseif($_POST["versao"] == '1.5') {
$codigo_versao = 6;
} elseif($_POST["versao"] == '1.6') {
$codigo_versao = 7;
} elseif($_POST["versao"] == '1.7') {
$codigo_versao = 8;
} elseif($_POST["versao"] == '1.8') {
$codigo_versao = 9;
} elseif($_POST["versao"] == '1.9') {
$codigo_versao = 10;
} elseif($_POST["versao"] == '1.10') {
$codigo_versao = 11;
} else {
$codigo_versao = 1;
}

replace("".$patch_app."/AndroidManifest.xml","codigo_versao",$codigo_versao);
replace("".$patch_app."/AndroidManifest.xml","numero_versao",$_POST["versao"]);

// Muda o idioma do app conforme o idioma do painel
if($dados_stm["idioma_painel"] == "pt-br") {

replace("".$patch_app."/res/values/strings.xml","MSGPLAYBTN","Clique na tela pra iniciar");

} elseif($dados_stm["idioma_painel"] == "en-us") {

replace("".$patch_app."/res/values/strings.xml","MSGPLAYBTN","Tap screen to start");

} else {

replace("".$patch_app."/res/values/strings.xml","MSGPLAYBTN","Haga clic para iniciar");

}

if(strlen($_POST["radio_nome"]) > 21) {
replace("".$patch_app."/res/values/dimens.xml","25dp","12dp");
replace("".$patch_app."/res/values/dimens.xml","50dp","17dp");
}

if(strlen($_POST["radio_nome"]) > 15) {
replace("".$patch_app."/res/values/dimens.xml","25dp","15dp");
replace("".$patch_app."/res/values/dimens.xml","50dp","20dp");
}

// Insere os dados no banco de dados
mysql_query("INSERT INTO apps (codigo_stm,radio_nome,radio_site,radio_facebook,radio_twitter,package,data,print,source,hash) VALUES ('".$dados_stm["codigo"]."','".$radio_nome."','".$endereco_site."','".$endereco_facebook."','".$endereco_twitter."','".$package."',NOW(),'".$printapp_temporario."','".$source."','".$hash."')") or die("<script> alert(\"Ooops!\\n\\nOcorreu um erro ao tentar inserir os dados no banco de dados!\\n\\nEntre em contato com nosso suporte.\\n\\nLog: ".mysql_error()."\"); window.location = 'javascript:history.back(-1)'; </script>");
$codigo_app = mysql_insert_id();

// Remove o source do app
@unlink("".$patch_tmp."/logo_".$hash.".png");
@unlink("".$patch_tmp."/icone_".$hash.".png");

$dados_app_criado = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".$codigo_app."'"));

// Compila o app
$nome_apk = nome_app_apk($dados_app_criado["radio_nome"]);

// Compila o App
$resultado = shell_exec("export JAVA_HOME=/usr;cd ../app_android/apps/".$dados_app_criado["hash"].";/opt/ant/bin/ant release 2>&1");

if(preg_match('/BUILD SUCCESSFUL/i',$resultado)) {

@copy("../app_android/apps/".$dados_app_criado["hash"]."/bin/".$nome_apk."-release.apk","../app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/App-".$nome_apk.".apk");

// Cria o zip com o conteudo para publicação no google play
$zip = new ZipArchive();
if ($zip->open("app_android/apps/".$dados_app_criado["hash"].".zip", ZIPARCHIVE::CREATE)!==TRUE) {
    die("Não foi possível criar o arquivo ZIP: ".$dados_app_criado["hash"].".zip");
}

$zip->addEmptyDir("".$dados_app_criado["hash"]."");
$zip->addFile("../app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/App-".$nome_apk.".apk","".$dados_app_criado["hash"]."/App-".$nome_apk.".apk");
$zip->addFile("../app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-logo.png","".$dados_app_criado["hash"]."/img-play-logo.png");
$zip->addFile("../app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-destaque.jpg","".$dados_app_criado["hash"]."/img-play-destaque.jpg");
$zip->addFile("../app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-app.png","".$dados_app_criado["hash"]."/img-play-app.png");
$status=$zip->getStatusString();
$zip->close();

if(!file_exists("../app_android/apps/".$dados_app_criado["hash"].".zip")) {
shell_exec("cd ../app_android/apps/;/usr/bin/zip -1 ".$dados_app_criado["hash"].".zip ".$dados_app_criado["hash"].";/usr/bin/zip -1 ".$dados_app_criado["hash"].".zip ".$dados_app_criado["hash"]."/arquivos_google_play/*");
}

mysql_query("Update apps set apk = 'App-".$nome_apk.".apk', compilado = 'sim', zip = '".$dados_app_criado["hash"].".zip', status = '1' where codigo = '".$dados_app_criado["codigo"]."'");

// Remove source
if($dados_app_criado["hash"] != "") {
remover_source_app("../app_android/apps/".$dados_app_criado["hash"]."");
}

} else {

$resultado_build .= "Path: cd ../app_android/apps/".$dados_app_criado["hash"]."\n";
$resultado_build .= "Cmd: /opt/ant/bin/ant release\n";
$resultado_build .= $resultado;

mysql_query("Update apps set log_build = '".addslashes($resultado_build)."' where codigo = '".$dados_app_criado["codigo"]."'");

}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/ajax.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
  <table width="740" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="710" align="left" class="texto_pequeno_erro" scope="col"><?php echo lang_info_streaming_app_android_info_solicitacao_painel_stm; ?></td>
    </tr>
  </table>
<table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><div id="quadro">
      <div id="quadro-topo"> <strong><?php echo lang_info_streaming_app_android_tab_titulo; ?></strong>      </div>
      <div class="texto_medio" id="quadro-conteudo">
      <table width="720" border="0" align="center" cellpadding="0" cellspacing="0" style="border:#D5D5D5 1px solid;">
    <tr style="background:url(img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="70" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_app_android_solicitacoes_porta; ?></td>
      <td width="120" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_app_android_solicitacoes_data; ?></td>
      <td width="400" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_app_android_solicitacoes_status; ?></td>
      <td width="130" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_streaming_app_android_solicitacoes_acao; ?></td>
    </tr>
<?php
$total_apps = 0;
$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data FROM apps ORDER by codigo DESC");
while ($dados_app = mysql_fetch_array($sql)) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));

$app_code = code_decode($dados_app["codigo"],"E");

if($dados_stm["codigo_cliente"] == $dados_revenda["codigo"]) {

if($dados_app["status"] == 1) {
$status = lang_info_streaming_app_android_requisicao_concluida;

$acao = "<a href=\"../app_android/apps/".$dados_app["zip"]."\" target=\"_blank\">[Download]</a>&nbsp;<a href=\"javascript:executar_acao_diversa('".$app_code."','app-android-remover' );\" title='Remover Solicitação'>[Remover]</a>";

$cor_status = '#C6FFC6';

} elseif($dados_app["status"] == 2) {

$status = $dados_app["aviso"];
$acao = "<a href=\"javascript:executar_acao_diversa('".$app_code."','app-android-remover' );\" title='Remover Solicitação'>[Remover]</a>";
$cor_status = '#FFB9B9';

} else {

$status = "Esta solicitação é a da fila, favor aguardar.";
$acao = "<a href=\"javascript:executar_acao_diversa('".$app_code."','app-android-remover' );\" title='Remover Solicitação'>[Remover]</a>";
$cor_status = '#FFFFFF';
}

echo "<tr style='background-color:".$cor_status.";'>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["porta"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_app["data"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$status."</td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;".$acao."</td>
</tr>";
$total_apps++;
}
}

if($total_apps == 0) {
echo "<td colspan='4' height='25' align='center' class='texto_padrao_pequeno'>".lang_info_sem_registros."</td>";
}
?>
  </table>
      </div>
    </div></td>
  </tr>
</table>
<br />
<form action="/admin/revenda-app-android" method="post" name="form" enctype="multipart/form-data">
<table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <div id="quadro">
            <div id="quadro-topo"> <strong><?php echo lang_info_streaming_app_android_tab_titulo_instrucoes; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="100%" height="40" align="left" class="texto_padrao_pequeno"><?php echo lang_info_streaming_app_android_instrucoes_1; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_2; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_3; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_4; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_5; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_6; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_7; ?><br />
                      <?php echo lang_info_streaming_app_android_instrucoes_8; ?></td>
                </tr>
              </table>
            </div>
  </div>
    <br />
    <div id="quadro">
            <div id="quadro-topo"><strong><?php echo lang_info_streaming_app_android_tab_titulo_info_radio; ?></strong>
              <input name="enviar" type="hidden" id="enviar" value="sim" />
            </div><div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="20%" height="50" class="texto_padrao_destaque"><?php echo lang_info_streaming_app_android_radio_porta; ?></td>
      <td width="80%" class="texto_padrao_pequeno"><select id="porta" name="porta" style="width:355px">
<option value="" selected="selected"><?php echo lang_info_streaming_app_android_radio_selecionar_porta; ?></option>
  <optgroup label="Streamings">
<?php
$i = 0;
$sql = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."' ORDER by porta ASC");
while ($dados_lista_stm = mysql_fetch_array($sql)) {

	$dados_servidor_lista_stm = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_lista_stm["codigo_servidor"]."'"));

	echo "<option value='".$dados_lista_stm["porta"]."' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."'>".$dados_lista_stm["porta"]." - ".dominio_servidor($dados_servidor_lista_stm["nome"])." (".$dados_lista_stm["identificacao"].")</option>";

$i++;
}
?>
  </optgroup>
</select></td>
    </tr>
    <tr>
      <td width="20%" class="texto_padrao_destaque"><?php echo lang_info_streaming_app_android_radio_nome; ?></td>
      <td width="80%" class="texto_padrao_pequeno"><input name="radio_nome" type="text" id="radio_nome" style="width:350px" value="" />
          <br />
          <?php echo lang_info_streaming_app_android_info_radio_nome; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo lang_info_streaming_app_android_radio_site; ?></td>
      <td class="texto_padrao_pequeno"><input name="radio_site" type="text" id="radio_site" style="width:350px" value=""/>
        <br />
        <?php echo lang_info_streaming_app_android_info_radio_site; ?></td>
    </tr>
    <tr>
      <td height="50" class="texto_padrao_destaque"><?php echo lang_info_streaming_app_android_versao; ?></td>
      <td class="texto_padrao_pequeno">
        <select name="versao" id="versao">
          <option value="1.0" selected="selected">1.0</option>
          <option value="1.1">1.1</option>
          <option value="1.2">1.2</option>
          <option value="1.3">1.3</option>
          <option value="1.4">1.4</option>
          <option value="1.5">1.5</option>
          <option value="1.6">1.6</option>
          <option value="1.7">1.7</option>
          <option value="1.8">1.8</option>
          <option value="1.9">1.9</option>
          <option value="1.10">1.10</option>
        </select>
        <br />
        <?php echo lang_info_streaming_app_android_info_versao; ?></td>
    </tr>
  </table>
  </div>
  </div>
  <br />
<div id="quadro">
            <div id="quadro-topo"><strong><?php echo lang_info_streaming_app_android_tab_titulo_personalizacao_app; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="20%" class="texto_padrao_destaque"><?php echo lang_info_streaming_app_android_radio_logo; ?></td>
      <td width="80%" class="texto_padrao_pequeno"><input name="logo" type="file" id="logo" style="width:350px" />
          <br />
          <?php echo lang_info_streaming_app_android_info_radio_logo; ?></td>
    </tr>
    <tr>
      <td class="texto_padrao_destaque"><?php echo lang_info_streaming_app_android_radio_icone; ?></td>
      <td class="texto_padrao_pequeno"><input name="icone" type="file" id="icone" style="width:350px" />
          <br />
          <?php echo lang_info_streaming_app_android_info_radio_icone; ?></td>
    </tr>
  </table>
  </div>
  <br />
  <br />
    <center><input name="button" type="submit" class="botao" id="button" value="<?php echo lang_info_streaming_app_android_botao_submit; ?>" onclick="abrir_log_sistema();" /></center>

</td>
  </tr>
</table>
</form>
      <br />
    <br />
    <br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"><img src='/img/ajax-loader.gif' /></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
