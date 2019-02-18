<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ssh.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if(isset($_POST["alterar"])) {

if($dados_stm["permitir_alterar_senha"] == 'sim') {

mysql_query("Update streamings set senha = '".$_POST["senha"]."', streamtitle = '".addslashes(formatar_texto($_POST["streamtitle"]))."', streamurl = '".$_POST["streamurl"]."', genre = '".addslashes(formatar_texto($_POST["genre"]))."', descricao = '".addslashes($_POST["descricao"])."', showlastsongs = '".$_POST["showlastsongs"]."', publicserver = '".$_POST["publicserver"]."', allowrelay = '".$_POST["allowrelay"]."', player_autoplay = '".$_POST["player_autoplay"]."', player_exibir_chat = '".$_POST["player_exibir_chat"]."', player_exibir_pedido_musical = '".$_POST["player_exibir_pedido_musical"]."', player_volume_inicial = '".$_POST["player_volume_inicial"]."', timezone = '".$_POST["timezone"]."', formato_data = '".$_POST["formato_data"]."' where codigo = '".$dados_stm["codigo"]."'");

} else {

mysql_query("Update streamings set streamtitle = '".addslashes(formatar_texto($_POST["streamtitle"]))."', streamurl = '".$_POST["streamurl"]."', genre = '".addslashes(formatar_texto($_POST["genre"]))."', descricao = '".addslashes($_POST["descricao"])."', showlastsongs = '".$_POST["showlastsongs"]."', publicserver = '".$_POST["publicserver"]."', allowrelay = '".$_POST["allowrelay"]."', player_autoplay = '".$_POST["player_autoplay"]."', player_exibir_chat = '".$_POST["player_exibir_chat"]."', player_exibir_pedido_musical = '".$_POST["player_exibir_pedido_musical"]."', player_volume_inicial = '".$_POST["player_volume_inicial"]."', timezone = '".$_POST["timezone"]."', formato_data = '".$_POST["formato_data"]."' where codigo = '".$dados_stm["codigo"]."'");

}

// Envia a musica de intro/backup
if(isset($_FILES["arquivo_intro"])) {

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->executar("mkdir -v /home/streaming/".$dados_stm["porta"]."");
$ssh->enviar_arquivo($_FILES["arquivo_intro"]["tmp_name"],"/home/streaming/".$dados_stm["porta"]."/streaming_intro.mp3",0644);

mysql_query("Update streamings set arquivo_intro = 'streaming_intro.mp3' where codigo = '".$dados_stm["codigo"]."'");
}

if(isset($_FILES["arquivo_backup"])) {

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->executar("mkdir -v /home/streaming/".$dados_stm["porta"]."");
$ssh->enviar_arquivo($_FILES["arquivo_backup"]["tmp_name"],"/home/streaming/".$dados_stm["porta"]."/streaming_backup.mp3",0644);

mysql_query("Update streamings set arquivo_backup = 'streaming_backup.mp3' where codigo = '".$dados_stm["codigo"]."'");
}

// Atualiza Cache dos players caso necessário
if($_POST["player_autoplay"] != $dados_stm["player_autoplay"] || $_POST["player_exibir_chat"] != $dados_stm["player_exibir_chat"] || $_POST["player_exibir_pedido_musical"] != $dados_stm["player_exibir_pedido_musical"] || $_POST["player_volume_inicial"] != $dados_stm["player_volume_inicial"]) {
@file_get_contents("http://player.srvstm.com/atualizar-cache-player/".$dados_stm["porta"]."");
}

if(!mysql_error()) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_config_stm_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_config_stm_resultado_erro']." ".mysql_error()."","erro");

}

