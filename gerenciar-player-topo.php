<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "player.".$dados_revenda["dominio_padrao"]."" : "player.".$dados_config["dominio_padrao"]."";

$cor_player_topo = ($_POST["cor"]) ? $_POST["cor"] : '000000';
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
        <div id="quadro-topo"><strong><?php echo $lang['lang_info_players_player_flash_topo']; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td height="25" class="texto_padrao"><iframe src="http://<?php echo $url_player; ?>/player-barra/<?php echo $dados_stm["porta"]; ?>/<?php echo $cor_player_topo; ?>" frameborder="0" width="100%" height="31"></iframe>
                <br />
              <br />
                <textarea name="textarea" readonly="readonly" style="width:99%; height:30px;font-size:11px" onmouseover="this.select()"><iframe src="http://<?php echo $url_player; ?>/player-barra/<?php echo $dados_stm["porta"]; ?>/<?php echo $cor_player_topo; ?>" frameborder="0" width="100%" height="31"></iframe>
          </textarea>
                <br />
                <br />
                <form action="/gerenciar-player-topo" method="post" name="cor_player_topo" id="cor_player_topo">
                  <select name="cor" onchange="document.cor_player_topo.submit();">
                    <option value="000000">Cor</option>
                    <option value="000000" style="background:#000000; color:#FFFFFF">Preto</option>
                    <option value="FF0000" style="background:#FF0000; color:#FFFFFF">Vermelho</option>
                    <option value="FF00FF" style="background:#FF00FF; color:#FFFFFF">Pink</option>
                    <option value="0000FF" style="background:#0000FF; color:#FFFFFF">Azul</option>
                  </select>
                </form>
              <br />
                <span class="texto_padrao_destaque"><?php echo $lang['lang_info_players_player_flash_topo_info']; ?></span></td>
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