<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

$ponto = (query_string('1')) ? "".query_string('1')."/" : "";

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "player.".$dados_revenda["dominio_padrao"]."" : "player.".$dados_config["dominio_padrao"]."";

$dados_servidor_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
$encoder = ($dados_servidor_shoutcast["encoder"] == "audio/aacp") ? "aac" : 'mp3';
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
            <td height="30" align="center" scope="col"><select style="width:98%;" onchange="window.open('/gerenciar-player-html5-simples/'+this.value+'','conteudo');">
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
                          </select>
            </td>
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
        <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_muses']; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td width="290" height="230" align="center" valign="top" class="texto_padrao">
<script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'mcclean',
'width':180,
'height':60
});
</script><br /><br />
<textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'mcclean',
'width':180,
'height':60
});
</script></textarea></td>
            <td width="290" height="230" align="center" valign="top" class="texto_padrao"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'darkconsole',
'width':190,
'height':62
});
              </script>
              <br />
              <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'darkconsole',
'width':190,
'height':62
});
</script>
              </textarea></td>
            <td width="290" height="230" align="center" valign="top" class="texto_padrao"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'compact',
'width':191,
'height':46
});
              </script>
              <br />
              <br />
              <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'compact',
'width':191,
'height':46
});
</script>
              </textarea></td>
          </tr>
          <tr>
            <td width="290" height="230" align="center" valign="top" class="texto_padrao"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'cassette',
'width':200,
'height':120
});
              </script>
                <br />
                <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'cassette',
'width':200,
'height':120
});
</script>
              </textarea></td>
            <td width="290" height="230" align="center" valign="top" class="texto_padrao"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'uuskin',
'width':166,
'height':120
});
              </script>
                <br />
                <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'uuskin',
'width':166,
'height':83
});
</script>
              </textarea></td>
            <td width="290" height="230" align="center" valign="top" class="texto_padrao"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'eastanbul',
'width':280,
'height':120
});
              </script>
                <br />
                <br />
                <textarea readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'eastanbul',
'width':467,
'height':26
});
</script>
              </textarea></td>
          </tr>
          <tr>
            <td height="250" align="center" valign="top" class="texto_padrao"><br /><br /><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'kplayer',
'width':220,
'height':175
});
              </script>
                <br />
                <br />
                <textarea name="textarea" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'kplayer',
'width':220,
'height':200
});
</script>
              </textarea></td>
            <td height="250" align="center" valign="top" class="texto_padrao"><br /><br /><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'oldstereo',
'width':318,
'height':130
});
              </script>
                <br />
                <br />
                <br />
                <br />
                <br />
                <textarea name="textarea" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'oldstereo',
'width':318,
'height':130
});
</script>
              </textarea></td>
            <td height="250" align="center" valign="top" class="texto_padrao"><br /><br /><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
                <script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':false,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'abrahadabra2',
'width':100,
'height':145
});
              </script>
                <br />
                <br />
                <br />
                <br />
                <textarea name="textarea" readonly="readonly" style="width:280px; height:100px;font-size:11px" onmouseover="this.select()"><script type="text/javascript" src="https://hosted.muses.org/mrp.js"></script>
<script type="text/javascript">
MRP.insert({
'url':'http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/<?php echo $ponto; ?>;',
'codec':'<?php echo $encoder; ?>',
'volume':100,
'autoplay':<?php echo $dados_stm["player_autoplay"]; ?>,
'forceHTML5':true,
'jsevents':false,
'buffering':0,
'title':'<?php echo $dados_stm["streamtitle"]; ?>',
'wmode':'transparent',
'skin':'abrahadabra2',
'width':100,
'height':141
});
</script>
              </textarea></td>
          </tr>
        </table>
      </div>
      </div>
      </th>
  </tr>
</table>
</div>
<!-- In�cio div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>