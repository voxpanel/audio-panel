<?php
require_once("inc/protecao-revenda.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$url_player = (!empty($dados_revenda["dominio_padrao"])) ? "player.".$dados_revenda["dominio_padrao"]."" : "player.".$dados_config["dominio_padrao"]."";

// Verifica se o streaming é do cliente
if($dados_stm["codigo_cliente"] != $_SESSION["code_user_logged"]) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["aviso_acesso_negado"] = status_acao(lang_info_acesso_stm_nao_permitido,"erro");

header("Location: /admin/revenda-informacoes");
exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<table width="885" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
<div id="quadro-topo"><strong><?php echo lang_info_players_tab_players; ?></strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,'conteudo');">
		    <option value="/admin/revenda-gerenciar-player-flash/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_flash; ?></option>
            <option value="/admin/revenda-gerenciar-player-flash/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_flash; ?></option>
            <option value="/admin/revenda-gerenciar-player-topo/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_flash_topo; ?></option>
            <option value="/admin/revenda-gerenciar-player-computador/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_computador; ?></option>
            <option value="/admin/revenda-gerenciar-player-celulares/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_celulares; ?></option>
            <option value="/admin/revenda-gerenciar-player-facebook/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_facebook; ?></option>
            <option value="/admin/revenda-gerenciar-player-twitter/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_twitter; ?></option>
            <?php if($dados_revenda["stm_exibir_app_android"] == 'sim' || $dados_stm["stm_exibir_app_android"] == 'sim') { ?>
            <option value="/admin/revenda-app-android"><?php echo lang_info_players_player_app_android; ?></option>
            <?php } ?>
            <option value="/admin/revenda-gerenciar-player-popup/<?php echo query_string('2'); ?>"><?php echo lang_info_players_player_popup; ?></option>
         </select>
         </td>
      </tr>
    </table>
  </div>
</div>
      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td width="885" height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo lang_info_players_player_flash; ?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td height="25" align="center" class="texto_padrao">
			<?php if($dados_stm["aacplus"] == 'sim') { ?>
                <embed src="http://<?php echo $url_player; ?>/player-aacplus.swf" width="280" height="20" allowscriptaccess="always" allowfullscreen="true" flashvars="file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=false" type="application/x-shockwave-flash" /></embed>
              </embed>
              <br />
              <br />
                <textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><embed src="http://<?php echo $url_player; ?>/player-aacplus.swf" width="280" height="20" allowscriptaccess="always" allowfullscreen="true" flashvars="file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=true" type="application/x-shockwave-flash" /></embed></embed>
          </textarea>
                <?php } else { ?>
                <embed height="17" width="260" flashvars="file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&volume=100&bufferlength=0" allowscriptaccess="always" quality="high" src="http://<?php echo $url_player; ?>/player.swf" type="application/x-shockwave-flash"></embed>
              <br />
              <br />
                <textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><embed height="17" width="260" flashvars="file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&volume=100&bufferlength=0&autostart=true" allowscriptaccess="always" quality="high" src="http://<?php echo $url_player; ?>/player.swf" type="application/x-shockwave-flash"></embed>
          </textarea>
                <?php } ?>
            </td>
          </tr>
        </table>
    </div>
      </div>
      </td>
    </tr>
  </table>
  <br />
</div>
</body>
</html>