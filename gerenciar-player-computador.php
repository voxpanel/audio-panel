<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

$ponto = (query_string('1')) ? "".query_string('1')."/" : "";

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
  <?php if($total_pontos > 0) { ?>
  <table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
  <tr>
    <th scope="col"> <div id="quadro">
        <div id="quadro-topo"> <strong>MuiltiPoint</strong></div>
      <div class="texto_medio" id="quadro-conteudo">
          <table width="870" border="0" cellspacing="0" cellpadding="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
            <tr>
              <td height="30" align="center" scope="col">
                <select name="select6" style="width:98%;" onchange="window.open('/gerenciar-player-computador/'+this.value+'','conteudo');">
                  <?php

if($total_pontos > 0) {

$sql = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
while ($dados_ponto = mysql_fetch_array($sql)) {

if($dados_ponto["id"] == query_string('1')) {
echo '<option value="' . $dados_ponto["id"] . '" selected="selected">ID ' . $dados_ponto["id"] . ' - ' . $dados_ponto["ponto"] . '</option>';
} else {
echo '<option value="' . $dados_ponto["id"] . '">ID ' . $dados_ponto["id"] . ' - ' . $dados_ponto["ponto"] . '</option>';
}

}

} else {
echo '<option disabled="disabled">' .$lang['lang_info_gerenciador_multipoint_sem_registros']. '</option>';
}
?>
                </select>              </td>
            </tr>
          </table>
      </div>
      </div>
      </th>
  </tr>
</table>
<?php } ?>
<table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
  <tr>
    <th scope="col"> <div id="quadro">
        <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_computador']; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td width="290" height="25" align="center" class="texto_padrao"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>winamp.pls"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-winamp.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea name="textarea2" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>winamp.pls"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-winamp.png" width="32" height="32" title="Ouvir no Winamp" /></a></textarea></td>
            <td width="290" align="center" class="texto_padrao"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>mediaplayer.asx"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-mediaplayer.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea name="textarea3" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>mediaplayer.asx"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-mediaplayer.png" width="32" height="32" title="Ouvir no MediaPlayer" /></a></textarea></td>
            <td width="290" align="center" class="texto_padrao"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>realplayer.rm"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-realplayer.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea name="textarea4" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>realplayer.rm"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-realplayer.png" width="32" height="32" title="Ouvir no RealPlayer" /></a></textarea></td>
          </tr>
          <tr>
            <td height="5" colspan="3" align="center" class="texto_padrao">&nbsp;</td>
          </tr>
          <tr>
            <td width="290" align="center" class="texto_padrao"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>vlc.m3u"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-vlc.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea name="textarea4" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>vlc.m3u"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-vlc.png" width="32" height="32" title="Ouvir no RealPlayer" /></a></textarea></td>
            <td width="290" align="center" class="texto_padrao"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>quicktime.qtl"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-qt.png" width="32" height="32" border="0" /></a><br />
                <br />
                <textarea name="textarea4" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $url_player; ?>/player/<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>quicktime.qtl"><img src="http://<?php echo $url_player; ?>/img/icones/img-icone-player-qt.png" width="32" height="32" title="Ouvir no RealPlayer" /></a></textarea></td>
            <td align="center" class="texto_padrao">&nbsp;</td>
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