<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));

if(isset($_POST["alterar"])) {

mysql_query("Update streamings set autodj_channels = '".$_POST["autodj_channels"]."', autodj_samplerate = '".$_POST["autodj_samplerate"]."', encoder = '".$_POST["encoder"]."', autodj_prog_aovivo = '".$_POST["autodj_prog_aovivo"]."', autodj_prog_aovivo_msg = '".$_POST["autodj_prog_aovivo_msg"]."' where codigo = '".$dados_stm["codigo"]."'");

// Atualiza Cache dos players caso necessário
if($_POST["autodj_prog_aovivo"] != $dados_stm["autodj_prog_aovivo"]) {
@file_get_contents("http://player.srvstm.com/atualizar-cache-player/".$dados_stm["porta"]."");
}

if(!mysql_error()) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_config_autodj_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_config_autodj_resultado_erro']." ".mysql_error()."","erro");

}

header("Location: /configuracoes-autodj");
exit();

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
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
<form method="post" action="/configuracoes-autodj" style="padding:0px; margin:0px">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_config_autodj_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
  	  <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_config_autodj_info']; ?></td>
     </tr>
    </table>    </td>
  </tr>
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_autodj_aba_qualidade_audio']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_autodj_canal']; ?></td>
            <td width="540" align="left">
            <select name="autodj_channels" class="input" id="autodj_channels" style="width:255px;">
          <option value="2"<?php if($dados_stm["autodj_channels"] == "2") { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_config_autodj_canal_stereo']; ?></option>
          <option value="1"<?php if($dados_stm["autodj_channels"] == "1") { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_config_autodj_canal_mono']; ?></option>
         </select>            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_autodj_samplerate']; ?></td>
            <td align="left">
            <select name="autodj_samplerate" class="input" id="autodj_samplerate" style="width:255px;">
		  <option value="22050"<?php if($dados_stm["autodj_samplerate"] == "22050") { echo ' selected="selected"'; } ?>>22050 Hz</option>
		  <option value="32000"<?php if($dados_stm["autodj_samplerate"] == "32000") { echo ' selected="selected"'; } ?>>32000 Hz</option>
		  <option value="44100"<?php if($dados_stm["autodj_samplerate"] == "44100") { echo ' selected="selected"'; } ?>>44100 Hz (padrão/default)</option>
		  <option value="48000"<?php if($dados_stm["autodj_samplerate"] == "48000") { echo ' selected="selected"'; } ?>>48000 Hz</option>
		  <option value="88200"<?php if($dados_stm["autodj_samplerate"] == "88200") { echo ' selected="selected"'; } ?>>88200 Hz</option>
		  <option value="96000"<?php if($dados_stm["autodj_samplerate"] == "96000") { echo ' selected="selected"'; } ?>>96000 Hz</option>
         </select>            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_autodj_encoder']; ?></td>
            <td align="left">
            <select name="encoder" class="input" id="encoder" style="width:255px;">
            <?php if($dados_stm["encoder_mp3"] == "sim") { ?>
              <option value="mp3"<?php if($dados_stm["encoder"] == "mp3") { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_config_autodj_encoder_mp3']; ?></option>
            <?php } ?>
            <?php if($dados_stm["encoder_aacplus"] == "sim") { ?>
              <option value="aacp"<?php if($dados_stm["encoder"] == "aacp") { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_config_autodj_encoder_aacplus']; ?></option>
            <?php } ?>
            </select>
            <?php if($dados_stm["encoder_mp3"] != "sim" && $dados_stm["encoder_aacplus"] != "sim") { ?>
            &nbsp;<?php echo $lang['lang_info_config_autodj_encoder_inativo_info']; ?>
            <?php } ?>
         </td>
          </tr>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_config_autodj_aba_transmissao_aovivo']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_autodj_porta_dj']; ?></td>
            <td width="540" align="left" class="texto_padrao"><?php echo $dados_stm["porta_dj"]; ?></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_autodj_programacao_aovivo']; ?></td>
            <td align="left" class="texto_padrao"><input name="autodj_prog_aovivo" type="checkbox" id="autodj_prog_aovivo" value="sim" style="vertical-align:middle"<?php if($dados_stm["autodj_prog_aovivo"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_info_config_autodj_ativar_programacao_aovivo']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_autodj_ativar_programacao_aovivo_info']; ?>');" style="cursor:pointer" /></span></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_autodj_programacao_aovivo_mensagem']; ?></td>
            <td align="left"><input name="autodj_prog_aovivo_msg" type="text" class="input" id="autodj_prog_aovivo_msg" style="width:250px;" value="<?php echo $dados_stm["autodj_prog_aovivo_msg"]; ?>" /></td>
          </tr>
        </table>
      </div>
    </div>    </td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_alterar_config']; ?>" />
      <input name="alterar" type="hidden" id="alterar" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
      </div>
</form>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>