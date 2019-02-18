
<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$dados_app_criado = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo_stm = '".$dados_stm["codigo"]."'"));

$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

$porta_code = code_decode($dados_stm["porta"],"E");

if(isset($_POST["enviar"])) {

if(isset($dados_app_criado["codigo"]) && $dados_app_criado["status"] < 2) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_app_existente']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

// Remove a requisição atual
mysql_query("Delete From apps where codigo = '".$dados_app_criado["codigo"]."' AND status = '2'");

require_once("admin/inc/wideimage/WideImage.php");

// Valida extensão
if($_FILES["logo"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_logo']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

if($_FILES["icone"]["type"] != "image/png") {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_formato_icone']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

if(strlen($_POST["radio_nome"]) > 30) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_max_caracter_nome_radio']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

if(empty($_POST["radio_nome"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_campo_vazio_nome_radio']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

if(empty($_POST["radio_site"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_campo_vazio_url_site']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

// Verifica se o primeiro caracter é numérico
if(preg_match('/^\d/',$_POST["radio_nome"])) {
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_nome_radio_numero']."\");
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
die ("<script> alert(\"".$lang['lang_info_streaming_app_android_resultado_erro_nome_radio_existente']."\");
       window.location = 'javascript:history.back(-1)'; </script>");
}

$servidor_stm = strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];

$patch_dir_apps = "app_android/apps";
$patch_app = "app_android/apps/".$hash."";
$patch_tmp = "app_android/apps/tmp";

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
copiar_source("app_android/".$source."/", $patch_app);

// Muda nome do package do source para o nome do package da radio
@rename("".$patch_app."/src/com/shoutcast/stm/radio_nome","".$patch_app."/src/com/shoutcast/stm/".nome_app_play($radio_nome)."");

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
$logo = $logo->resize(300, 300);
$logo->saveToFile("".$patch_app."/res/drawable-mdpi/radio_logo.png");

// Cria icone para o Play
$play_icone = WideImage::load("".$patch_tmp."/logo_".$hash.".png");
$play_icone = $play_icone->resize(512, 512);
$play_icone->saveToFile("".$patch_app."/arquivos_google_play/google-play-logo.png");

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

// Cria print temporario do app para exibir no resultado da requisição
$printapp_temp = WideImage::load("".$patch_app."/arquivos_google_play/img-play-app.png");
$printapp_temp = $printapp_temp->resize(200, 355);
$printapp_temp->saveToFile("".$patch_tmp."/".$hash."-img-play-app.png");

$printapp_temporario = "".$patch_tmp."/".$hash."-img-play-app.png";

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

//Bug fix
remover_source_app("app_android/apps/".$dados_app_criado["hash"]."/src/com/shoutcast/stm/radio_nome");

// Compila o App
$resultado = shell_exec("export JAVA_HOME=/usr;cd /home/painel/public_html/app_android/apps/".$dados_app_criado["hash"].";/opt/ant/bin/ant release 2>&1");

if(preg_match('/BUILD SUCCESSFUL/i',$resultado)) {

@copy("app_android/apps/".$dados_app_criado["hash"]."/bin/".$nome_apk."-release.apk","app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/App-".$nome_apk.".apk");

// Cria o zip com o conteudo para publicação no google play
$zip = new ZipArchive();
if ($zip->open("app_android/apps/".$dados_app_criado["hash"].".zip", ZIPARCHIVE::CREATE)!==TRUE) {
    die("Não foi possível criar o arquivo ZIP: ".$dados_app_criado["hash"].".zip");
}

$zip->addEmptyDir("".$dados_app_criado["hash"]."");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/App-".$nome_apk.".apk","".$dados_app_criado["hash"]."/App-".$nome_apk.".apk");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-logo.png","".$dados_app_criado["hash"]."/img-play-logo.png");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-destaque.jpg","".$dados_app_criado["hash"]."/img-play-destaque.jpg");
$zip->addFile("app_android/apps/".$dados_app_criado["hash"]."/arquivos_google_play/img-play-app.png","".$dados_app_criado["hash"]."/img-play-app.png");
$status=$zip->getStatusString();
$zip->close();

if(!file_exists("app_android/apps/".$dados_app_criado["hash"].".zip")) {
shell_exec("cd app_android/apps/;/usr/bin/zip -1 ".$dados_app_criado["hash"].".zip ".$dados_app_criado["hash"].";/usr/bin/zip -1 ".$dados_app_criado["hash"].".zip ".$dados_app_criado["hash"]."/arquivos_google_play/*");
}

mysql_query("Update apps set apk = 'App-".$nome_apk.".apk', compilado = 'sim', zip = '".$dados_app_criado["hash"].".zip', status = '1' where codigo = '".$dados_app_criado["codigo"]."'");

// Remove source
if($dados_app_criado["hash"] != "") {
remover_source_app("app_android/apps/".$dados_app_criado["hash"]."");
}

} else {

$resultado_build .= "Path: cd /home/painel/public_html/app_android/apps/".$dados_app_criado["hash"]."\n";
$resultado_build .= "Cmd: /opt/ant/bin/ant release\n";
$resultado_build .= $resultado;

mysql_query("Update apps set log_build = '".addslashes($resultado_build)."' where codigo = '".$dados_app_criado["codigo"]."'");

}

}
?>

<?php include('header.php');
?>
<body class="hold-transition skin-blue sidebar-mini">
<?php if($dados_stm["status"] == 1) { ?>
<?php if($verificacao_revenda_logada === true) { ?>
<div class="texto_padrao_vermelho" id="barra-alerta-revenda-logada">
<img src="/img/icones/atencao.png" width="16" height="16" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_acessar_painel_revenda_logado']; ?>
</div>
<?php } ?>
<div class="wrapper">

 <?php include('include-header-top.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  <?php include('include-menu-left.php'); ?>

  <!-- Content Wrapper. Contains page content -->
<?php include('include-info-top.php');?>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->


        <section class="col-lg-7 connectedSortable">

 <?php if(isset($dados_app_criado["codigo"])) { ?>
  <div class="box-header with-border">
<table class="table table-bordered">
  <tr>
    <td>
      
      <div class="box-header with-border">
       <h3><?php echo $lang['lang_info_streaming_app_android_tab_titulo']; ?></h3> 
     </div>
      <div class="texto_medio" id="quadro-conteudo">
      <table class="table table-bordered">

    <tr>
      <td>&nbsp;<?php echo $lang['lang_info_streaming_app_android_data']; ?></td>
      <td>&nbsp;<?php echo $lang['lang_info_streaming_app_android_status']; ?></td>
      <td>&nbsp;<?php echo $lang['lang_info_streaming_app_android_acao']; ?></td>
    </tr>

<?php
$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data FROM apps WHERE codigo_stm = '".$dados_stm["codigo"]."'");
while ($dados_app = mysql_fetch_array($sql)) {

$app_code = code_decode($dados_app["codigo"],"E");

if($dados_app["status"] == 1) {

$status = $lang['lang_info_streaming_app_android_requisicao_concluida'];

$acao = "<a href=\"/app_android/apps/".$dados_app["zip"]."\" target=\"_blank\" class=\"btn bg-olive margin\">[Download]</a>
&nbsp;<a href=\"javascript:executar_acao_streaming_autodj('".$app_code."','remover-app-android' );\"class=\"btn bg-orange margin\">".$lang['lang_info_streaming_app_android_botao_remover_app']."</a>";

$cor_status = '#C6FFC6';

} elseif($dados_app["status"] == 2) {

$status = $dados_app["aviso"];
$acao = "<a href=\"javascript:executar_acao_streaming_autodj('".$app_code."','remover-app-android' );\">".$lang['lang_info_streaming_app_android_botao_remover_app']."</a>";
$cor_status = '#FFB9B9';

} else {

$status = $lang['lang_info_streaming_app_android_requisicao_em_andamento'];
$acao = "<a href=\"javascript:executar_acao_streaming_autodj('".$app_code."','remover-app-android' );\">".$lang['lang_info_streaming_app_android_botao_remover_app']."</a>";
$cor_status = '#FFFFFF';
}

echo "<tr style='background-color:".$cor_status.";'>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_app["data"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$status."</td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;".$acao."</td>
</tr>";

}
?>
  </table>
      </div>
    
</td>
  </tr>
  <?php if(isset($patch_tmp)) { ?>
  <tr>
    <td height="350" align="center"><br /><br /><img src="<?php echo "/".$patch_tmp."/".$hash."-img-play-app.png";?>" /><br /><br />
      <span class="texto_padrao_verde_destaque"><?php echo $lang['lang_info_streaming_app_android_info_previa']; ?></span></td>
  </tr>
  <?php } ?>
</table>
</div>
<br />
<?php } ?>

<?php if(!isset($dados_app_criado["codigo"]) || $dados_app_criado["status"] == 2) { ?>        
<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informações da Radio</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            
            <form role="form" action="/app-android" method="post" name="form" enctype="multipart/form-data">
              <input name="enviar" type="hidden" id="enviar" value="sim" />
              <div class="box-body">
                <div class="form-group">
                  <label>Nome da Rádio</label>
                  <input name="radio_nome" type="text" class="form-control" id="radio_nome">
                  <p class="help-block">(Máximo 30 caracteres, regra do Google Play)</p>
                </div>
                <div class="form-group">
                  <label>Nome do Site</label>
                  <input name="radio_site" type="text" id="radio_site" class="form-control">
                  <p class="help-block">(Digite a URL completa com http:// do site da radio)</p>
                </div>

<div class="form-group">
<label for="exampleInputEmail1"><?php echo $lang['lang_info_streaming_app_android_versao']; ?></label>
            <select name="versao" id="versao" class="form-control select2" style="width:75px;">
              
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
  <p class="help-block"><?php echo $lang['lang_info_streaming_app_android_versao']; ?> </p>         
</div>
<?php if($total_pontos > 0) { ?>    
<div class="form-group">
<label for="exampleInputEmail1"><?php echo $lang['lang_info_streaming_app_android_multipoint']; ?></label>
            <select name="ponto" id="ponto" class="form-control select2" style="width:355px;">
            <?php
            $sql = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
            while ($dados_ponto = mysql_fetch_array($sql)) {

            echo '<option value="' . $dados_ponto["id"] . '">ID: ' . $dados_ponto["id"] . ' - Ponto: ' . $dados_ponto["ponto"] . '</option>';

            }

            ?>
          </select>        
</div>
<?php } ?>
                
              </div>
              <!-- /.box-body -->
              
            
          </div>


          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo $lang['lang_info_streaming_app_android_tab_titulo_personalizacao_app']; ?></h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
           
              <div class="box-body">
                
                <div class="form-group">
                  <label for="exampleInputFile">Logomarga</label>
                  <input name="logo" type="file" id="logo">

                  <p class="help-block">(Deve ter exatamente 300x300 pixels, fundo transparente e ser PNG)</p>
                </div>

                <div class="form-group">
                  <label for="exampleInputFile">Ícone</label>
                  <input name="icone" type="file" id="icone">

                  <p class="help-block">(Deve ter exatamente 144x144 pixels, fundo transparente e ser PNG)</p>
                </div>
                
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button name="button" type="submit" class="btn btn-primary" id="button"><?php echo $lang['lang_info_streaming_app_android_botao_submit']; ?></button>
              </div>
            </form>
          </div>

<?php } ?>


        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">

<div class="callout callout-warning">
                <h4>Instruções!</h4>

                <p>
-Siga as instruções abaixo de cada campo para evitar erros na criação e layout do seu app.</p>
<p>-A logomarca e o ícone devem ter fundo transparênte e ser uma imagem PNG.</p>
<p>-Se for usar um fundo colorido teste antes colocando a logomarca em cima do fundo(em um editor de imagem) para ver se fica com contraste bom.</p>
<p>-Não use medidas menores ou maiores do que as específicadas abaixo de cada campo.</p>
<p>-Se não tiver facebook ou twitter, deixe estes campos totalmente em branco que será usado a URL do site no lugar deles.</p>
<p>-O App será criado e disponibilizado para download no mesmo instante da solicitação caso não haja nenhum erro.</p>
              </div>


        </section>
        <!-- right col -->
      </div>
      <!-- /.row (main row) -->


<section>
  


  
</section>


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="https://voxpanel.github.io">voxpanel.github.io</a>.</strong> Direitos Reservados.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-user bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                <p>New phone +1(800)555-1234</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                <p>nora@example.com</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-file-code-o bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                <p>Execution time 5 seconds</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="label label-danger pull-right">70%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Update Resume
                <span class="label label-success pull-right">95%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Laravel Integration
                <span class="label label-warning pull-right">50%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Back End Framework
                <span class="label label-primary pull-right">68%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Allow mail redirect
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Other sets of options are available
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Expose author name in posts
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Allow the user to show his name in blog posts
            </p>
          </div>
          <!-- /.form-group -->

          <h3 class="control-sidebar-heading">Chat Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Show me as online
              <input type="checkbox" class="pull-right" checked>
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Turn off notifications
              <input type="checkbox" class="pull-right">
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Delete chat history
              <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
            </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
<?php } else { ?>
<div style="width:100%; height:700px;cursor: not-allowed; z-index:-1">
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
  <tr>
        <td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo $lang['lang_alerta_bloqueio']; ?></td>
    </tr>
</table>
</div>
<?php } ?>
</body>
</html>