header("Location: /configuracoes-streaming");
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
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
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
<form method="post" action="/configuracoes-streaming" style="padding:0px; margin:0px" enctype="multipart/form-data">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_config_stm_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_stm_aba_senha']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
        <?php if($dados_stm["permitir_alterar_senha"] == 'sim') { ?>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_senha']; ?></td>
            <td align="left">
            <input name="senha" type="password" class="input" id="senha" style="width:250px;" value="<?php echo $dados_stm["senha"]; ?>" onkeyup="bloquear_acentos(this);" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_senha_info']; ?>');" style="cursor:pointer" />         </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_senha_admin']; ?></td>
            <td align="left" class="texto_padrao"><input type="text" class="input" style="width:250px;cursor:not-allowed" value="<?php echo $dados_stm["senha_admin"]; ?>" disabled="disabled" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_senha_admin_info']; ?>');" style="cursor:pointer" />         </td>
          </tr>
          <?php } else { ?>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_senha']; ?></td>
            <td align="left">
            <input type="password" class="input" style="width:250px;cursor:not-allowed" value="disabled" disabled="disabled" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_senha_info']; ?>');" style="cursor:pointer" />         </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_senha_admin']; ?></td>
            <td align="left" class="texto_padrao"><input type="password" class="input" style="width:250px;cursor:not-allowed" value="disabled" disabled="disabled" />&nbsp;&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_senha_admin_info']; ?>');" style="cursor:pointer" />         </td>
          </tr>
          <?php } ?>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_config_stm_aba_info_radio']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_titulo']; ?></td>
            <td align="left"><input name="streamtitle" type="text" class="input" id="streamtitle" style="width:250px;" value="<?php echo $dados_stm["streamtitle"]; ?>" onkeyup="bloquear_acentos(this);" />            </td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_url']; ?></td>
            <td align="left" class="texto_padrao"><input name="streamurl" type="text" class="input" id="streamurl" style="width:250px;" value="<?php echo $dados_stm["streamurl"]; ?>" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_genero']; ?></td>
            <td align="left">
            <?php $genero_personalizado == "sim"; ?>
            <select name="lista_generos" class="input" id="lista_generos" style="width:255px;" onchange="document.getElementById('genre').value = this.value;">
            	<option value="" selected="selected">OUTRO/Other(personalizado)</option>
				<option value="Alternative"<?php if($dados_stm["genre"] == "Alternative") { echo ' selected="selected"'; } ?>>Alternative</option>
				<option value="Blues"<?php if($dados_stm["genre"] == "Blues") { echo ' selected="selected"'; } ?>>Blues</option>
				<option value="Classical"<?php if($dados_stm["genre"] == "Classical") { echo ' selected="selected"'; } ?>>Classical</option>
				<option value="Country"<?php if($dados_stm["genre"] == "Country") { echo ' selected="selected"'; } ?>>Country</option>
				<option value="Decades"<?php if($dados_stm["genre"] == "c") { echo ' selected="selected"'; } ?>>Decades/Decadas(50,60,70,80,90)</option>
				<option value="Electronic"<?php if($dados_stm["genre"] == "Electronic") { echo ' selected="selected"'; } ?>>Electronic</option>
				<option value="Folk"<?php if($dados_stm["genre"] == "Folk") { echo ' selected="selected"'; } ?>>Folk</option>
				<option value="International"<?php if($dados_stm["genre"] == "International") { echo ' selected="selected"'; } ?>>International</option>
				<option value="Jazz"<?php if($dados_stm["genre"] == "Jazz") { echo ' selected="selected"'; } ?>>Jazz</option>
				<option value="Latin"<?php if($dados_stm["genre"] == "Latin") { echo ' selected="selected"'; } ?>>Latin</option>
				<option value="Metal"<?php if($dados_stm["genre"] == "Metal") { echo ' selected="selected"'; } ?>>Metal</option>
				<option value="Misc"<?php if($dados_stm["genre"] == "Misc") { echo ' selected="selected"'; } ?>>Misc</option>
				<option value="New Age"<?php if($dados_stm["genre"] == "New Age") { echo ' selected="selected"'; } ?>>New Age</option>
				<option value="Pop"<?php if($dados_stm["genre"] == "Pop") { echo ' selected="selected"'; } ?>>Pop</option>
				<option value="Rap"<?php if($dados_stm["genre"] == "Rap") { echo ' selected="selected"'; } ?>>Rap</option>
				<option value="Reggae"<?php if($dados_stm["genre"] == "Reggae") { echo ' selected="selected"'; } ?>>Reggae</option>
				<option value="Rock"<?php if($dados_stm["genre"] == "Rock") { echo ' selected="selected"'; } ?>>Rock</option>
                <option value="Gospel"<?php if($dados_stm["genre"] == "Gospel") { echo ' selected="selected"'; } ?>>Gospel</option>
                <option value="Catolic"<?php if($dados_stm["genre"] == "Catolic") { echo ' selected="selected"'; } ?>>Católico</option>
			</select>
            <input name="genre" type="text" class="input" id="genre" style="width:200px;" value="<?php echo $dados_stm["genre"]; ?>" onkeyup="bloquear_acentos(this);" />            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_descricao']; ?></td>
            <td align="left"><input name="descricao" type="text" class="input" id="descricao" style="width:250px;" value="<?php echo $dados_stm["descricao"]; ?>" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_descricao_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">&nbsp;</td>
            <td align="left">&nbsp;</td>
          </tr>
        </table>
      </div>
      <div class="tab-page" id="tabPage3">
       	<h2 class="tab"><?php echo $lang['lang_info_config_stm_aba_config']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_qtd_musicas']; ?></td>
            <td align="left"><input name="showlastsongs" type="text" class="input" id="showlastsongs" style="width:90px;" value="<?php echo $dados_stm["showlastsongs"]; ?>" />&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_qtd_musicas_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_publico']; ?></td>
            <td align="left" class="texto_padrao"><input name="publicserver" type="radio" value="always" style="vertical-align:middle"<?php if($dados_stm["publicserver"] == "always") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<input name="publicserver" type="radio" value="never" style="vertical-align:middle"<?php if($dados_stm["publicserver"] == "never") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_publico_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_liberar_relay']; ?></td>
            <td align="left" class="texto_padrao"><input name="allowrelay" type="radio" value="1" style="vertical-align:middle"<?php if($dados_stm["allowrelay"] == "1") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<input name="allowrelay" type="radio" value="0" style="vertical-align:middle"<?php if($dados_stm["allowrelay"] == "0") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_liberar_relay_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <?php if($dados_stm["arquivo_intro"]) { ?>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_audio_intro']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><input name="arquivo_intro" type="file" class="texto_padrao_pequeno" size="30" accept="audio/*" />
              &nbsp;(<?php if($dados_stm["arquivo_intro"]) { echo $lang['lang_info_config_stm_audio_intro_backup_atual'].$dados_stm["arquivo_intro"]; } else { echo $lang['lang_info_config_stm_audio_intro_backup_nao_definido']; } ?>)&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_audio_intro_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_stm_audio_backup']; ?></td>
            <td align="left" class="texto_padrao_pequeno"><input name="arquivo_backup" type="file" class="texto_padrao_pequeno" size="30" accept="audio/*" />
              &nbsp;(<?php if($dados_stm["arquivo_backup"]) { echo $lang['lang_info_config_stm_audio_intro_backup_atual'].$dados_stm["arquivo_backup"]; } else { echo $lang['lang_info_config_stm_audio_intro_backup_nao_definido']; } ?>)&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_audio_backup_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <div class="tab-page" id="tabPage3">
       	<h2 class="tab"><?php echo $lang['lang_info_config_stm_aba_players']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_auto_play']; ?></td>
            <td align="left" class="texto_padrao"><input name="player_autoplay" type="radio" value="true" style="vertical-align:middle"<?php if($dados_stm["player_autoplay"] == "true") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<input name="player_autoplay" type="radio" value="false" style="vertical-align:middle"<?php if($dados_stm["player_autoplay"] == "false") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_auto_play_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_player_exibir_chat']; ?></td>
            <td align="left" class="texto_padrao"><input name="player_exibir_chat" type="radio" value="sim" style="vertical-align:middle"<?php if($dados_stm["player_exibir_chat"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<input name="player_exibir_chat" type="radio" value="nao" style="vertical-align:middle"<?php if($dados_stm["player_exibir_chat"] == "nao") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_stm_player_exibir_chat_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_player_exibir_pedido_musical']; ?></td>
            <td align="left" class="texto_padrao"><input name="player_exibir_pedido_musical" type="radio" value="sim" style="vertical-align:middle"<?php if($dados_stm["player_exibir_pedido_musical"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<input name="player_exibir_pedido_musical" type="radio" value="nao" style="vertical-align:middle"<?php if($dados_stm["player_exibir_pedido_musical"] == "nao") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_nao']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['player_exibir_pedido_musical_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_stm_player_volume_inicial']; ?></td>
            <td align="left" class="texto_padrao">
            <select name="player_volume_inicial" class="input" id="player_volume_inicial" style="width:115px;">
          		<option value="1.0"<?php if($dados_stm["player_volume_inicial"] == "1.0") { echo ' selected="selected"'; } ?>>100%</option>
                <option value="0.9"<?php if($dados_stm["player_volume_inicial"] == "0.9") { echo ' selected="selected"'; } ?>>90%</option>
                <option value="0.8"<?php if($dados_stm["player_volume_inicial"] == "0.8") { echo ' selected="selected"'; } ?>>80%</option>
                <option value="0.7"<?php if($dados_stm["player_volume_inicial"] == "0.7") { echo ' selected="selected"'; } ?>>70%</option>
                <option value="0.6"<?php if($dados_stm["player_volume_inicial"] == "0.6") { echo ' selected="selected"'; } ?>>60%</option>
                <option value="0.5"<?php if($dados_stm["player_volume_inicial"] == "0.5") { echo ' selected="selected"'; } ?>>50%</option>
                <option value="0.4"<?php if($dados_stm["player_volume_inicial"] == "0.4") { echo ' selected="selected"'; } ?>>40%</option>
                <option value="0.3"<?php if($dados_stm["player_volume_inicial"] == "0.3") { echo ' selected="selected"'; } ?>>30%</option>
                <option value="0.2"<?php if($dados_stm["player_volume_inicial"] == "0.2") { echo ' selected="selected"'; } ?>>20%</option>
                <option value="0.1"<?php if($dados_stm["player_volume_inicial"] == "0.1") { echo ' selected="selected"'; } ?>>10%</option>
         	</select>            </td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
            <td align="left" class="texto_padrao">&nbsp;</td>
          </tr>
        </table>
      </div>
      <div class="tab-page" id="tabPage3">
       	<h2 class="tab"><?php echo $lang['lang_info_config_stm_aba_timezone_datas']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#FFFF66; border:#DFDF00 1px solid">
  	  		<tr>
        		<td width="30" height="30" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        		<td width="860" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_config_painel_info_hora_certa']; ?></td>
     		</tr>
    	</table>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_painel_time_zone']; ?></td>
            <td align="left">
            <select name="timezone" id="timezone" style="width:255px;">
<optgroup label="Afeganistão (AF)">
<option <?php if($dados_stm["timezone"] == "Asia/Kabul") { echo 'selected="selected"'; } ?> value="Asia/Kabul">Cabul</option>
</optgroup>
<optgroup label="África do Sul (ZA)">
<option <?php if($dados_stm["timezone"] == "Africa/Johannesburg") { echo 'selected="selected"'; } ?> value="Africa/Johannesburg">Joanesburgo</option>
</optgroup>
<optgroup label="Albânia (AL)">
<option <?php if($dados_stm["timezone"] == "Europe/Tirane") { echo 'selected="selected"'; } ?> value="Europe/Tirane">Tirana</option>
</optgroup>
<optgroup label="Alemanha (DE)">
<option <?php if($dados_stm["timezone"] == "Europe/Berlin") { echo 'selected="selected"'; } ?> value="Europe/Berlin">Berlim</option>
<option <?php if($dados_stm["timezone"] == "Europe/Busingen") { echo 'selected="selected"'; } ?> value="Europe/Busingen">Büsingen</option>
</optgroup>
<optgroup label="Andorra (AD)">
<option <?php if($dados_stm["timezone"] == "Europe/Andorra") { echo 'selected="selected"'; } ?> value="Europe/Andorra">Andorra</option>
</optgroup>
<optgroup label="Angola (AO)">
<option <?php if($dados_stm["timezone"] == "Africa/Luanda") { echo 'selected="selected"'; } ?> value="Africa/Luanda">Luanda</option>
</optgroup>
<optgroup label="Anguilla (AI)">
<option <?php if($dados_stm["timezone"] == "America/Anguilla") { echo 'selected="selected"'; } ?> value="America/Anguilla">Anguilla</option>
</optgroup>
<optgroup label="Antártica (AQ)">
<option <?php if($dados_stm["timezone"] == "Antarctica/Casey") { echo 'selected="selected"'; } ?> value="Antarctica/Casey">Casey</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Davis") { echo 'selected="selected"'; } ?> value="Antarctica/Davis">Davis</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/DumontDUrville") { echo 'selected="selected"'; } ?> value="Antarctica/DumontDUrville">Dumont D'Urville</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Mawson") { echo 'selected="selected"'; } ?> value="Antarctica/Mawson">Mawson</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/McMurdo") { echo 'selected="selected"'; } ?> value="Antarctica/McMurdo">McMurdo</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Palmer") { echo 'selected="selected"'; } ?> value="Antarctica/Palmer">Palmer</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/South_Pole") { echo 'selected="selected"'; } ?> value="Antarctica/South_Pole">Pólo Sul</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Rothera") { echo 'selected="selected"'; } ?> value="Antarctica/Rothera">Rothera</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Syowa") { echo 'selected="selected"'; } ?> value="Antarctica/Syowa">Syowa</option>
<option <?php if($dados_stm["timezone"] == "Antarctica/Vostok") { echo 'selected="selected"'; } ?> value="Antarctica/Vostok">Vostok</option>
</optgroup>
<optgroup label="Antígua e Barbuda (AG)">
<option <?php if($dados_stm["timezone"] == "America/Antigua") { echo 'selected="selected"'; } ?> value="America/Antigua">Antigua</option>
</optgroup>
<optgroup label="Antilhas Neerlandesas (AN)"></optgroup>
<optgroup label="Arábia Saudita (SA)">
<option <?php if($dados_stm["timezone"] == "Asia/Riyadh") { echo 'selected="selected"'; } ?> value="Asia/Riyadh">Riade</option>
</optgroup>
<optgroup label="Argélia (DZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Algiers") { echo 'selected="selected"'; } ?> value="Africa/Algiers">Algéria</option>
</optgroup>
<optgroup label="Argentina (AR)">
<option <?php if($dados_stm["timezone"] == "America/Argentina/Buenos_Aires") { echo 'selected="selected"'; } ?> value="America/Argentina/Buenos_Aires">Buenos Aires</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Catamarca") { echo 'selected="selected"'; } ?> value="America/Argentina/Catamarca">Catamarca</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Cordoba") { echo 'selected="selected"'; } ?> value="America/Argentina/Cordoba">Córdoba</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Jujuy") { echo 'selected="selected"'; } ?> value="America/Argentina/Jujuy">Jujuy</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/La_Rioja") { echo 'selected="selected"'; } ?> value="America/Argentina/La_Rioja">La Rioja</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Mendoza") { echo 'selected="selected"'; } ?> value="America/Argentina/Mendoza">Mendoza</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Rio_Gallegos") { echo 'selected="selected"'; } ?> value="America/Argentina/Rio_Gallegos">Rio Gallegos</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Salta") { echo 'selected="selected"'; } ?> value="America/Argentina/Salta">Salta</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/San_Juan") { echo 'selected="selected"'; } ?> value="America/Argentina/San_Juan">San Juan</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/San_Luis") { echo 'selected="selected"'; } ?> value="America/Argentina/San_Luis">San Luís</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Tucuman") { echo 'selected="selected"'; } ?> value="America/Argentina/Tucuman">Tucumán</option>
<option <?php if($dados_stm["timezone"] == "America/Argentina/Ushuaia") { echo 'selected="selected"'; } ?> value="America/Argentina/Ushuaia">Ushuaia</option>
</optgroup>
<optgroup label="Armênia (AM)">
<option <?php if($dados_stm["timezone"] == "Asia/Yerevan") { echo 'selected="selected"'; } ?> value="Asia/Yerevan">Yerevan</option>
</optgroup>
<optgroup label="Aruba (AW)">
<option <?php if($dados_stm["timezone"] == "America/Aruba") { echo 'selected="selected"'; } ?> value="America/Aruba">Aruba</option>
</optgroup>
<optgroup label="Austrália (AU)">
<option <?php if($dados_stm["timezone"] == "Australia/Adelaide") { echo 'selected="selected"'; } ?> value="Australia/Adelaide">Adelaide</option>
<option <?php if($dados_stm["timezone"] == "Australia/Brisbane") { echo 'selected="selected"'; } ?> value="Australia/Brisbane">Brisbane</option>
<option <?php if($dados_stm["timezone"] == "Australia/Broken_Hill") { echo 'selected="selected"'; } ?> value="Australia/Broken_Hill">Broken Hill</option>
<option <?php if($dados_stm["timezone"] == "Australia/Currie") { echo 'selected="selected"'; } ?> value="Australia/Currie">Currie</option>
<option <?php if($dados_stm["timezone"] == "Australia/Darwin") { echo 'selected="selected"'; } ?> value="Australia/Darwin">Darwin</option>
<option <?php if($dados_stm["timezone"] == "Australia/Eucla") { echo 'selected="selected"'; } ?> value="Australia/Eucla">Eucla</option>
<option <?php if($dados_stm["timezone"] == "Australia/Hobart") { echo 'selected="selected"'; } ?> value="Australia/Hobart">Hobart</option>
<option <?php if($dados_stm["timezone"] == "Australia/Lindeman") { echo 'selected="selected"'; } ?> value="Australia/Lindeman">Lindeman</option>
<option <?php if($dados_stm["timezone"] == "Australia/Lord_Howe") { echo 'selected="selected"'; } ?> value="Australia/Lord_Howe">Lord Howe</option>
<option <?php if($dados_stm["timezone"] == "Australia/Macquarie") { echo 'selected="selected"'; } ?> value="Antarctica/Macquarie">Macquarie</option>
<option <?php if($dados_stm["timezone"] == "Australia/Melbourne") { echo 'selected="selected"'; } ?> value="Australia/Melbourne">Melbourne</option>
<option <?php if($dados_stm["timezone"] == "Australia/Perth") { echo 'selected="selected"'; } ?> value="Australia/Perth">Perth</option>
<option <?php if($dados_stm["timezone"] == "Australia/Sydney") { echo 'selected="selected"'; } ?> value="Australia/Sydney">Sydney</option>
</optgroup>
<optgroup label="Áustria (AT)">
<option <?php if($dados_stm["timezone"] == "Europe/Vienna") { echo 'selected="selected"'; } ?> value="Europe/Vienna">Viena</option>
</optgroup>
<optgroup label="Azerbaijão (AZ)">
<option <?php if($dados_stm["timezone"] == "Asia/Baku") { echo 'selected="selected"'; } ?> value="Asia/Baku">Baku</option>
</optgroup>
<optgroup label="Bahamas (BS)">
<option <?php if($dados_stm["timezone"] == "America/Nassau") { echo 'selected="selected"'; } ?> value="America/Nassau">Nassau</option>
</optgroup>
<optgroup label="Bahrein (BH)">
<option <?php if($dados_stm["timezone"] == "Asia/Bahrain") { echo 'selected="selected"'; } ?> value="Asia/Bahrain">Bahrain</option>
</optgroup>
<optgroup label="Bangladesh (BD)">
<option <?php if($dados_stm["timezone"] == "Asia/Dhaka") { echo 'selected="selected"'; } ?> value="Asia/Dhaka">Dhaka</option>
</optgroup>
<optgroup label="Barbados (BB)">
<option <?php if($dados_stm["timezone"] == "America/Barbados") { echo 'selected="selected"'; } ?> value="America/Barbados">Barbados</option>
</optgroup>
<optgroup label="Bélgica (BE)">
<option <?php if($dados_stm["timezone"] == "Europe/Brussels") { echo 'selected="selected"'; } ?> value="Europe/Brussels">Bruxelas</option>
</optgroup>
<optgroup label="Belize (BZ)">
<option <?php if($dados_stm["timezone"] == "America/Belize") { echo 'selected="selected"'; } ?> value="America/Belize">Belize</option>
</optgroup>
<optgroup label="Benim (BJ)">
<option <?php if($dados_stm["timezone"] == "Africa/Porto-Novo") { echo 'selected="selected"'; } ?> value="Africa/Porto-Novo">Porto-Novo</option>
</optgroup>
<optgroup label="Bermudas (BM)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Bermuda") { echo 'selected="selected"'; } ?> value="Atlantic/Bermuda">Bermuda</option>
</optgroup>
<optgroup label="Bielorrússia (BY)">
<option <?php if($dados_stm["timezone"] == "Europe/Minsk") { echo 'selected="selected"'; } ?> value="Europe/Minsk">Minsk</option>
</optgroup>
<optgroup label="Bolívia (BO)">
<option <?php if($dados_stm["timezone"] == "America/La_Paz") { echo 'selected="selected"'; } ?> value="America/La_Paz">La Paz</option>
</optgroup>
<optgroup label="Bonaire, Santo Eustáquio e Saba (BQ)">
<option <?php if($dados_stm["timezone"] == "America/Kralendijk") { echo 'selected="selected"'; } ?> value="America/Kralendijk">Kralendijk</option>
</optgroup>
<optgroup label="Bósnia e Herzegovina (BA)">
<option <?php if($dados_stm["timezone"] == "Europe/Sarajevo") { echo 'selected="selected"'; } ?> value="Europe/Sarajevo">Sarajevo</option>
</optgroup>
<optgroup label="Botswana (BW)">
<option <?php if($dados_stm["timezone"] == "Africa/Gaborone") { echo 'selected="selected"'; } ?> value="Africa/Gaborone">Gaborone</option>
</optgroup>
<optgroup label="Brasil (BR)">
<option <?php if($dados_stm["timezone"] == "America/Araguaina") { echo 'selected="selected"'; } ?> value="America/Araguaina">Araguaína</option>
<option <?php if($dados_stm["timezone"] == "America/Bahia") { echo 'selected="selected"'; } ?> value="America/Bahia">Bahia</option>
<option <?php if($dados_stm["timezone"] == "America/Belem") { echo 'selected="selected"'; } ?> value="America/Belem">Belém</option>
<option <?php if($dados_stm["timezone"] == "America/Boa_Vista") { echo 'selected="selected"'; } ?> value="America/Boa_Vista">Boa Vista</option>
<option <?php if($dados_stm["timezone"] == "America/Campo_Grande") { echo 'selected="selected"'; } ?> value="America/Campo_Grande">Campo Grande</option>
<option <?php if($dados_stm["timezone"] == "America/Cuiaba") { echo 'selected="selected"'; } ?> value="America/Cuiaba">Cuiabá</option>
<option <?php if($dados_stm["timezone"] == "America/Eirunepe") { echo 'selected="selected"'; } ?> value="America/Eirunepe">Eirunepé</option>
<option <?php if($dados_stm["timezone"] == "America/Noronha") { echo 'selected="selected"'; } ?> value="America/Noronha">Fernando de Noronha</option>
<option <?php if($dados_stm["timezone"] == "America/Fortaleza") { echo 'selected="selected"'; } ?> value="America/Fortaleza">Fortaleza</option>
<option <?php if($dados_stm["timezone"] == "America/Maceio") { echo 'selected="selected"'; } ?> value="America/Maceio">Maceió</option>
<option <?php if($dados_stm["timezone"] == "America/Manaus") { echo 'selected="selected"'; } ?> value="America/Manaus">Manaus</option>
<option <?php if($dados_stm["timezone"] == "America/Porto_Velho") { echo 'selected="selected"'; } ?> value="America/Porto_Velho">Porto Velho</option>
<option <?php if($dados_stm["timezone"] == "America/Recife") { echo 'selected="selected"'; } ?> value="America/Recife">Recife</option>
<option <?php if($dados_stm["timezone"] == "America/Rio_Branco") { echo 'selected="selected"'; } ?> value="America/Rio_Branco">Rio Branco</option>
<option <?php if($dados_stm["timezone"] == "America/Santarem") { echo 'selected="selected"'; } ?> value="America/Santarem">Santarém</option>
<option <?php if($dados_stm["timezone"] == "America/Sao_Paulo") { echo 'selected="selected"'; } ?> value="America/Sao_Paulo">São Paulo (Horário de Brasilia)</option>
</optgroup>
<optgroup label="Brunei (BN)">
<option <?php if($dados_stm["timezone"] == "Asia/Brunei") { echo 'selected="selected"'; } ?> value="Asia/Brunei">Brunei</option>
</optgroup>
<optgroup label="Bulgária (BG)">
<option <?php if($dados_stm["timezone"] == "Europe/Sofia") { echo 'selected="selected"'; } ?> value="Europe/Sofia">Sofia</option>
</optgroup>
<optgroup label="Burkina Faso (BF)">
<option <?php if($dados_stm["timezone"] == "Africa/Ouagadougou") { echo 'selected="selected"'; } ?> value="Africa/Ouagadougou">Ouagadougou</option>
</optgroup>
<optgroup label="Burúndi (BI)">
<option <?php if($dados_stm["timezone"] == "Africa/Bujumbura") { echo 'selected="selected"'; } ?> value="Africa/Bujumbura">Bujumbura</option>
</optgroup>
<optgroup label="Butão (BT)">
<option <?php if($dados_stm["timezone"] == "Asia/Thimphu") { echo 'selected="selected"'; } ?> value="Asia/Thimphu">Thimphu</option>
</optgroup>
<optgroup label="Cabo Verde (CV)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Cape_Verde") { echo 'selected="selected"'; } ?> value="Atlantic/Cape_Verde">Cabo Verde</option>
</optgroup>
<optgroup label="Camarões (CM)">
<option <?php if($dados_stm["timezone"] == "Africa/Douala") { echo 'selected="selected"'; } ?> value="Africa/Douala">Douala</option>
</optgroup>
<optgroup label="Camboja (KH)">
<option <?php if($dados_stm["timezone"] == "Asia/Phnom_Penh") { echo 'selected="selected"'; } ?> value="Asia/Phnom_Penh">Phnom Penh</option>
</optgroup>
<optgroup label="Canadá (CA)">
<option <?php if($dados_stm["timezone"] == "America/Atikokan") { echo 'selected="selected"'; } ?> value="America/Atikokan">Atikokan</option>
<option <?php if($dados_stm["timezone"] == "America/Blanc-Sablon") { echo 'selected="selected"'; } ?> value="America/Blanc-Sablon">Blanc-Sablon</option>
<option <?php if($dados_stm["timezone"] == "America/Cambridge_Bay") { echo 'selected="selected"'; } ?> value="America/Cambridge_Bay">Cambridge Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Creston") { echo 'selected="selected"'; } ?> value="America/Creston">Creston</option>
<option <?php if($dados_stm["timezone"] == "America/Dawson") { echo 'selected="selected"'; } ?> value="America/Dawson">Dawson</option>
<option <?php if($dados_stm["timezone"] == "America/Dawson_Creek") { echo 'selected="selected"'; } ?> value="America/Dawson_Creek">Dawson Creek</option>
<option <?php if($dados_stm["timezone"] == "America/Edmonton") { echo 'selected="selected"'; } ?> value="America/Edmonton">Edmonton</option>
<option <?php if($dados_stm["timezone"] == "America/Glace_Bay") { echo 'selected="selected"'; } ?> value="America/Glace_Bay">Glace Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Goose_Bay") { echo 'selected="selected"'; } ?> value="America/Goose_Bay">Goose Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Halifax") { echo 'selected="selected"'; } ?> value="America/Halifax">Halifax</option>
<option <?php if($dados_stm["timezone"] == "America/Inuvik") { echo 'selected="selected"'; } ?> value="America/Inuvik">Inuvik</option>
<option <?php if($dados_stm["timezone"] == "America/Iqaluit") { echo 'selected="selected"'; } ?> value="America/Iqaluit">Iqaluit</option>
<option <?php if($dados_stm["timezone"] == "America/Moncton") { echo 'selected="selected"'; } ?> value="America/Moncton">Moncton</option>
<option <?php if($dados_stm["timezone"] == "America/Montreal") { echo 'selected="selected"'; } ?> value="America/Montreal">Montreal</option>
<option <?php if($dados_stm["timezone"] == "America/Nipigon") { echo 'selected="selected"'; } ?> value="America/Nipigon">Nipigon</option>
<option <?php if($dados_stm["timezone"] == "America/Pangnirtung") { echo 'selected="selected"'; } ?> value="America/Pangnirtung">Pangnirtung</option>
<option <?php if($dados_stm["timezone"] == "America/Rainy_River") { echo 'selected="selected"'; } ?> value="America/Rainy_River">Rainy River</option>
<option <?php if($dados_stm["timezone"] == "America/Rankin_Inlet") { echo 'selected="selected"'; } ?> value="America/Rankin_Inlet">Rankin Inlet</option>
<option <?php if($dados_stm["timezone"] == "America/Regina") { echo 'selected="selected"'; } ?> value="America/Regina">Regina</option>
<option <?php if($dados_stm["timezone"] == "America/Resolute") { echo 'selected="selected"'; } ?> value="America/Resolute">Resolute</option>
<option <?php if($dados_stm["timezone"] == "America/St_Johns") { echo 'selected="selected"'; } ?> value="America/St_Johns">St Johns</option>
<option <?php if($dados_stm["timezone"] == "America/Swift_Current") { echo 'selected="selected"'; } ?> value="America/Swift_Current">Swift Current</option>
<option <?php if($dados_stm["timezone"] == "America/Thunder_Bay") { echo 'selected="selected"'; } ?> value="America/Thunder_Bay">Thunder Bay</option>
<option <?php if($dados_stm["timezone"] == "America/Toronto") { echo 'selected="selected"'; } ?> value="America/Toronto">Toronto</option>
<option <?php if($dados_stm["timezone"] == "America/Vancouver") { echo 'selected="selected"'; } ?> value="America/Vancouver">Vancouver</option>
<option <?php if($dados_stm["timezone"] == "America/Whitehorse") { echo 'selected="selected"'; } ?> value="America/Whitehorse">Whitehorse</option>
<option <?php if($dados_stm["timezone"] == "America/Winnipeg") { echo 'selected="selected"'; } ?> value="America/Winnipeg">Winnipeg</option>
<option <?php if($dados_stm["timezone"] == "America/Yellowknife") { echo 'selected="selected"'; } ?> value="America/Yellowknife">Yellowknife</option>
</optgroup>
<optgroup label="Catar (QA)">
<option <?php if($dados_stm["timezone"] == "Asia/Qatar") { echo 'selected="selected"'; } ?> value="Asia/Qatar">Qatar</option>
</optgroup>
<optgroup label="Cazaquistão (KZ)">
<option <?php if($dados_stm["timezone"] == "Asia/Almaty") { echo 'selected="selected"'; } ?> value="Asia/Almaty">Almaty</option>
<option <?php if($dados_stm["timezone"] == "Asia/Aqtau") { echo 'selected="selected"'; } ?> value="Asia/Aqtau">Aqtau</option>
<option <?php if($dados_stm["timezone"] == "Asia/Aqtobe") { echo 'selected="selected"'; } ?> value="Asia/Aqtobe">Aqtobe</option>
<option <?php if($dados_stm["timezone"] == "Asia/Oral") { echo 'selected="selected"'; } ?> value="Asia/Oral">Oral</option>
<option <?php if($dados_stm["timezone"] == "Asia/Qyzylorda") { echo 'selected="selected"'; } ?> value="Asia/Qyzylorda">Qyzylorda</option>
</optgroup>
<optgroup label="Chade (TD)">
<option <?php if($dados_stm["timezone"] == "Africa/Ndjamena") { echo 'selected="selected"'; } ?> value="Africa/Ndjamena">Ndjamena</option>
</optgroup>
<optgroup label="Chile (CL)">
<option <?php if($dados_stm["timezone"] == "Pacific/Easter") { echo 'selected="selected"'; } ?> value="Pacific/Easter">Páscoa</option>
<option <?php if($dados_stm["timezone"] == "America/Santiago") { echo 'selected="selected"'; } ?> value="America/Santiago">Santiago</option>
</optgroup>
<optgroup label="China (CN)">
<option <?php if($dados_stm["timezone"] == "Asia/Chongqing") { echo 'selected="selected"'; } ?> value="Asia/Chongqing">Chongqing</option>
<option <?php if($dados_stm["timezone"] == "Asia/Harbin") { echo 'selected="selected"'; } ?> value="Asia/Harbin">Harbin</option>
<option <?php if($dados_stm["timezone"] == "Asia/Kashgar") { echo 'selected="selected"'; } ?> value="Asia/Kashgar">Kashgar</option>
<option <?php if($dados_stm["timezone"] == "Asia/Urumqi") { echo 'selected="selected"'; } ?> value="Asia/Urumqi">Urumqi</option>
<option <?php if($dados_stm["timezone"] == "Asia/Shanghai") { echo 'selected="selected"'; } ?> value="Asia/Shanghai">Xangai</option>
</optgroup>
<optgroup label="Chipre (CY)">
<option <?php if($dados_stm["timezone"] == "Asia/Nicosia") { echo 'selected="selected"'; } ?> value="Asia/Nicosia">Nicosia</option>
</optgroup>
<optgroup label="Coletividade de São Martinho (MF)">
<option <?php if($dados_stm["timezone"] == "America/Marigot") { echo 'selected="selected"'; } ?> value="America/Marigot">Marigot</option>
</optgroup>
<optgroup label="Colômbia (CO)">
<option <?php if($dados_stm["timezone"] == "America/Bogota") { echo 'selected="selected"'; } ?> value="America/Bogota">Bogotá</option>
</optgroup>
<optgroup label="Comores (KM)">
<option <?php if($dados_stm["timezone"] == "Indian/Comoro") { echo 'selected="selected"'; } ?> value="Indian/Comoro">Comoro</option>
</optgroup>
<optgroup label="Coreia do Norte (KP)">
<option <?php if($dados_stm["timezone"] == "Asia/Pyongyang") { echo 'selected="selected"'; } ?> value="Asia/Pyongyang">Pyongyang</option>
</optgroup>
<optgroup label="Coreia do Sul (KR)">
<option <?php if($dados_stm["timezone"] == "Asia/Seoul") { echo 'selected="selected"'; } ?> value="Asia/Seoul">Seoul</option>
</optgroup>
<optgroup label="Costa do Marfim (CI)">
<option <?php if($dados_stm["timezone"] == "Africa/Abidjan") { echo 'selected="selected"'; } ?> value="Africa/Abidjan">Abidjan</option>
</optgroup>
<optgroup label="Costa Rica (CR)">
<option <?php if($dados_stm["timezone"] == "America/Costa_Rica") { echo 'selected="selected"'; } ?> value="America/Costa_Rica">Costa Rica</option>
</optgroup>
<optgroup label="Croácia (HR)">
<option <?php if($dados_stm["timezone"] == "Europe/Zagreb") { echo 'selected="selected"'; } ?> value="Europe/Zagreb">Zagreb</option>
</optgroup>
<optgroup label="Cuba (CU)">
<option <?php if($dados_stm["timezone"] == "America/Havana") { echo 'selected="selected"'; } ?> value="America/Havana">Havana</option>
</optgroup>
<optgroup label="Curaçao (CW)">
<option <?php if($dados_stm["timezone"] == "America/Curacao") { echo 'selected="selected"'; } ?> value="America/Curacao">Curaçao</option>
</optgroup>
<optgroup label="Dinamarca (DK)">
<option <?php if($dados_stm["timezone"] == "Europe/Copenhagen") { echo 'selected="selected"'; } ?> value="Europe/Copenhagen">Copenhaga</option>
</optgroup>
<optgroup label="Domínica (DM)">
<option <?php if($dados_stm["timezone"] == "America/Dominica") { echo 'selected="selected"'; } ?> value="America/Dominica">Dominica</option>
</optgroup>
<optgroup label="Egito (EG)">
<option <?php if($dados_stm["timezone"] == "Africa/Cairo") { echo 'selected="selected"'; } ?> value="Africa/Cairo">Cairo</option>
</optgroup>
<optgroup label="El Salvador (SV)">
<option <?php if($dados_stm["timezone"] == "America/El_Salvador") { echo 'selected="selected"'; } ?> value="America/El_Salvador">El Salvador</option>
</optgroup>
<optgroup label="Emirados Árabes Unidos (AE)">
<option <?php if($dados_stm["timezone"] == "Asia/Dubai") { echo 'selected="selected"'; } ?> value="Asia/Dubai">Dubai</option>
</optgroup>
<optgroup label="Equador (EC)">
<option <?php if($dados_stm["timezone"] == "Pacific/Galapagos") { echo 'selected="selected"'; } ?> value="Pacific/Galapagos">Galápagos</option>
<option <?php if($dados_stm["timezone"] == "America/Guayaquil") { echo 'selected="selected"'; } ?> value="America/Guayaquil">Guayaquil</option>
</optgroup>
<optgroup label="Eritreia (ER)">
<option <?php if($dados_stm["timezone"] == "Africa/Asmara") { echo 'selected="selected"'; } ?> value="Africa/Asmara">Asmara</option>
</optgroup>
<optgroup label="Eslováquia (SK)">
<option <?php if($dados_stm["timezone"] == "Europe/Bratislava") { echo 'selected="selected"'; } ?> value="Europe/Bratislava">Bratislava</option>
</optgroup>
<optgroup label="Eslovênia (SI)">
<option <?php if($dados_stm["timezone"] == "Europe/Ljubljana") { echo 'selected="selected"'; } ?> value="Europe/Ljubljana">Ljubljana</option>
</optgroup>
<optgroup label="Espanha (ES)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Canary") { echo 'selected="selected"'; } ?> value="Atlantic/Canary">Canárias</option>
<option <?php if($dados_stm["timezone"] == "Africa/Ceuta") { echo 'selected="selected"'; } ?> value="Africa/Ceuta">Ceuta</option>
<option <?php if($dados_stm["timezone"] == "Europe/Madrid") { echo 'selected="selected"'; } ?> value="Europe/Madrid">Madrid</option>
</optgroup>
<optgroup label="Estados Unidos (US)">
<option <?php if($dados_stm["timezone"] == "America/Adak") { echo 'selected="selected"'; } ?> value="America/Adak">Adak</option>
<option <?php if($dados_stm["timezone"] == "America/Anchorage") { echo 'selected="selected"'; } ?> value="America/Anchorage">Anchorage</option>
<option <?php if($dados_stm["timezone"] == "America/Boise") { echo 'selected="selected"'; } ?> value="America/Boise">Boise</option>
<option <?php if($dados_stm["timezone"] == "America/Chicago") { echo 'selected="selected"'; } ?> value="America/Chicago">Chicago</option>
<option <?php if($dados_stm["timezone"] == "America/North_Dakota/Beulah") { echo 'selected="selected"'; } ?> value="America/North_Dakota/Beulah">Dakota do Norte - Beulah</option>
<option <?php if($dados_stm["timezone"] == "America/North_Dakota/Center") { echo 'selected="selected"'; } ?> value="America/North_Dakota/Center">Dakota do Norte - Center</option>
<option <?php if($dados_stm["timezone"] == "America/North_Dakota/New_Salem") { echo 'selected="selected"'; } ?> value="America/North_Dakota/New_Salem">Dakota do Norte - New Salem</option>
<option <?php if($dados_stm["timezone"] == "America/Denver") { echo 'selected="selected"'; } ?> value="America/Denver">Denver</option>
<option <?php if($dados_stm["timezone"] == "America/Detroit") { echo 'selected="selected"'; } ?> value="America/Detroit">Detroit</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Honolulu") { echo 'selected="selected"'; } ?> value="Pacific/Honolulu">Honolulu</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Indianapolis") { echo 'selected="selected"'; } ?> value="America/Indiana/Indianapolis">Indiana - Indianápolis</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Knox") { echo 'selected="selected"'; } ?> value="America/Indiana/Knox">Indiana - Knox</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Marengo") { echo 'selected="selected"'; } ?> value="America/Indiana/Marengo">Indiana - Marengo</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Petersburg") { echo 'selected="selected"'; } ?> value="America/Indiana/Petersburg">Indiana - Petersburg</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Tell_City") { echo 'selected="selected"'; } ?> value="America/Indiana/Tell_City">Indiana - Tell City</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Vevay") { echo 'selected="selected"'; } ?> value="America/Indiana/Vevay">Indiana - Vevay</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Vincennes") { echo 'selected="selected"'; } ?> value="America/Indiana/Vincennes">Indiana - Vincennes</option>
<option <?php if($dados_stm["timezone"] == "America/Indiana/Winamac") { echo 'selected="selected"'; } ?> value="America/Indiana/Winamac">Indiana - Winamac</option>
<option <?php if($dados_stm["timezone"] == "America/Juneau") { echo 'selected="selected"'; } ?> value="America/Juneau">Juneau</option>
<option <?php if($dados_stm["timezone"] == "America/Kentucky/Louisville") { echo 'selected="selected"'; } ?> value="America/Kentucky/Louisville">Kentucky - Louisville</option>
<option <?php if($dados_stm["timezone"] == "America/Kentucky/Monticello") { echo 'selected="selected"'; } ?> value="America/Kentucky/Monticello">Kentucky - Monticello</option>
<option <?php if($dados_stm["timezone"] == "America/Los_Angeles") { echo 'selected="selected"'; } ?> value="America/Los_Angeles">Los Angeles</option>
<option <?php if($dados_stm["timezone"] == "America/Menominee") { echo 'selected="selected"'; } ?> value="America/Menominee">Menominee</option>
<option <?php if($dados_stm["timezone"] == "America/Metlakatla") { echo 'selected="selected"'; } ?> value="America/Metlakatla">Metlakatla</option>
<option <?php if($dados_stm["timezone"] == "America/Nome") { echo 'selected="selected"'; } ?> value="America/Nome">Nome</option>
<option <?php if($dados_stm["timezone"] == "America/New_York") { echo 'selected="selected"'; } ?> value="America/New_York">Nova Iorque</option>
<option <?php if($dados_stm["timezone"] == "America/Phoenix") { echo 'selected="selected"'; } ?> value="America/Phoenix">Phoenix</option>
<option <?php if($dados_stm["timezone"] == "America/Shiprock") { echo 'selected="selected"'; } ?> value="America/Shiprock">Shiprock</option>
<option <?php if($dados_stm["timezone"] == "America/Sitka") { echo 'selected="selected"'; } ?> value="America/Sitka">Sitka</option>
<option <?php if($dados_stm["timezone"] == "America/Yakutat") { echo 'selected="selected"'; } ?> value="America/Yakutat">Yakutat</option>
</optgroup>
<optgroup label="Estónia (EE)">
<option <?php if($dados_stm["timezone"] == "Europe/Tallinn") { echo 'selected="selected"'; } ?> value="Europe/Tallinn">Tallinn</option>
</optgroup>
<optgroup label="Etiópia (ET)">
<option <?php if($dados_stm["timezone"] == "Africa/Addis_Ababa") { echo 'selected="selected"'; } ?> value="Africa/Addis_Ababa">Adis Abeba</option>
</optgroup>
<optgroup label="Fiji (FJ)">
<option <?php if($dados_stm["timezone"] == "Pacific/Fiji") { echo 'selected="selected"'; } ?> value="Pacific/Fiji">Fiji</option>
</optgroup>
<optgroup label="Filipinas (PH)">
<option <?php if($dados_stm["timezone"] == "Asia/Manila") { echo 'selected="selected"'; } ?> value="Asia/Manila">Manila</option>
</optgroup>
<optgroup label="Finlândia (FI)">
<option <?php if($dados_stm["timezone"] == "Europe/Helsinki") { echo 'selected="selected"'; } ?> value="Europe/Helsinki">Helsínquia</option>
</optgroup>
<optgroup label="França (FR)">
<option <?php if($dados_stm["timezone"] == "Europe/Paris") { echo 'selected="selected"'; } ?> value="Europe/Paris">Paris</option>
</optgroup>
<optgroup label="Gabão (GA)">
<option <?php if($dados_stm["timezone"] == "Africa/Libreville") { echo 'selected="selected"'; } ?> value="Africa/Libreville">Libreville</option>
</optgroup>
<optgroup label="Gâmbia (GM)">
<option <?php if($dados_stm["timezone"] == "Africa/Banjul") { echo 'selected="selected"'; } ?> value="Africa/Banjul">Banjul</option>
</optgroup>
<optgroup label="Gana (GH)">
<option <?php if($dados_stm["timezone"] == "Africa/Accra") { echo 'selected="selected"'; } ?> value="Africa/Accra">Accra</option>
</optgroup>
<optgroup label="Geórgia (GE)">
<option <?php if($dados_stm["timezone"] == "Asia/Tbilisi") { echo 'selected="selected"'; } ?> value="Asia/Tbilisi">Tbilisi</option>
</optgroup>
<optgroup label="Gibraltar (GI)">
<option <?php if($dados_stm["timezone"] == "Europe/Gibraltar") { echo 'selected="selected"'; } ?> value="Europe/Gibraltar">Gibraltar</option>
</optgroup>
<optgroup label="Granada (GD)">
<option <?php if($dados_stm["timezone"] == "America/Grenada") { echo 'selected="selected"'; } ?> value="America/Grenada">Grenada</option>
</optgroup>
<optgroup label="Grécia (GR)">
<option <?php if($dados_stm["timezone"] == "Europe/Athens") { echo 'selected="selected"'; } ?> value="Europe/Athens">Atenas</option>
</optgroup>
<optgroup label="Groelândia (GL)">
<option <?php if($dados_stm["timezone"] == "America/Danmarkshavn") { echo 'selected="selected"'; } ?> value="America/Danmarkshavn">Danmarkshavn</option>
<option <?php if($dados_stm["timezone"] == "America/Godthab") { echo 'selected="selected"'; } ?> value="America/Godthab">Godthab</option>
<option <?php if($dados_stm["timezone"] == "America/Scoresbysund") { echo 'selected="selected"'; } ?> value="America/Scoresbysund">Scoresbysund</option>
<option <?php if($dados_stm["timezone"] == "America/Thule") { echo 'selected="selected"'; } ?> value="America/Thule">Thule</option>
</optgroup>
<optgroup label="Guadalupe (GP)">
<option <?php if($dados_stm["timezone"] == "America/Guadeloupe") { echo 'selected="selected"'; } ?> value="America/Guadeloupe">Guadalupe</option>
</optgroup>
<optgroup label="Guam (GU)">
<option <?php if($dados_stm["timezone"] == "Pacific/Guam") { echo 'selected="selected"'; } ?> value="Pacific/Guam">Guam</option>
</optgroup>
<optgroup label="Guatemala (GT)">
<option <?php if($dados_stm["timezone"] == "America/Guatemala") { echo 'selected="selected"'; } ?> value="America/Guatemala">Guatemala</option>
</optgroup>
<optgroup label="Guernsey (GG)">
<option <?php if($dados_stm["timezone"] == "Europe/Guernsey") { echo 'selected="selected"'; } ?> value="Europe/Guernsey">Guernsey</option>
</optgroup>
<optgroup label="Guiana (GY)">
<option <?php if($dados_stm["timezone"] == "America/Guyana") { echo 'selected="selected"'; } ?> value="America/Guyana">Guiana</option>
</optgroup>
<optgroup label="Guiana Francesa (GF)">
<option <?php if($dados_stm["timezone"] == "America/Cayenne") { echo 'selected="selected"'; } ?> value="America/Cayenne">Cayenne</option>
</optgroup>
<optgroup label="Guiné (GN)">
<option <?php if($dados_stm["timezone"] == "Africa/Conakry") { echo 'selected="selected"'; } ?> value="Africa/Conakry">Conakry</option>
</optgroup>
<optgroup label="Guiné Equatorial (GQ)">
<option <?php if($dados_stm["timezone"] == "Africa/Malabo") { echo 'selected="selected"'; } ?> value="Africa/Malabo">Malabo</option>
</optgroup>
<optgroup label="Guiné-Bissau (GW)">
<option <?php if($dados_stm["timezone"] == "Africa/Bissau") { echo 'selected="selected"'; } ?> value="Africa/Bissau">Bissau</option>
</optgroup>
<optgroup label="Haiti (HT)">
<option <?php if($dados_stm["timezone"] == "America/Port-au-Prince") { echo 'selected="selected"'; } ?> value="America/Port-au-Prince">Port-au-Prince</option>
</optgroup>
<optgroup label="Honduras (HN)">
<option <?php if($dados_stm["timezone"] == "America/Tegucigalpa") { echo 'selected="selected"'; } ?> value="America/Tegucigalpa">Tegucigalpa</option>
</optgroup>
<optgroup label="Hong Kong (HK)">
<option <?php if($dados_stm["timezone"] == "Asia/Hong_Kong") { echo 'selected="selected"'; } ?> value="Asia/Hong_Kong">Hong Kong</option>
</optgroup>
<optgroup label="Hungria (HU)">
<option <?php if($dados_stm["timezone"] == "Europe/Budapest") { echo 'selected="selected"'; } ?> value="Europe/Budapest">Budapeste</option>
</optgroup>
<optgroup label="Iêmen (YE)">
<option <?php if($dados_stm["timezone"] == "Asia/Aden") { echo 'selected="selected"'; } ?> value="Asia/Aden">Aden</option>
</optgroup>
<optgroup label="Ilha Bouvet (BV)"></optgroup>
<optgroup label="Ilha Christmas (CX)">
<option <?php if($dados_stm["timezone"] == "Indian/Christmas") { echo 'selected="selected"'; } ?> value="Indian/Christmas">Natal</option>
</optgroup>
<optgroup label="Ilha de Man (IM)">
<option <?php if($dados_stm["timezone"] == "Europe/Isle_of_Man") { echo 'selected="selected"'; } ?> value="Europe/Isle_of_Man">Ilha de Man</option>
</optgroup>
<optgroup label="Ilha Heard e Ilhas McDonald (HM)"></optgroup>
<optgroup label="Ilha Norfolk (NF)">
<option <?php if($dados_stm["timezone"] == "Pacific/Norfolk") { echo 'selected="selected"'; } ?> value="Pacific/Norfolk">Norfolk</option>
</optgroup>
<optgroup label="Ilhas Aland (AX)">
<option <?php if($dados_stm["timezone"] == "Europe/Mariehamn") { echo 'selected="selected"'; } ?> value="Europe/Mariehamn">Mariehamn</option>
</optgroup>
<optgroup label="Ilhas Cayman (KY)">
<option <?php if($dados_stm["timezone"] == "America/Cayman") { echo 'selected="selected"'; } ?> value="America/Cayman">Ilhas Cayman</option>
</optgroup>
<optgroup label="Ilhas Cocos (CC)">
<option <?php if($dados_stm["timezone"] == "Indian/Cocos") { echo 'selected="selected"'; } ?> value="Indian/Cocos">Cocos</option>
</optgroup>
<optgroup label="Ilhas Cook (CK)">
<option <?php if($dados_stm["timezone"] == "Pacific/Rarotonga") { echo 'selected="selected"'; } ?> value="Pacific/Rarotonga">Rarotonga</option>
</optgroup>
<optgroup label="Ilhas Feroé (FO)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Faroe") { echo 'selected="selected"'; } ?> value="Atlantic/Faroe">Faroé</option>
</optgroup>
<optgroup label="Ilhas Geórgia do Sul e Sandwich do Sul (GS)">
<option <?php if($dados_stm["timezone"] == "Atlantic/South_Georgia") { echo 'selected="selected"'; } ?> value="Atlantic/South_Georgia">South Georgia</option>
</optgroup>
<optgroup label="Ilhas Malvinas (FK)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Stanley") { echo 'selected="selected"'; } ?> value="Atlantic/Stanley">Stanley</option>
</optgroup>
<optgroup label="Ilhas Marianas do Norte (MP)">
<option <?php if($dados_stm["timezone"] == "Pacific/Saipan") { echo 'selected="selected"'; } ?> value="Pacific/Saipan">Saipan</option>
</optgroup>
<optgroup label="Ilhas Marshall (MH)">
<option <?php if($dados_stm["timezone"] == "Pacific/Kwajalein") { echo 'selected="selected"'; } ?> value="Pacific/Kwajalein">Kwajalein</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Majuro") { echo 'selected="selected"'; } ?> value="Pacific/Majuro">Majuro</option>
</optgroup>
<optgroup label="Ilhas Menores Distantes dos Estados Unidos (UM)">
<option <?php if($dados_stm["timezone"] == "Pacific/Johnston") { echo 'selected="selected"'; } ?> value="Pacific/Johnston">Johnston</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Midway") { echo 'selected="selected"'; } ?> value="Pacific/Midway">Midway</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Wake") { echo 'selected="selected"'; } ?> value="Pacific/Wake">Wake</option>
</optgroup>
<optgroup label="Ilhas Pitcairn (PN)">
<option <?php if($dados_stm["timezone"] == "Pacific/Pitcairn") { echo 'selected="selected"'; } ?> value="Pacific/Pitcairn">Pitcairn</option>
</optgroup>
<optgroup label="Ilhas Salomão (SB)">
<option <?php if($dados_stm["timezone"] == "Pacific/Guadalcanal") { echo 'selected="selected"'; } ?> value="Pacific/Guadalcanal">Guadalcanal</option>
</optgroup>
<optgroup label="Ilhas Turcas e Caicos (TC)">
<option <?php if($dados_stm["timezone"] == "America/Grand_Turk") { echo 'selected="selected"'; } ?> value="America/Grand_Turk">Grand Turk</option>
</optgroup>
<optgroup label="Ilhas Virgens Americanas (VI)">
<option <?php if($dados_stm["timezone"] == "America/St_Thomas") { echo 'selected="selected"'; } ?> value="America/St_Thomas">St Thomas</option>
</optgroup>
<optgroup label="Ilhas Virgens Britânicas (VG)">
<option <?php if($dados_stm["timezone"] == "America/Tortola") { echo 'selected="selected"'; } ?> value="America/Tortola">Tortola</option>
</optgroup>
<optgroup label="Índia (IN)">
<option <?php if($dados_stm["timezone"] == "Asia/Kolkata") { echo 'selected="selected"'; } ?> value="Asia/Kolkata">Kolkata</option>
</optgroup>
<optgroup label="Indonésia (ID)">
<option <?php if($dados_stm["timezone"] == "Asia/Jakarta") { echo 'selected="selected"'; } ?> value="Asia/Jakarta">Jakarta</option>
<option <?php if($dados_stm["timezone"] == "Asia/Jayapura") { echo 'selected="selected"'; } ?> value="Asia/Jayapura">Jayapura</option>
<option <?php if($dados_stm["timezone"] == "Asia/Makassar") { echo 'selected="selected"'; } ?> value="Asia/Makassar">Makassar</option>
<option <?php if($dados_stm["timezone"] == "Asia/Pontianak") { echo 'selected="selected"'; } ?> value="Asia/Pontianak">Pontianak</option>
</optgroup>
<optgroup label="Irã (IR)">
<option <?php if($dados_stm["timezone"] == "Asia/Tehran") { echo 'selected="selected"'; } ?> value="Asia/Tehran">Teerão</option>
</optgroup>
<optgroup label="Iraque (IQ)">
<option <?php if($dados_stm["timezone"] == "Asia/Baghdad") { echo 'selected="selected"'; } ?> value="Asia/Baghdad">Bagdade</option>
</optgroup>
<optgroup label="Irlanda (IE)">
<option <?php if($dados_stm["timezone"] == "Europe/Dublin") { echo 'selected="selected"'; } ?> value="Europe/Dublin">Dublim</option>
</optgroup>
<optgroup label="Islândia (IS)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Reykjavik") { echo 'selected="selected"'; } ?> value="Atlantic/Reykjavik">Reykjavik</option>
</optgroup>
<optgroup label="Israel (IL)">
<option <?php if($dados_stm["timezone"] == "Asia/Jerusalem") { echo 'selected="selected"'; } ?> value="Asia/Jerusalem">Jerusalém</option>
</optgroup>
<optgroup label="Itália (IT)">
<option <?php if($dados_stm["timezone"] == "Europe/Rome") { echo 'selected="selected"'; } ?> value="Europe/Rome">Roma</option>
</optgroup>
<optgroup label="Jamaica (JM)">
<option <?php if($dados_stm["timezone"] == "America/Jamaica") { echo 'selected="selected"'; } ?> value="America/Jamaica">Jamaica</option>
</optgroup>
<optgroup label="Japão (JP)">
<option <?php if($dados_stm["timezone"] == "Asia/Tokyo") { echo 'selected="selected"'; } ?> value="Asia/Tokyo">Tóquio</option>
</optgroup>
<optgroup label="Jersey (JE)">
<option <?php if($dados_stm["timezone"] == "Europe/Jersey") { echo 'selected="selected"'; } ?> value="Europe/Jersey">Jersey</option>
</optgroup>
<optgroup label="Jibuti (DJ)">
<option <?php if($dados_stm["timezone"] == "Africa/Djibouti") { echo 'selected="selected"'; } ?> value="Africa/Djibouti">Djibuti</option>
</optgroup>
<optgroup label="Jordânia (JO)">
<option <?php if($dados_stm["timezone"] == "Asia/Amman") { echo 'selected="selected"'; } ?> value="Asia/Amman">Amã</option>
</optgroup>
<optgroup label="Kiribati (KI)">
<option <?php if($dados_stm["timezone"] == "Pacific/Enderbury") { echo 'selected="selected"'; } ?> value="Pacific/Enderbury">Enderbury</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Kiritimati") { echo 'selected="selected"'; } ?> value="Pacific/Kiritimati">Kiritimati</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Tarawa") { echo 'selected="selected"'; } ?> value="Pacific/Tarawa">Tarawa</option>
</optgroup>
<optgroup label="Kosovo (XK)"></optgroup>
<optgroup label="Kuwait (KW)">
<option <?php if($dados_stm["timezone"] == "Asia/Kuwait") { echo 'selected="selected"'; } ?> value="Asia/Kuwait">Kuwait</option>
</optgroup>
<optgroup label="Laos (LA)">
<option <?php if($dados_stm["timezone"] == "Asia/Vientiane") { echo 'selected="selected"'; } ?> value="Asia/Vientiane">Vientiane</option>
</optgroup>
<optgroup label="Lesoto (LS)">
<option <?php if($dados_stm["timezone"] == "Africa/Maseru") { echo 'selected="selected"'; } ?> value="Africa/Maseru">Maseru</option>
</optgroup>
<optgroup label="Letônia (LV)">
<option <?php if($dados_stm["timezone"] == "Europe/Riga") { echo 'selected="selected"'; } ?> value="Europe/Riga">Riga</option>
</optgroup>
<optgroup label="Líbano (LB)">
<option <?php if($dados_stm["timezone"] == "Asia/Beirut") { echo 'selected="selected"'; } ?> value="Asia/Beirut">Beirute</option>
</optgroup>
<optgroup label="Libéria (LR)">
<option <?php if($dados_stm["timezone"] == "Africa/Monrovia") { echo 'selected="selected"'; } ?> value="Africa/Monrovia">Monrovia</option>
</optgroup>
<optgroup label="Líbia (LY)">
<option <?php if($dados_stm["timezone"] == "Africa/Tripoli") { echo 'selected="selected"'; } ?> value="Africa/Tripoli">Trípoli</option>
</optgroup>
<optgroup label="Liechtenstein (LI)">
<option <?php if($dados_stm["timezone"] == "Europe/Vaduz") { echo 'selected="selected"'; } ?> value="Europe/Vaduz">Vaduz</option>
</optgroup>
<optgroup label="Lituânia (LT)">
<option <?php if($dados_stm["timezone"] == "Europe/Vilnius") { echo 'selected="selected"'; } ?> value="Europe/Vilnius">Vilnius</option>
</optgroup>
<optgroup label="Luxemburgo (LU)">
<option <?php if($dados_stm["timezone"] == "Europe/Luxembourg") { echo 'selected="selected"'; } ?> value="Europe/Luxembourg">Luxemburgo</option>
</optgroup>
<optgroup label="Macau (MO)">
<option <?php if($dados_stm["timezone"] == "Asia/Macau") { echo 'selected="selected"'; } ?> value="Asia/Macau">Macau</option>
</optgroup>
<optgroup label="Macedônia (MK)">
<option <?php if($dados_stm["timezone"] == "Europe/Skopje") { echo 'selected="selected"'; } ?> value="Europe/Skopje">Skopje</option>
</optgroup>
<optgroup label="Madagascar (MG)">
<option <?php if($dados_stm["timezone"] == "Indian/Antananarivo") { echo 'selected="selected"'; } ?> value="Indian/Antananarivo">Antananarivo</option>
</optgroup>
<optgroup label="Malásia (MY)">
<option <?php if($dados_stm["timezone"] == "Asia/Kuala_Lumpur") { echo 'selected="selected"'; } ?> value="Asia/Kuala_Lumpur">Kuala Lumpur</option>
<option <?php if($dados_stm["timezone"] == "Asia/Kuching") { echo 'selected="selected"'; } ?> value="Asia/Kuching">Kuching</option>
</optgroup>
<optgroup label="Malawi (MW)">
<option <?php if($dados_stm["timezone"] == "Africa/Blantyre") { echo 'selected="selected"'; } ?> value="Africa/Blantyre">Blantyre</option>
</optgroup>
<optgroup label="Maldivas (MV)">
<option <?php if($dados_stm["timezone"] == "Indian/Maldives") { echo 'selected="selected"'; } ?> value="Indian/Maldives">Maldivas</option>
</optgroup>
<optgroup label="Mali (ML)">
<option <?php if($dados_stm["timezone"] == "Africa/Bamako") { echo 'selected="selected"'; } ?> value="Africa/Bamako">Bamako</option>
</optgroup>
<optgroup label="Malta (MT)">
<option <?php if($dados_stm["timezone"] == "Europe/Malta") { echo 'selected="selected"'; } ?> value="Europe/Malta">Malta</option>
</optgroup>
<optgroup label="Marrocos (MA)">
<option <?php if($dados_stm["timezone"] == "Africa/Casablanca") { echo 'selected="selected"'; } ?> value="Africa/Casablanca">Casablanca</option>
</optgroup>
<optgroup label="Martinica (MQ)">
<option <?php if($dados_stm["timezone"] == "America/Martinique") { echo 'selected="selected"'; } ?> value="America/Martinique">Martinica</option>
</optgroup>
<optgroup label="Maurício (MU)">
<option <?php if($dados_stm["timezone"] == "Indian/Mauritius") { echo 'selected="selected"'; } ?> value="Indian/Mauritius">Maurícias</option>
</optgroup>
<optgroup label="Mauritânia (MR)">
<option <?php if($dados_stm["timezone"] == "Africa/Nouakchott") { echo 'selected="selected"'; } ?> value="Africa/Nouakchott">Nouakchott</option>
</optgroup>
<optgroup label="Mayotte (YT)">
<option <?php if($dados_stm["timezone"] == "Indian/Mayotte") { echo 'selected="selected"'; } ?> value="Indian/Mayotte">Mayotte</option>
</optgroup>
<optgroup label="México (MX)">
<option <?php if($dados_stm["timezone"] == "America/Bahia_Banderas") { echo 'selected="selected"'; } ?> value="America/Bahia_Banderas">Bahia Banderas</option>
<option <?php if($dados_stm["timezone"] == "America/Cancun") { echo 'selected="selected"'; } ?> value="America/Cancun">Cancún</option>
<option <?php if($dados_stm["timezone"] == "America/Chihuahua") { echo 'selected="selected"'; } ?> value="America/Chihuahua">Chihuahua</option>
<option <?php if($dados_stm["timezone"] == "America/Mexico_City") { echo 'selected="selected"'; } ?> value="America/Mexico_City">Cidade do México</option>
<option <?php if($dados_stm["timezone"] == "America/Hermosillo") { echo 'selected="selected"'; } ?> value="America/Hermosillo">Hermosillo</option>
<option <?php if($dados_stm["timezone"] == "America/Matamoros") { echo 'selected="selected"'; } ?> value="America/Matamoros">Matamoros</option>
<option <?php if($dados_stm["timezone"] == "America/Mazatlan") { echo 'selected="selected"'; } ?> value="America/Mazatlan">Mazatlan</option>
<option <?php if($dados_stm["timezone"] == "America/Merida") { echo 'selected="selected"'; } ?> value="America/Merida">Mérida</option>
<option <?php if($dados_stm["timezone"] == "America/Monterrey") { echo 'selected="selected"'; } ?> value="America/Monterrey">Monterrey</option>
<option <?php if($dados_stm["timezone"] == "America/Ojinaga") { echo 'selected="selected"'; } ?> value="America/Ojinaga">Ojinaga</option>
<option <?php if($dados_stm["timezone"] == "America/Santa_Isabel") { echo 'selected="selected"'; } ?> value="America/Santa_Isabel">Santa Isabel</option>
<option <?php if($dados_stm["timezone"] == "America/Tijuana") { echo 'selected="selected"'; } ?> value="America/Tijuana">Tijuana</option>
</optgroup>
<optgroup label="Micronésia (FM)">
<option <?php if($dados_stm["timezone"] == "Pacific/Chuuk") { echo 'selected="selected"'; } ?> value="Pacific/Chuuk">Chuuk</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Kosrae") { echo 'selected="selected"'; } ?> value="Pacific/Kosrae">Kosrae</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Pohnpei") { echo 'selected="selected"'; } ?> value="Pacific/Pohnpei">Pohnpei</option>
</optgroup>
<optgroup label="Moçambique (MZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Maputo") { echo 'selected="selected"'; } ?> value="Africa/Maputo">Maputo</option>
</optgroup>
<optgroup label="Moldávia (MD)">
<option <?php if($dados_stm["timezone"] == "Europe/Chisinau") { echo 'selected="selected"'; } ?> value="Europe/Chisinau">Chisinau</option>
</optgroup>
<optgroup label="Mônaco (MC)">
<option <?php if($dados_stm["timezone"] == "Europe/Monaco") { echo 'selected="selected"'; } ?> value="Europe/Monaco">Mónaco</option>
</optgroup>
<optgroup label="Mongólia (MN)">
<option <?php if($dados_stm["timezone"] == "Asia/Choibalsan") { echo 'selected="selected"'; } ?> value="Asia/Choibalsan">Choibalsan</option>
<option <?php if($dados_stm["timezone"] == "Asia/Hovd") { echo 'selected="selected"'; } ?> value="Asia/Hovd">Hovd</option>
<option <?php if($dados_stm["timezone"] == "Asia/Ulaanbaatar") { echo 'selected="selected"'; } ?> value="Asia/Ulaanbaatar">Ulaanbaatar</option>
</optgroup>
<optgroup label="Montenegro (ME)">
<option <?php if($dados_stm["timezone"] == "Europe/Podgorica") { echo 'selected="selected"'; } ?> value="Europe/Podgorica">Podgorica</option>
</optgroup>
<optgroup label="Montserrat (MS)">
<option <?php if($dados_stm["timezone"] == "America/Montserrat") { echo 'selected="selected"'; } ?> value="America/Montserrat">Montserrat</option>
</optgroup>
<optgroup label="Myanmar (MM)">
<option <?php if($dados_stm["timezone"] == "Asia/Rangoon") { echo 'selected="selected"'; } ?> value="Asia/Rangoon">Rangoon</option>
</optgroup>
<optgroup label="Namíbia (NA)">
<option <?php if($dados_stm["timezone"] == "Africa/Windhoek") { echo 'selected="selected"'; } ?> value="Africa/Windhoek">Windhoek</option>
</optgroup>
<optgroup label="Nauru (NR)">
<option <?php if($dados_stm["timezone"] == "Pacific/Nauru") { echo 'selected="selected"'; } ?> value="Pacific/Nauru">Nauru</option>
</optgroup>
<optgroup label="Nepal (NP)">
<option <?php if($dados_stm["timezone"] == "Asia/Kathmandu") { echo 'selected="selected"'; } ?> value="Asia/Kathmandu">Kathmandu</option>
</optgroup>
<optgroup label="Nicarágua (NI)">
<option <?php if($dados_stm["timezone"] == "America/Managua") { echo 'selected="selected"'; } ?> value="America/Managua">Manágua</option>
</optgroup>
<optgroup label="Níger (NE)">
<option <?php if($dados_stm["timezone"] == "Africa/Niamey") { echo 'selected="selected"'; } ?> value="Africa/Niamey">Niamey</option>
</optgroup>
<optgroup label="Nigéria (NG)">
<option <?php if($dados_stm["timezone"] == "Africa/Lagos") { echo 'selected="selected"'; } ?> value="Africa/Lagos">Lagos</option>
</optgroup>
<optgroup label="Niue (NU)">
<option <?php if($dados_stm["timezone"] == "Pacific/Niue") { echo 'selected="selected"'; } ?> value="Pacific/Niue">Niue</option>
</optgroup>
<optgroup label="Noruega (NO)">
<option <?php if($dados_stm["timezone"] == "Europe/Oslo") { echo 'selected="selected"'; } ?> value="Europe/Oslo">Oslo</option>
</optgroup>
<optgroup label="Nova Caledônia (NC)">
<option <?php if($dados_stm["timezone"] == "Pacific/Noumea") { echo 'selected="selected"'; } ?> value="Pacific/Noumea">Noumea</option>
</optgroup>
<optgroup label="Nova Zelândia (NZ)">
<option <?php if($dados_stm["timezone"] == "Pacific/Auckland") { echo 'selected="selected"'; } ?> value="Pacific/Auckland">Auckland</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Chatham") { echo 'selected="selected"'; } ?> value="Pacific/Chatham">Chatham</option>
</optgroup>
<optgroup label="Omã (OM)">
<option <?php if($dados_stm["timezone"] == "Asia/Muscat") { echo 'selected="selected"'; } ?> value="Asia/Muscat">Muscat</option>
</optgroup>
<optgroup label="Países Baixos (NL)">

