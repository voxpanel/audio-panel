<?php
set_time_limit(0);
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="/inc/ajax-streaming.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
	
   };
   function disableselect(e){
	return false;
	}
	function reEnable(){
	return true;
	}
	document.onselectstart=new Function ("return false")
	if (window.sidebar){
	document.onmousedown=disableselect
	document.onclick=reEnable
	}
	document.oncontextmenu = function (e) {
    return false;
	};
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<div id="quadro_requisicao" style="display:none">
  <div id="quadro">
            <div id="quadro-topo"><strong><?php echo $lang['lang_info_utilitario_youtube_tab_resultado']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td align="center" class="texto_padrao"><img src="/img/ajax-loader.gif" width="220" height="19" id="img_loader" /><br />
                  <div id="resultado_requisicao" style="width:98%; height:150px; border:#999999 1px solid; text-align:left; overflow-y:scroll; padding:5px; background-color:#F4F4F7" class="texto_padrao"></div></td>
                </tr>
              </table>
          </div>
        </div>
<br />
</div>
<form action="/utilitario-youtube-processa" method="post" name="youtube">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_utilitario_youtube_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><?php echo $lang['lang_info_utilitario_youtube_info']; ?></td>
      </tr>
    </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
<?php
$total_urls = 0;
$lista_url_videos = explode("\n",$_POST["videos"]);

foreach($lista_url_videos as $url_video) {

$video_id = youtube_parser($url_video);

if($total_urls < 6) {

if(strlen($video_id) > 2) {

echo '<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; background-color:#F4F4F7; border:#CCCCCC 1px solid;">
	<tr>
        <td width="130" height="110" align="center"><img src="https://img.youtube.com/vi/'.$video_id.'/mqdefault.jpg" width="120" height="68" alt="Preview"></td>
        <td width="560" align="left"><iframe src="http://'.$dados_servidor["ip"].':555/youtube.php?porta='.$dados_stm["porta"].'&pasta='.$_POST["pasta"].'&video='.$video_id.'" frameborder="0" width="100%" height="100" style="background: #FFFFFF url(\'http://srvstm.com/img/ajax-loader.gif\') center right no-repeat;" onload="this.style.background=\'#FFFFFF\';"></iframe>
</td>
</tr>
</table>';
$total_urls++;
}
}
}
?>
</td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_voltar']; ?>" onclick="window.open('/utilitario-youtube','conteudo');" /></td>
  </tr>
</table>
    </div>
    </div>
</form>
</div>
<br />
<br />
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo">
<div class="meter">
	<span style="width: 100%"></span>
</div>
</div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
