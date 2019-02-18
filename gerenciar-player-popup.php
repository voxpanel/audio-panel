<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "player.".$dados_revenda["dominio_padrao"]."" : "player.".$dados_config["dominio_padrao"]."";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
  <table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
    <tr>
      <th scope="col"><div id="quadro">
          <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_tab_players']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
              <tr>
                <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;"><select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,'conteudo');">
                    <option value="/gerenciar-player-html5-simples"><?php echo $lang['lang_info_players_player_selecione']; ?></option>
                    <option value="/gerenciar-player-html5-simples"><?php echo $lang['lang_info_players_player_html5_simples']; ?></option>
                    <option value="/gerenciar-player-muses"><?php echo $lang['lang_info_players_player_muses']; ?></option>
                    <option value="/gerenciar-player-topo"><?php echo $lang['lang_info_players_player_flash_topo']; ?></option>
                    <option value="/gerenciar-player-computador"><?php echo $lang['lang_info_players_player_computador']; ?></option>
                    <option value="/gerenciar-player-celulares"><?php echo $lang['lang_info_players_player_celulares']; ?></option>
                    <option value="/gerenciar-player-facebook"><?php echo $lang['lang_info_players_player_facebook']; ?></option>
                    <option value="/gerenciar-player-twitter"><?php echo $lang['lang_info_players_player_twitter']; ?></option>
                    <?php if($dados_stm["stm_exibir_app_android"] == 'sim') { ?>
                    <option value="/app-android"><?php echo $lang['lang_info_players_player_app_android']; ?></option>
                    <?php } ?>
                    <option value="/gerenciar-player-popup"><?php echo $lang['lang_info_players_player_popup']; ?></option>
                    <option value="/gerenciar-player-popup-responsivo"><?php echo $lang['lang_info_players_player_popup_responsivo']; ?></option>
                  </select>
                </td>
              </tr>
            </table>
        </div>
      </div></th>
    </tr>
  </table>
  <table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
  <tr>
    <th scope="col"> <div id="quadro">
        <div id="quadro-topo"> <strong>MuiltiPoint</strong></div>
      <div class="texto_medio" id="quadro-conteudo">
          <table width="870" border="0" cellspacing="0" cellpadding="0" style="padding-bottom:5px;">
            <tr>
              <th align="left" scope="col"><table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#FFFF66; border:#DFDF00 1px solid">
                <tr>
                  <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
                  <td width="740" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_players_player_info_multipoint']; ?></td>
                </tr>
              </table></th>
            </tr>
          </table>
      </div>
      </div>
      </th>
  </tr>
</table>
<table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
  <tr>
    <th scope="col"> <div id="quadro">
        <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_popup']; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td width="435" height="25" align="center"><img src="/img/img-exemplo-player-popup1.jpg" alt="Player Popup" width="400" height="270" border="0" /></td>
            <td width="435" align="center"><img src="/img/img-exemplo-player-popup2.jpg" alt="Player Popup" width="400" height="270" border="0" /></td>
          </tr>
          <tr>
            <td height="120" align="center" class="texto_padrao"><br /><textarea name="textarea2" readonly="readonly" style="width:400px; height:100px;font-size:11px" onmouseover="this.select()"><script>
function abrir_player_popup() {
window.open( "http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/1", "","width=400,height=270,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=no,resizable=NO" );
}
</script>
<a href="javascript:abrir_player_popup();"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-popup.png" width="32" height="32" title="Abrir Player Pop-up" />&quot;</a></textarea><br /><br />
<textarea name="textarea2" readonly="readonly" style="width:400px; height:40px;font-size:11px" onmouseover="this.select()"><iframe src="http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/1" frameborder="0" width="500" height="290"></iframe></textarea></td>
            <td align="center" class="texto_padrao"><br /><textarea name="textarea" readonly="readonly" style="width:400px; height:100px;font-size:11px" onmouseover="this.select()"><script>
function abrir_player_popup() {
window.open( "http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/2", "","width=400,height=270,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=no,resizable=NO" );
}
</script>
<a href="javascript:abrir_player_popup();"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-popup.png" width="32" height="32" title="Abrir Player Pop-up" />&quot;</a></textarea><br /><br />
<textarea name="textarea2" readonly="readonly" style="width:400px; height:40px;font-size:11px" onmouseover="this.select()"><iframe src="http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/2" frameborder="0" width="500" height="290"></iframe></textarea></td>
          </tr>
          <tr>
            <td height="5" colspan="2" align="center" class="texto_padrao">&nbsp;</td>
          </tr>
          <tr>
            <td height="25" align="center"><img src="/img/img-exemplo-player-popup3.jpg" alt="Player Popup" width="400" height="270" border="0" /></td>
            <td align="center"><img src="/img/img-exemplo-player-popup4.jpg" alt="Player Popup" width="400" height="270" border="0" /></td>
          </tr>
          <tr>
            <td height="120" align="center" class="texto_padrao"><br /><textarea name="textarea3" readonly="readonly" style="width:400px; height:100px;font-size:11px" onmouseover="this.select()"><script>
function abrir_player_popup() {
window.open( "http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/3", "","width=400,height=270,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=no,resizable=NO" );
}
</script>
<a href="javascript:abrir_player_popup();"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-popup.png" width="32" height="32" title="Abrir Player Pop-up" /></a></textarea><br /><br />
<textarea name="textarea2" readonly="readonly" style="width:400px; height:40px;font-size:11px" onmouseover="this.select()"><iframe src="http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/3" frameborder="0" width="500" height="290"></iframe></textarea></td>
            <td align="center" class="texto_padrao"><br /><textarea name="textarea3" readonly="readonly" style="width:400px; height:100px;font-size:11px" onmouseover="this.select()"><script>
function abrir_player_popup() {
window.open( "http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/4", "","width=400,height=270,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=no,resizable=NO" );
}
</script>
<a href="javascript:abrir_player_popup();"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-popup.png" width="32" height="32" title="Abrir Player Pop-up" /></a></textarea><br /><br />
<textarea name="textarea2" readonly="readonly" style="width:400px; height:40px;font-size:11px" onmouseover="this.select()"><iframe src="http://<?php echo $url_player; ?>/player-popup/<?php echo $dados_stm["porta"]; ?>/4" frameborder="0" width="500" height="290"></iframe></textarea></td>
          </tr>
        </table>
      </div>
      </div>
      </th>
  </tr>
</table>
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