<option <?php if($dados_stm["timezone"] == "Europe/Amsterdam") { echo 'selected="selected"'; } ?> value="Europe/Amsterdam">Amsterdão</option>
</optgroup>
<optgroup label="Palau (PW)">
<option <?php if($dados_stm["timezone"] == "Pacific/Palau") { echo 'selected="selected"'; } ?> value="Pacific/Palau">Palau</option>
</optgroup>
<optgroup label="Panamá (PA)">
<option <?php if($dados_stm["timezone"] == "America/Panama") { echo 'selected="selected"'; } ?> value="America/Panama">Panamá</option>
</optgroup>
<optgroup label="Papua-Nova Guiné (PG)">
<option <?php if($dados_stm["timezone"] == "Pacific/Port_Moresby") { echo 'selected="selected"'; } ?> value="Pacific/Port_Moresby">Port Moresby</option>
</optgroup>
<optgroup label="Paquistão (PK)">
<option <?php if($dados_stm["timezone"] == "Asia/Karachi") { echo 'selected="selected"'; } ?> value="Asia/Karachi">Karachi</option>
</optgroup>
<optgroup label="Paraguai (PY)">
<option <?php if($dados_stm["timezone"] == "America/Asuncion") { echo 'selected="selected"'; } ?> value="America/Asuncion">Asunción</option>
</optgroup>
<optgroup label="Peru (PE)">
<option <?php if($dados_stm["timezone"] == "America/Lima") { echo 'selected="selected"'; } ?> value="America/Lima">Lima</option>
</optgroup>
<optgroup label="Polinésia Francesa (PF)">
<option <?php if($dados_stm["timezone"] == "Pacific/Gambier") { echo 'selected="selected"'; } ?> value="Pacific/Gambier">Gambier</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Marquesas") { echo 'selected="selected"'; } ?> value="Pacific/Marquesas">Marquesas</option>
<option <?php if($dados_stm["timezone"] == "Pacific/Tahiti") { echo 'selected="selected"'; } ?> value="Pacific/Tahiti">Tahiti</option>
</optgroup>
<optgroup label="Polônia (PL)">
<option <?php if($dados_stm["timezone"] == "Europe/Warsaw") { echo 'selected="selected"'; } ?> value="Europe/Warsaw">Varsóvia</option>
</optgroup>
<optgroup label="Porto Rico (PR)">
<option <?php if($dados_stm["timezone"] == "America/Puerto_Rico") { echo 'selected="selected"'; } ?> value="America/Puerto_Rico">Porto Rico</option>
</optgroup>
<optgroup label="Portugal (PT)">
<option <?php if($dados_stm["timezone"] == "Atlantic/Azores") { echo 'selected="selected"'; } ?> value="Atlantic/Azores">Açores</option>
<option <?php if($dados_stm["timezone"] == "Europe/Lisbon") { echo 'selected="selected"'; } ?> value="Europe/Lisbon">Lisboa</option>
<option <?php if($dados_stm["timezone"] == "Atlantic/Madeira") { echo 'selected="selected"'; } ?> value="Atlantic/Madeira">Madeira</option>
</optgroup>
<optgroup label="Quênia (KE)">
<option <?php if($dados_stm["timezone"] == "Africa/Nairobi") { echo 'selected="selected"'; } ?> value="Africa/Nairobi">Nairobi</option>
</optgroup>
<optgroup label="Quirguistão (KG)">
<option <?php if($dados_stm["timezone"] == "Asia/Bishkek") { echo 'selected="selected"'; } ?> value="Asia/Bishkek">Bishkek</option>
</optgroup>
<optgroup label="Reino Unido (GB)">
<option <?php if($dados_stm["timezone"] == "Europe/London") { echo 'selected="selected"'; } ?> value="Europe/London">Londres</option>
</optgroup>
<optgroup label="República Centro-Africana (CF)">
<option <?php if($dados_stm["timezone"] == "Africa/Bangui") { echo 'selected="selected"'; } ?> value="Africa/Bangui">Bangui</option>
</optgroup>
<optgroup label="República Democrática do Congo (CD)">
<option <?php if($dados_stm["timezone"] == "Africa/Kinshasa") { echo 'selected="selected"'; } ?> value="Africa/Kinshasa">Kinshasa</option>
<option <?php if($dados_stm["timezone"] == "Africa/Lubumbashi") { echo 'selected="selected"'; } ?> value="Africa/Lubumbashi">Lubumbashi</option>
</optgroup>
<optgroup label="República do Congo (CG)">
<option <?php if($dados_stm["timezone"] == "Africa/Brazzaville") { echo 'selected="selected"'; } ?> value="Africa/Brazzaville">Brazzaville</option>
</optgroup>
<optgroup label="República Dominicana (DO)">
<option <?php if($dados_stm["timezone"] == "America/Santo_Domingo") { echo 'selected="selected"'; } ?> value="America/Santo_Domingo">Santo Domingo</option>
</optgroup>
<optgroup label="República Tcheca (CZ)">
<option <?php if($dados_stm["timezone"] == "Europe/Prague") { echo 'selected="selected"'; } ?> value="Europe/Prague">Praga</option>
</optgroup>
<optgroup label="Reunião (RE)">
<option <?php if($dados_stm["timezone"] == "Indian/Reunion") { echo 'selected="selected"'; } ?> value="Indian/Reunion">Reunião</option>
</optgroup>
<optgroup label="Romênia (RO)">
<option <?php if($dados_stm["timezone"] == "Europe/Bucharest") { echo 'selected="selected"'; } ?> value="Europe/Bucharest">Bucareste</option>
</optgroup>
<optgroup label="Ruanda (RW)">
<option <?php if($dados_stm["timezone"] == "Africa/Kigali") { echo 'selected="selected"'; } ?> value="Africa/Kigali">Kigali</option>
</optgroup>
<optgroup label="Rússia (RU)">
<option <?php if($dados_stm["timezone"] == "Asia/Anadyr") { echo 'selected="selected"'; } ?> value="Asia/Anadyr">Anadyr</option>
<option <?php if($dados_stm["timezone"] == "Asia/Irkutsk") { echo 'selected="selected"'; } ?> value="Asia/Irkutsk">Irkutsk</option>
<option <?php if($dados_stm["timezone"] == "Europe/Kaliningrad") { echo 'selected="selected"'; } ?> value="Europe/Kaliningrad">Kaliningrad</option>
<option <?php if($dados_stm["timezone"] == "Asia/Kamchatka") { echo 'selected="selected"'; } ?> value="Asia/Kamchatka">Kamchatka</option>
<option <?php if($dados_stm["timezone"] == "Asia/Khandyga") { echo 'selected="selected"'; } ?> value="Asia/Khandyga">Khandyga</option>
<option <?php if($dados_stm["timezone"] == "Asia/Krasnoyarsk") { echo 'selected="selected"'; } ?> value="Asia/Krasnoyarsk">Krasnoyarsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Magadan") { echo 'selected="selected"'; } ?> value="Asia/Magadan">Magadan</option>
<option <?php if($dados_stm["timezone"] == "Europe/Moscow") { echo 'selected="selected"'; } ?> value="Europe/Moscow">Moscovo</option>
<option <?php if($dados_stm["timezone"] == "Asia/Novokuznetsk") { echo 'selected="selected"'; } ?> value="Asia/Novokuznetsk">Novokuznetsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Novosibirsk") { echo 'selected="selected"'; } ?> value="Asia/Novosibirsk">Novosibirsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Omsk") { echo 'selected="selected"'; } ?> value="Asia/Omsk">Omsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Sakhalin") { echo 'selected="selected"'; } ?> value="Asia/Sakhalin">Sakhalin</option>
<option <?php if($dados_stm["timezone"] == "Europe/Samara") { echo 'selected="selected"'; } ?> value="Europe/Samara">Samara</option>
<option <?php if($dados_stm["timezone"] == "Asia/Ust-Nera") { echo 'selected="selected"'; } ?> value="Asia/Ust-Nera">Ust-Nera</option>
<option <?php if($dados_stm["timezone"] == "Asia/Vladivostok") { echo 'selected="selected"'; } ?> value="Asia/Vladivostok">Vladivostok</option>
<option <?php if($dados_stm["timezone"] == "Europe/Volgograd") { echo 'selected="selected"'; } ?> value="Europe/Volgograd">Volgograd</option>
<option <?php if($dados_stm["timezone"] == "Asia/Yakutsk") { echo 'selected="selected"'; } ?> value="Asia/Yakutsk">Yakutsk</option>
<option <?php if($dados_stm["timezone"] == "Asia/Yekaterinburg") { echo 'selected="selected"'; } ?> value="Asia/Yekaterinburg">Yekaterinburg</option>
</optgroup>
<optgroup label="Saara Ocidental (EH)">
<option <?php if($dados_stm["timezone"] == "Africa/El_Aaiun") { echo 'selected="selected"'; } ?> value="Africa/El_Aaiun">El Aaiun</option>
</optgroup>
<optgroup label="Saint-Pierre e Miquelon (PM)">
<option <?php if($dados_stm["timezone"] == "America/Miquelon") { echo 'selected="selected"'; } ?> value="America/Miquelon">Miquelon</option>
</optgroup>
<optgroup label="Samoa (WS)">
<option <?php if($dados_stm["timezone"] == "Pacific/Apia") { echo 'selected="selected"'; } ?> value="Pacific/Apia">Apia</option>
</optgroup>
<optgroup label="Samoa Americana (AS)">
<option <?php if($dados_stm["timezone"] == "Pacific/Pago_Pago") { echo 'selected="selected"'; } ?> value="Pacific/Pago_Pago">Pago Pago</option>
</optgroup>
<optgroup label="San Marino (SM)">
<option <?php if($dados_stm["timezone"] == "Europe/San_Marino") { echo 'selected="selected"'; } ?> value="Europe/San_Marino">San Marino</option>
</optgroup>
<optgroup label="Santa Helena (SH)">
<option <?php if($dados_stm["timezone"] == "Atlantic/St_Helena") { echo 'selected="selected"'; } ?> value="Atlantic/St_Helena">St Helena</option>
</optgroup>
<optgroup label="Santa Lúcia (LC)">
<option <?php if($dados_stm["timezone"] == "America/St_Lucia") { echo 'selected="selected"'; } ?> value="America/St_Lucia">St Lucia</option>
</optgroup>
<optgroup label="São Bartolomeu (BL)">
<option <?php if($dados_stm["timezone"] == "America/St_Barthelemy") { echo 'selected="selected"'; } ?> value="America/St_Barthelemy">St Barthelemy</option>
</optgroup>
<optgroup label="São Cristóvão e Nevis (KN)">
<option <?php if($dados_stm["timezone"] == "America/St_Kitts") { echo 'selected="selected"'; } ?> value="America/St_Kitts">St Kitts</option>
</optgroup>
<optgroup label="São Martinho (Países Baixos) (SX)">
<option <?php if($dados_stm["timezone"] == "America/Lower_Princes") { echo 'selected="selected"'; } ?> value="America/Lower_Princes">Lower Princes</option>
</optgroup>
<optgroup label="São Tomé e Príncipe (ST)">
<option <?php if($dados_stm["timezone"] == "Africa/Sao_Tome") { echo 'selected="selected"'; } ?> value="Africa/Sao_Tome">São Tomé</option>
</optgroup>
<optgroup label="São Vicente e Granadinas (VC)">
<option <?php if($dados_stm["timezone"] == "America/St_Vincent") { echo 'selected="selected"'; } ?> value="America/St_Vincent">St Vincent</option>
</optgroup>
<optgroup label="Senegal (SN)">
<option <?php if($dados_stm["timezone"] == "Africa/Dakar") { echo 'selected="selected"'; } ?> value="Africa/Dakar">Dakar</option>
</optgroup>
<optgroup label="Serra Leoa (SL)">
<option <?php if($dados_stm["timezone"] == "Africa/Freetown") { echo 'selected="selected"'; } ?> value="Africa/Freetown">Freetown</option>
</optgroup>
<optgroup label="Sérvia (RS)">
<option <?php if($dados_stm["timezone"] == "Europe/Belgrade") { echo 'selected="selected"'; } ?> value="Europe/Belgrade">Belgrado</option>
</optgroup>
<optgroup label="Sérvia e Montenegro (CS)"></optgroup>
<optgroup label="Seychelles (SC)">
<option <?php if($dados_stm["timezone"] == "Indian/Mahe") { echo 'selected="selected"'; } ?> value="Indian/Mahe">Mahe</option>
</optgroup>
<optgroup label="Singapura (SG)">
<option <?php if($dados_stm["timezone"] == "Asia/Singapore") { echo 'selected="selected"'; } ?> value="Asia/Singapore">Singapura</option>
</optgroup>
<optgroup label="Síria (SY)">
<option <?php if($dados_stm["timezone"] == "Asia/Damascus") { echo 'selected="selected"'; } ?> value="Asia/Damascus">Damasco</option>
</optgroup>
<optgroup label="Somália (SO)">
<option <?php if($dados_stm["timezone"] == "Africa/Mogadishu") { echo 'selected="selected"'; } ?> value="Africa/Mogadishu">Mogadíscio</option>
</optgroup>
<optgroup label="Sri Lanka (LK)">
<option <?php if($dados_stm["timezone"] == "Asia/Colombo") { echo 'selected="selected"'; } ?> value="Asia/Colombo">Colombo</option>
</optgroup>
<optgroup label="Suazilândia (SZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Mbabane") { echo 'selected="selected"'; } ?> value="Africa/Mbabane">Mbabane</option>
</optgroup>
<optgroup label="Sudão (SD)">
<option <?php if($dados_stm["timezone"] == "Africa/Khartoum") { echo 'selected="selected"'; } ?> value="Africa/Khartoum">Cartum</option>
</optgroup>
<optgroup label="Sudão do Sul (SS)">
<option <?php if($dados_stm["timezone"] == "Africa/Juba") { echo 'selected="selected"'; } ?> value="Africa/Juba">Juba</option>
</optgroup>
<optgroup label="Suécia (SE)">
<option <?php if($dados_stm["timezone"] == "Europe/Stockholm") { echo 'selected="selected"'; } ?> value="Europe/Stockholm">Estocolmo</option>
</optgroup>
<optgroup label="Suíça (CH)">
<option <?php if($dados_stm["timezone"] == "Europe/Zurich") { echo 'selected="selected"'; } ?> value="Europe/Zurich">Zurique</option>
</optgroup>
<optgroup label="Suriname (SR)">
<option <?php if($dados_stm["timezone"] == "America/Paramaribo") { echo 'selected="selected"'; } ?> value="America/Paramaribo">Paramaribo</option>
</optgroup>
<optgroup label="Svalbard e Jan Mayen (SJ)">
<option <?php if($dados_stm["timezone"] == "Arctic/Longyearbyen") { echo 'selected="selected"'; } ?> value="Arctic/Longyearbyen">Longyearbyen</option>
</optgroup>
<optgroup label="Tailândia (TH)">
<option <?php if($dados_stm["timezone"] == "Asia/Bangkok") { echo 'selected="selected"'; } ?> value="Asia/Bangkok">Banguecoque</option>
</optgroup>
<optgroup label="Taiwan (TW)">
<option <?php if($dados_stm["timezone"] == "Asia/Taipei") { echo 'selected="selected"'; } ?> value="Asia/Taipei">Taipe</option>
</optgroup>
<optgroup label="Tajiquistão (TJ)">
<option <?php if($dados_stm["timezone"] == "Asia/Dushanbe") { echo 'selected="selected"'; } ?> value="Asia/Dushanbe">Dushanbe</option>
</optgroup>
<optgroup label="Tanzânia (TZ)">
<option <?php if($dados_stm["timezone"] == "Africa/Dar_es_Salaam") { echo 'selected="selected"'; } ?> value="Africa/Dar_es_Salaam">Dar es Salaam</option>
</optgroup>
<optgroup label="Terras Austrais e Antárticas Francesas (TF)">
<option <?php if($dados_stm["timezone"] == "Indian/Kerguelen") { echo 'selected="selected"'; } ?> value="Indian/Kerguelen">Kerguelen</option>
</optgroup>
<optgroup label="Território Britânico do Oceano Índico (IO)">
<option <?php if($dados_stm["timezone"] == "Indian/Chagos") { echo 'selected="selected"'; } ?> value="Indian/Chagos">Chagos</option>
</optgroup>
<optgroup label="Territórios Palestinianos (PS)">
<option <?php if($dados_stm["timezone"] == "Asia/Gaza") { echo 'selected="selected"'; } ?> value="Asia/Gaza">Gaza</option>
<option <?php if($dados_stm["timezone"] == "Asia/Hebron") { echo 'selected="selected"'; } ?> value="Asia/Hebron">Hebron</option>
</optgroup>
<optgroup label="Timor-Leste (TL)">
<option <?php if($dados_stm["timezone"] == "Asia/Dili") { echo 'selected="selected"'; } ?> value="Asia/Dili">Dili</option>
</optgroup>
<optgroup label="Togo (TG)">
<option <?php if($dados_stm["timezone"] == "Africa/Lome") { echo 'selected="selected"'; } ?> value="Africa/Lome">Lomé</option>
</optgroup>
<optgroup label="Tokelau (TK)">
<option <?php if($dados_stm["timezone"] == "Pacific/Fakaofo") { echo 'selected="selected"'; } ?> value="Pacific/Fakaofo">Fakaofo</option>
</optgroup>
<optgroup label="Tonga (TO)">
<option <?php if($dados_stm["timezone"] == "Pacific/Tongatapu") { echo 'selected="selected"'; } ?> value="Pacific/Tongatapu">Tongatapu</option>
</optgroup>
<optgroup label="Trinidad e Tobago (TT)">
<option <?php if($dados_stm["timezone"] == "America/Port_of_Spain") { echo 'selected="selected"'; } ?> value="America/Port_of_Spain">Port of Spain</option>
</optgroup>
<optgroup label="Tunísia (TN)">
<option <?php if($dados_stm["timezone"] == "Africa/Tunis") { echo 'selected="selected"'; } ?> value="Africa/Tunis">Túnis</option>
</optgroup>
<optgroup label="Turquemenistão (TM)">
<option <?php if($dados_stm["timezone"] == "Asia/Ashgabat") { echo 'selected="selected"'; } ?> value="Asia/Ashgabat">Ashgabat</option>
</optgroup>
<optgroup label="Turquia (TR)">
<option <?php if($dados_stm["timezone"] == "Europe/Istanbul") { echo 'selected="selected"'; } ?> value="Europe/Istanbul">Istanbul</option>
</optgroup>
<optgroup label="Tuvalu (TV)">
<option <?php if($dados_stm["timezone"] == "Pacific/Funafuti") { echo 'selected="selected"'; } ?> value="Pacific/Funafuti">Funafuti</option>
</optgroup>
<optgroup label="Ucrânia (UA)">
<option <?php if($dados_stm["timezone"] == "Europe/Kiev") { echo 'selected="selected"'; } ?> value="Europe/Kiev">Kiev</option>
<option <?php if($dados_stm["timezone"] == "Europe/Simferopol") { echo 'selected="selected"'; } ?> value="Europe/Simferopol">Simferopol</option>
<option <?php if($dados_stm["timezone"] == "Europe/Uzhgorod") { echo 'selected="selected"'; } ?> value="Europe/Uzhgorod">Uzhgorod</option>
<option <?php if($dados_stm["timezone"] == "Europe/Zaporozhye") { echo 'selected="selected"'; } ?> value="Europe/Zaporozhye">Zaporozhye</option>
</optgroup>
<optgroup label="Uganda (UG)">
<option <?php if($dados_stm["timezone"] == "Africa/Kampala") { echo 'selected="selected"'; } ?> value="Africa/Kampala">Kampala</option>
</optgroup>
<optgroup label="Uruguai (UY)">
<option <?php if($dados_stm["timezone"] == "America/Montevideo") { echo 'selected="selected"'; } ?> value="America/Montevideo">Montevidéu</option>
</optgroup>
<optgroup label="Uzbequistão (UZ)">
<option <?php if($dados_stm["timezone"] == "Asia/Samarkand") { echo 'selected="selected"'; } ?> value="Asia/Samarkand">Samarkand</option>
<option <?php if($dados_stm["timezone"] == "Asia/Tashkent") { echo 'selected="selected"'; } ?> value="Asia/Tashkent">Tashkent</option>
</optgroup>
<optgroup label="Vanuatu (VU)">
<option <?php if($dados_stm["timezone"] == "Pacific/Efate") { echo 'selected="selected"'; } ?> value="Pacific/Efate">Efate</option>
</optgroup>
<optgroup label="Vaticano (VA)">
<option <?php if($dados_stm["timezone"] == "Europe/Vatican") { echo 'selected="selected"'; } ?> value="Europe/Vatican">Vaticano</option>
</optgroup>
<optgroup label="Venezuela (VE)">
<option <?php if($dados_stm["timezone"] == "America/Caracas") { echo 'selected="selected"'; } ?> value="America/Caracas">Caracas</option>
</optgroup>
<optgroup label="Vietnã (VN)">
<option <?php if($dados_stm["timezone"] == "Asia/Ho_Chi_Minh") { echo 'selected="selected"'; } ?> value="Asia/Ho_Chi_Minh">Ho Chi Minh</option>
</optgroup>
<optgroup label="Wallis e Futuna (WF)">
<option <?php if($dados_stm["timezone"] == "Pacific/Wallis") { echo 'selected="selected"'; } ?> value="Pacific/Wallis">Wallis</option>
</optgroup>
<optgroup label="Zâmbia (ZM)">
<option <?php if($dados_stm["timezone"] == "Africa/Lusaka") { echo 'selected="selected"'; } ?> value="Africa/Lusaka">Lusaka</option>
</optgroup>
<optgroup label="Zimbábue (ZW)">
<option <?php if($dados_stm["timezone"] == "Africa/Harare") { echo 'selected="selected"'; } ?> value="Africa/Harare">Harare</option>
</optgroup>
</select>            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_painel_formato_data']; ?></td>
            <td align="left" class="texto_padrao_vermelho">
            <select name="formato_data" class="input" id="formato_data" style="width:255px;">
          		<option value="d/m/Y H:i:s"<?php if($dados_stm["formato_data"] == "d/m/Y H:i:s") { echo ' selected="selected"'; } ?>>DD/MM/YYYY</option>
		  		<option value="m/d/Y H:i:s"<?php if($dados_stm["formato_data"] == "m/d/Y H:i:s") { echo ' selected="selected"'; } ?>>MM/DD/YYYY</option>
          		<option value="Y-m-d H:i:s"<?php if($dados_stm["formato_data"] == "Y-m-d H:i:s") { echo ' selected="selected"'; } ?>>YYYY-MM-DD</option>		  
         	</select>&nbsp;<?php echo formatar_data($dados_stm["formato_data"], date("Y-m-d H:i:s"), $dados_stm["timezone"]); ?>         </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">&nbsp;</td>
            <td align="left" class="texto_padrao">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">&nbsp;</td>
            <td align="left" class="texto_padrao">&nbsp;</td>
          </tr>
        </table>
      </div>
      </div>
    </td>
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