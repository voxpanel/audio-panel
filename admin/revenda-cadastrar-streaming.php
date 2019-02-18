<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

// Estat�sticas
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_streamings = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

$total_ouvintes = $ouvintes["total"]+$ouvintes_subrevendas["total"];

$porcentagem_uso_streamings = ($dados_revenda["streamings"] == 0) ? "0" : $total_streamings*100/$dados_revenda["streamings"];
$porcentagem_uso_ouvintes = ($dados_revenda["ouvintes"] == 0) ? "0" : $total_ouvintes*100/$dados_revenda["ouvintes"];
$porcentagem_uso_espaco_ftp = ($dados_revenda["espaco"] == 0) ? "0" : ($espaco_subrevendas["total"]+$espaco_streamings["total"])*100/$dados_revenda["espaco"];

$stat_streamings_descricao = ($dados_revenda["streamings"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_ilimitado.'</span>' : "".$total_streamings." / ".$dados_revenda["streamings"]."";
$stat_ouvintes_descricao = ($dados_revenda["ouvintes"] == 999999) ? '<span class="texto_ilimitado">'.lang_info_ilimitado.'</span>' : "".$total_ouvintes." / ".$dados_revenda["ouvintes"]."";
$stat_espaco_ftp_descricao = "".tamanho(($espaco_subrevendas["total"]+$espaco_streamings["total"]))." / ".tamanho($dados_revenda["espaco"])."";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastrar Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/javascript-abas.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!--[if IE]><script type="text/javascript" src="/inc/excanvas.js"></script><![endif]-->
<script src="/inc/jquery.knob.min.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };

function configurar_ouvintes_ilimitados() {

if(document.getElementById("ouvintes_ilimitados").checked) {
document.getElementById('ouvintes').value = '999999';
} else {
document.getElementById('ouvintes').value = '';
}

}
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<form method="post" action="/admin/revenda-cadastra-streaming" style="padding:0px; margin:0px" onSubmit="document.getElementById('cadastrar').disabled=true;">
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <th scope="col"><div id="quadro">
            	<div id="quadro-topo"><strong><?php echo lang_info_pagina_cadastrar_streaming_tab_titulo; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
      <div class="tab-page" id="tabPage1">
        <h2 class="tab"><?php echo lang_info_pagina_cadastrar_streaming_aba_geral; ?></h2>
        <table width="590" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_ouvintes; ?></td>
        <td align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" value="" />&nbsp;
        <?php if($dados_revenda["ouvintes"] == '999999') { ?>
        <input type="checkbox" id="ouvintes_ilimitados" onclick="configurar_ouvintes_ilimitados();" style="vertical-align:middle" />&nbsp;<span class="texto_ilimitado"><?php echo lang_info_pagina_cadastrar_streaming_ouvintes_ilimitados; ?></span>
        <?php } ?>        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_bitrate; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="bitrate" class="input" id="bitrate" style="width:255px;">
  <?php
		   foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
		   
		   if($bitrate <= $dados_revenda["bitrate"]) {
		   
		   if($bitrate == $dados_revenda["bitrate"]) {
		   echo "<option value=\"".$bitrate."\">".$bitrate." Kbps(m�ximo)</option>\n";
		   } else {
		   echo "<option value=\"".$bitrate."\">".$bitrate." Kbps</option>\n";
		   }

		   }
		    
		   }
		  ?>
</select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_streaming_ativar_autodj; ?></td>
        <td align="left" class="texto_padrao">
          <input name="autodj" type="radio" id="autodj" onclick="document.getElementById('espaco').disabled=false;document.getElementById('espaco').style.cursor = 'auto';" value="sim" checked="checked" />&nbsp;<?php echo lang_opcao_sim; ?>
          <input type="radio" name="autodj" id="autodj" value="nao" onclick="document.getElementById('espaco').disabled=true;document.getElementById('espaco').style.cursor = 'not-allowed';" />&nbsp;<?php echo lang_opcao_nao; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_espaco_autodj; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="text" class="input" id="espaco" style="width:250px;" />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_espaco_autodj_ajuda; ?>');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_senha; ?></td>
        <td align="left"><input name="senha" type="text" class="input" id="senha" style="width:250px;" />&nbsp;<img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Gerar Senha" width="16" height="16" align="absmiddle" onclick="gerar_senha('senha');" style="cursor:pointer" />&nbsp;
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_senha_ajuda; ?>');" style="cursor:pointer" />        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_identificacao; ?></td>
        <td align="left"><input name="identificacao" type="text" class="input" id="identificacao" style="width:250px;" />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_identificacao_ajuda; ?>');" style="cursor:pointer" />        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_idioma; ?></td>
        <td align="left">
        <select name="idioma_painel" id="idioma_painel" style="width:255px;">
          <option value="pt-br" selected="selected">Portugu�s(Brasil)</option>
          <option value="es">Espa�ol</option>
          <option value="en-us">English(USA)</option>
        </select>
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_idioma_ajuda; ?>');" style="cursor:pointer" />        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_streaming_email; ?></td>
        <td align="left"><input name="email" type="text" class="input" id="email" style="width:250px;" />
          <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_email_ajuda; ?>');" style="cursor:pointer" /></td>
          </tr>
        </table>
      </div>
      <div class="tab-page" id="tabPage2">
        <h2 class="tab"><?php echo lang_info_pagina_cadastrar_streaming_aba_recursos; ?></h2>
        <table width="590" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <?php if($dados_revenda["aacplus"] == 'sim') { ?>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_rtmp; ?></td>
        <td align="left" class="texto_padrao">
          <input name="aacplus" type="radio" id="aacplus" onclick="configurar_aacplus_revenda(this.value);" value="sim" checked="checked" />&nbsp;<?php echo lang_opcao_sim; ?>
          <input type="radio" name="aacplus" id="aacplus" value="nao" onclick="configurar_aacplus_revenda(this.value);" />&nbsp;<?php echo lang_opcao_nao; ?>          </td>
      </tr>
      <?php } else { ?>
      <tr>
        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_rtmp; ?></td>
        <td align="left" class="texto_log_sistema_erro"><?php echo lang_info_pagina_cadastrar_streaming_rtmp_info_desativado; ?>
          <input name="aacplus" id="aacplus" type="hidden" value="nao" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_streaming_permitir_alterar_senha; ?></td>
        <td align="left" class="texto_padrao">
          <input type="radio" name="permitir_alterar_senha" id="permitir_alterar_senha" value="sim" checked="checked" />&nbsp;<?php echo lang_opcao_sim; ?>
          <input type="radio" name="permitir_alterar_senha" id="permitir_alterar_senha" value="nao" />&nbsp;<?php echo lang_opcao_nao; ?>
          <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_permitir_alterar_senha_info_ajuda; ?>');" style="cursor:pointer" />          </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_app_android; ?></td>
                  <td align="left" class="texto_padrao">
                  <input name="exibir_app_android" type="radio" value="sim" />&nbsp;<?php echo lang_opcao_sim; ?>
                  <input name="exibir_app_android" type="radio" value="nao" />&nbsp;<?php echo lang_opcao_nao; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_app_android_info_ajuda; ?>');" style="cursor:pointer" /></td>
          </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_mini_site; ?></td>
                  <td align="left" class="texto_padrao">
                  <input name="exibir_mini_site" type="radio" value="sim" />&nbsp;<?php echo lang_opcao_sim; ?>
                  <input name="exibir_mini_site" type="radio" value="nao" />&nbsp;<?php echo lang_opcao_nao; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_mini_site_info_ajuda; ?>');" style="cursor:pointer" /></td>
          </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_streaming_encoders; ?></td>
                  <td align="left" class="texto_padrao">
                    <input name="encoder_mp3" type="checkbox" id="encoder_mp3" value="sim" checked="checked" />
                    &nbsp;<?php echo lang_info_pagina_cadastrar_streaming_encoders_mp3; ?>
                    <input name="encoder_aacplus" type="checkbox" id="encoder_aacplus" value="sim" checked="checked" />
&nbsp;<?php echo lang_info_pagina_cadastrar_streaming_encoders_aacplus; ?>
                      <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo lang_info_pagina_cadastrar_streaming_encoders_info_ajuda; ?>');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao">&nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao">&nbsp;</td>
      </tr>
        </table>
      </div>
      <div class="tab-page" id="tabPage2">
        <h2 class="tab"><?php echo lang_info_pagina_cadastrar_streaming_aba_estatisticas; ?></h2>
        <table width="590" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="196" height="120" align="center">
            <?php if($dados_revenda["streamings"] != 999999) { ?>
            <input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="80" data-height="80" value="<?php echo round($porcentagem_uso_streamings); ?>" id="grafico_uso_plano_streamings" />
            <?php } else { ?>
            <img src="img/img-ilimitado.png" width="78" height="78" alt="<?php echo lang_info_ilimitado; ?>" title="<?php echo lang_info_ilimitado; ?>" />           
            <?php } ?>            </td>
            <td width="196" height="100" align="center">
            <?php if($dados_revenda["ouvintes"] != 999999) { ?>
            <input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="80" data-height="80" value="<?php echo round($porcentagem_uso_ouvintes); ?>" id="grafico_uso_plano_ouvintes" />
            <?php } else { ?>
            <img src="img/img-ilimitado.png" width="78" height="78" alt="<?php echo lang_info_ilimitado; ?>" title="<?php echo lang_info_ilimitado; ?>" />        
            <?php } ?>
            </td>
            <td width="196" height="100" align="center">
            <?php if($dados_revenda["espaco"] != 999999) { ?>
            <input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="80" data-height="80" value="<?php echo round($porcentagem_uso_espaco_ftp); ?>" id="grafico_uso_plano_espaco_ftp" />
            <?php } else { ?>
            <img src="img/img-ilimitado.png" width="78" height="78" alt="<?php echo lang_info_ilimitado; ?>" title="<?php echo lang_info_ilimitado; ?>" />            
            <?php } ?>
            </td>
          </tr>
          <tr>
            <td height="120" align="center" valign="top"><?php echo $stat_streamings_descricao; ?><br />
              Streamings</td>
            <td align="center" valign="top"><?php echo $stat_ouvintes_descricao; ?><br />
              <?php echo lang_info_pagina_cadastrar_streaming_ouvintes; ?></td>
            <td align="center" valign="top"><?php echo $stat_espaco_ftp_descricao; ?><br />
              <?php echo lang_info_pagina_cadastrar_streaming_espaco_autodj; ?></td>
          </tr>
        </table>
      </div>
      </div>
    <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo lang_botao_titulo_cadastrar; ?>" id="cadastrar" /></td>
  </tr>
</table>
</div>
  </div>
  </th>
</tr>
</table>
</form>
<script type="text/javascript">
// Barra de Progresso Ouvintes
$(function() {
	$(".knob").knob();
	<?php if($dados_revenda["streamings"] != 999999) { ?>
	document.getElementById('grafico_uso_plano_streamings').value=document.getElementById('grafico_uso_plano_streamings').value+'%';
	<?php } ?>
	<?php if($dados_revenda["ouvintes"] != 999999) { ?>
	document.getElementById('grafico_uso_plano_ouvintes').value=document.getElementById('grafico_uso_plano_ouvintes').value+'%';
	<?php } ?>
	<?php if($dados_revenda["espaco"] != 999999) { ?>
	document.getElementById('grafico_uso_plano_espaco_ftp').value=document.getElementById('grafico_uso_plano_espaco_ftp').value+'%';
	<?php } ?>
});
</script>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</div>
</body>
</html>
