<?php
require_once("admin/inc/protecao-final.php");

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
<div id="sub-conteudo">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_utilidade_editor_id3tag']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
  	  <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        <td width="860" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_config_relay_info']; ?></td>
     </tr>
    </table>    </td>
  </tr>
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_utilidade_editor_id3tag']; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_relay_ativar']; ?></td>
            <td width="740" align="left">
            <input name="relay" type="checkbox" id="relay" value="sim"<?php if($dados_stm["relay"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>
            </td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_relay_ativar_monitoramento']; ?></td>
            <td width="740" align="left">
            <input name="relay_monitorar" type="checkbox" id="relay_monitorar" value="sim"<?php if($dados_stm["relay_monitorar"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_ativar_monitoramento_info']; ?>');" style="cursor:pointer" />
            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_relay_ip']; ?></td>
            <td align="left">
            <input name="relay_ip" type="text" class="input" id="relay_ip" style="width:250px;" value="<?php echo $dados_stm["relay_ip"]; ?>" />&nbsp;
            <img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_ip_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_relay_porta']; ?></td>
            <td align="left">
            <input name="relay_porta" type="text" class="input" id="relay_porta" style="width:250px;" value="<?php echo $dados_stm["relay_porta"]; ?>" />&nbsp;
            <img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_porta_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_relay_aba_relay']; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_relay_ativar']; ?></td>
            <td width="740" align="left">
            <input name="relay" type="checkbox" id="relay" value="sim"<?php if($dados_stm["relay"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>
            </td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_relay_ativar_monitoramento']; ?></td>
            <td width="740" align="left">
            <input name="relay_monitorar" type="checkbox" id="relay_monitorar" value="sim"<?php if($dados_stm["relay_monitorar"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_ativar_monitoramento_info']; ?>');" style="cursor:pointer" />
            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_relay_ip']; ?></td>
            <td align="left">
            <input name="relay_ip" type="text" class="input" id="relay_ip" style="width:250px;" value="<?php echo $dados_stm["relay_ip"]; ?>" />&nbsp;
            <img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_ip_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_relay_porta']; ?></td>
            <td align="left">
            <input name="relay_porta" type="text" class="input" id="relay_porta" style="width:250px;" value="<?php echo $dados_stm["relay_porta"]; ?>" />&nbsp;
            <img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_porta_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_config_relay_aba_relay']; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_relay_ativar']; ?></td>
            <td width="740" align="left">
            <input name="relay" type="checkbox" id="relay" value="sim"<?php if($dados_stm["relay"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>
            </td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_config_relay_ativar_monitoramento']; ?></td>
            <td width="740" align="left">
            <input name="relay_monitorar" type="checkbox" id="relay_monitorar" value="sim"<?php if($dados_stm["relay_monitorar"] == "sim") { echo ' checked="checked"'; } ?> />&nbsp;<?php echo $lang['lang_label_sim']; ?>&nbsp;<img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_ativar_monitoramento_info']; ?>');" style="cursor:pointer" />
            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_relay_ip']; ?></td>
            <td align="left">
            <input name="relay_ip" type="text" class="input" id="relay_ip" style="width:250px;" value="<?php echo $dados_stm["relay_ip"]; ?>" />&nbsp;
            <img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_ip_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_config_relay_porta']; ?></td>
            <td align="left">
            <input name="relay_porta" type="text" class="input" id="relay_porta" style="width:250px;" value="<?php echo $dados_stm["relay_porta"]; ?>" />&nbsp;
            <img src="img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_config_relay_porta_info']; ?>');" style="cursor:pointer" /></td>
          </tr>
        </table>
   	  </div>
      </div></td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_alterar_config']; ?>" />
      <input name="alterar" type="hidden" id="alterar" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
      </div>
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