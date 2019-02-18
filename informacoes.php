<?php
require_once("admin/inc/protecao-final.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));

$limite_ouvintes = ($dados_stm["ouvintes"] == 999999) ? '<span class="texto_ilimitado">'.$lang['lang_info_ilimitado'].'</span>' : $dados_stm["ouvintes"];

$porta_code = code_decode($dados_stm["porta"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
	// Status de exibição dos quadros
	document.getElementById('tabela_avisos').style.display=getCookie('tabela_avisos');
	document.getElementById('tabela_info_stm').style.display=getCookie('tabela_info_stm');
	document.getElementById('tabela_info_plano').style.display=getCookie('tabela_info_plano');
	document.getElementById('tabela_info_ftp').style.display=getCookie('tabela_info_ftp');
	document.getElementById('tabela_gerenciamento_streaming').style.display=getCookie('tabela_gerenciamento_streaming');
	document.getElementById('tabela_gerenciamento_autodj').style.display=getCookie('tabela_gerenciamento_autodj');
	document.getElementById('tabela_utilitarios').style.display=getCookie('tabela_utilitarios');
	document.getElementById('tabela_solucao_problemas').style.display=getCookie('tabela_solucao_problemas');
	document.getElementById('tabela_painel').style.display=getCookie('tabela_painel');
   };
   window.onkeydown = function (event) {
		if (event.keyCode == 27) {
			document.getElementById('log-sistema-fundo').style.display = 'none';
			document.getElementById('log-sistema').style.display = 'none';
		}
	}
</script>


</head>

<body>
<div id="sub-conteudo">
<?php if($dados_servidor["status"] == "on") { ?>
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
  <?php 
$total_dicas_rapidas = mysql_num_rows(mysql_query("SELECT * FROM dicas_rapidas where exibir = 'sim'"));

if($total_dicas_rapidas > 0) {

$dados_dica_rapida = mysql_fetch_array(mysql_query("SELECT * FROM dicas_rapidas where exibir = 'sim' ORDER BY RAND() LIMIT 1"));

$dados_dicas_rapidas_acesso = mysql_fetch_array(mysql_query("SELECT * FROM dicas_rapidas_acessos where codigo_stm = '".$dados_stm["codigo"]."' AND codigo_dica = '".$dados_dica_rapida["codigo"]."'"));

if($dados_dicas_rapidas_acesso["total"] < 10) {

if($dados_dicas_rapidas_acesso["total"] == 0) {
mysql_query("INSERT INTO dicas_rapidas_acessos (codigo_stm,codigo_dica,total) VALUES (".$dados_stm["codigo"].",'".$dados_dica_rapida["codigo"]."','1')");
} else {
mysql_query("Update dicas_rapidas_acessos set total = total+1 where codigo = '".$dados_dicas_rapidas_acesso["codigo"]."'");
}

$dica_rapida = str_replace("PAINEL","http://".$_SERVER['HTTP_HOST']."",$dados_dica_rapida["mensagem"]);
$dica_rapida = str_replace("PORTA","".$dados_stm["porta"]."",$dica_rapida);
?>
<table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
<tr>
            <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
            <td width="850" align="left" class="texto_padrao_destaque" scope="col"><?php echo $dica_rapida; ?></td>
    </tr>
</table>
<?php
}
}
?>
<?php if($dados_stm["status"] == 1) { ?>
<table width="885" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
      <td width="885" height="50" align="center" valign="top">
      <div id="quadro">
            	<div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_avisos');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_avisos']; ?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
            		  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="display:block" id="tabela_avisos">
                        <tr>
                          <td height="25" class="texto_padrao">
						  <?php
							carregar_avisos_streaming($dados_stm["porta"],$dados_servidor["codigo"]);
						  ?>						  </td>
                        </tr>
                      </table>
            		</div>
      </div>      </td>
    </tr>
  </table>
  <table width="885" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="295" height="50" align="center" valign="top" style="padding-right:5px"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_stm');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_streaming']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0" style="display:block" id="tabela_info_stm">
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_porta']; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["porta"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_porta_dj']; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_stm["porta_dj"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ip_conexao']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_shoutcast']; ?></td>
                <td align="left" class="texto_padrao_pequeno"><a href="javascript:abrir_janela('http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>',720,500);">http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?></a></td>
              </tr>
              <?php if($dados_stm["aacplus"] == 'sim') { ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_rtmp']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno">rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?></td>
              </tr>
              <?php } else { ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque"></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"></td>
              </tr>
              <?php } ?>
            </table>
        </div>
      </div></td>
      <td width="295" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_plano');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_plano']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0" style="display:block" id="tabela_info_plano">
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ouvintes']; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $limite_ouvintes; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espaco_ftp']; ?></td>
                <td align="left" class="texto_padrao"><?php echo tamanho($dados_stm["espaco"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_playlists']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $total_playlists; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_bitrate']; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_stm["bitrate"]; ?> Kbps</td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
            </table>
        </div>
      </div></td>
      <td width="295" align="center" valign="top" style="padding-left:5px;"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_ftp');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_ftp']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0" style="display:block" id="tabela_info_ftp">
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ip_ftp']; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_login_ftp']; ?></td>
                <td align="left" class="texto_padrao"><?php echo $dados_stm["porta"]; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espaco_ftp']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo tamanho($dados_stm["espaco"]); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
            </table>
        </div>
      </div></td>
    </tr>
  </table>
  <table width="885" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="885" align="center" valign="top">
      <?php if($dados_stm["autodj"] == "sim") { ?>
      <table width="885" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="442" align="center" valign="top" style="padding-right:5px">
          <div id="quadro2">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="425" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/dados-conexao','conteudo');"><img src="img/icones/img-icone-dados-conexao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?></td>
                    <td height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?></td>
                    <td height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-relay','conteudo');"><img src="img/icones/img-relay.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_relay']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_relay']; ?></td>
                  </tr>
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
                    <td height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/ouvintes-conectados','conteudo');"><img src="img/icones/img-icone-ouvintes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ouvintes_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ouvintes_conectados']; ?></td>
                    <td height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                  </tr>
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="kick_streaming('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-desconectar-source.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_kick']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_kick']; ?></td>
                    <td height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="ativar_desativar_protecao('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-protecao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_protecao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_protecao']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-multipoint','conteudo');"><img src="img/icones/img-icone-multipoint-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_multipoint']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_multipoint']; ?></td>
                  </tr>
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/logs-shoutcast','conteudo');"><img src="img/icones/img-icone-logs-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs_servidor']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs_servidor']; ?></td>
                    <?php if($dados_stm["exibir_app_android"] == 'sim') { ?>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/app-android','conteudo');"><img src="img/icones/img-icone-app-android-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?></td>
                    <?php } ?>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque"></td>
                  </tr>
                </table>
            </div>
          </div></td>
          <td width="442" align="center" valign="top" style="padding-left:5px">
          <div id="quadro">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_autodj');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_autodj']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="425" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_autodj">
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="pular_musica('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-pular-musica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_pular_musica']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_pular_musica']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="recarregar_playlist('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-recarregar-playlist.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_recarregar_playlist']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_recarregar_playlist']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="trocar_playlist('<?php echo $porta_code;?>','carregar_playlists','0');"><img src="img/icones/img-icone-trocar-playlist.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_trocar_playlist']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_trocar_playlist']; ?></td>
                  </tr>
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-musicas','conteudo');"><img src="img/icones/img-icone-gerenciador-musicas.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_musicas']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_musicas']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/playlists','conteudo');"><img src="img/icones/img-icone-playlists.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_playlists']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_playlists']; ?>&nbsp;</td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-djs','conteudo');"><img src="img/icones/img-icone-dj.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_djs']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_djs']; ?></td>
                  </tr>
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-agendamentos','conteudo');"><img src="img/icones/img-icone-agendamento.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_agendamentos']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gerenciar_agendamentos']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-playlists-hora-certa','conteudo');"><img src="img/icones/img-icone-hora-certa-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_hora_certa']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_hora_certa']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-playlists-vinhetas-comerciais','conteudo');"><img src="img/icones/img-icone-vinhetas-comerciais-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_vinhetas_comerciais']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_vinhetas_comerciais']; ?></td>
                  </tr>
                  <tr>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-autodj','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?></td>
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/logs-autodj','conteudo');"><img src="img/icones/img-icone-logs-64x64.png" alt="" width="48" height="48" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs_servidor']; ?>" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs_servidor']; ?></td>

                    <?php if($dados_stm['programetes'] == "sim"){ ?>    
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/programetes','conteudo');"><img src="img/icones/img-icone-programetes-64x64.png" alt="" width="48" height="48" title="Programetes" /> <br />
                        Programetes&nbsp;<span class="label label-verde"><?php echo $lang['lang_label_novo']; ?></span>
                    </td>

                    <?php } ?>

                    <?php if($dados_stm['programetes'] == "nao"){ ?>    
                    <td width="141" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick=""><img src="img/icones/img-icone-programetes-64x64.png" alt="" width="48" height="48" title="Programetes" /> <br />
                        Programetes&nbsp;<span class="label" style="background: red;">Indisponível</span>
                    </td>

                    <?php } ?>


                  </tr>
                </table>
            </div>
          </div></td>
        </tr>
      </table>
      <?php } ?>      
      <?php if($dados_stm["autodj"] == "nao") { ?>
      <table width="885" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" valign="top">
          <div id="quadro2">
            <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_gerenciamento_streaming');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
            <div class="texto_medio" id="quadro-conteudo">
                <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_gerenciamento_streaming">
                  <tr>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/dados-conexao','conteudo');"><img src="img/icones/img-icone-dados-conexao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_dados_conexao']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-streaming','conteudo');"><img src="img/icones/img-icone-configuracoes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-relay','conteudo');"><img src="img/icones/img-icone-relay.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_relay']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_relay']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_players();"><img src="img/icones/img-icone-players.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_players']; ?>&nbsp;</td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/ouvintes-conectados','conteudo');"><img src="img/icones/img-icone-ouvintes.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ouvintes_conectados']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ouvintes_conectados']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="carregar_estatisticas_streaming('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-estatistica.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_stats']; ?></td>
                  </tr>
                  <tr>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="kick_streaming('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-desconectar-source.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_kick']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_kick']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="ativar_desativar_protecao('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-protecao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_protecao']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_protecao']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_janela('http://www.facebook.com/sharer/sharer.php?app_id=522557647825370&amp;display=popup&amp;redirect_uri=http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>/fechar&amp;u=http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>',500,300);"><img src="img/icones/img-icone-facebook-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_player_facebook']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_player_facebook']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/gerenciar-multipoint','conteudo');"><img src="img/icones/img-icone-multipoint-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_multipoint']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_multipoint']; ?></td>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/logs-shoutcast','conteudo');"><img src="img/icones/img-icone-logs-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs_servidor']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs_servidor']; ?></td>
                        <?php if($dados_stm["exibir_app_android"] == 'sim') { ?>
                    <td width="145" height="80" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/app-android','conteudo');"><img src="img/icones/img-icone-app-android-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>" width="48" height="48" /> <br />
                        <?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?></td>
                        <?php } ?>
                  </tr>
                </table>
            </div>
          </div></td>
          </tr>
      </table>
      <?php } ?>
      </td>
    </tr>
  </table>
  <?php if($dados_stm["autodj"] == "sim") { ?>
  <table width="885" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="885" height="50" align="center" valign="top">
      <div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_utilitarios');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_utilitarios']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
          <table width="875" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_utilitarios">
            <tr>
              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="definir_nome_musica('<?php echo $porta_code;?>','<?php echo $lang['lang_info_pagina_informacoes_tab_menu_definir_nome_musica_info']; ?>');"><img src="img/icones/img-icone-mudar-nome-musica-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_definir_nome_musica']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_definir_nome_musica']; ?></td>

              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-gravador','conteudo');"><img src="img/icones/img-icone-gravar-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_gravador']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_gravador']; ?>&nbsp;<span class="label label-amarelo">BREVE</span>
              </td>

              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-migrar-musicas','conteudo');"><img src="img/icones/img-icone-download-musica-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_migrar_musicas']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_migrar_musicas']; ?>&nbsp;</td>
              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-editar-tag-musicas','conteudo');"><img src="img/icones/img-icone-tag-mp3-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_editar_tag_musicas']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_editar_tag_musicas']; ?>&nbsp;</td>
              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-download-mp3','conteudo');"><img src="img/icones/img-icone-download-mp3.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_download_mp3']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_download_mp3']; ?></td>
              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-download-soundcloud','conteudo');"><img src="img/icones/img-icone-download-soundcloud.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_download_soundcloud']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_download_soundcloud']; ?>&nbsp;<span class="label label-verde"><?php echo $lang['lang_label_novo']; ?></span></td>
              <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-youtube','conteudo');"><img src="img/icones/img-icone-youtube-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_download_youtube']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_download_youtube']; ?></td>
              </tr>
          </table>
        </div>
      </div></td>
    </tr>
  </table>
  <?php } ?>
    <table width="885" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="885" height="50" align="center" valign="top">
      <div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_solucao_problemas');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_solucao_problemas']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
          <table width="875" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_solucao_problemas">
            <tr>
              <td width="145" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="atualizar_cache_player_facebook('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-facebook-64x64.png" title="<?php echo $lang['lang_info_pagina_resolver_problemas_tab_menu_player_facebook']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_player_facebook']; ?></td>
              <td width="145" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="configurar_encoder('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-encoder-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_encoder']; ?>" width="48" height="48" /> <br />
                  <?php echo $lang['lang_info_pagina_informacoes_tab_menu_encoder']; ?></td>
                  <?php if($dados_stm["autodj"] == 'sim') { ?>
                <td width="145" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="diagnosticar_autodj('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-diagnosticar-autodj-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_diagnosticar_autodj']; ?>" width="48" height="48" /> <br />
                <?php echo $lang['lang_info_pagina_informacoes_tab_menu_diagnosticar_autodj']; ?></td>
                <?php } ?>
            <td width="145" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="atualizar_cache_players('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-player-cache-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_player_cache']; ?>" width="48" height="48" /> <br />
                      <?php echo $lang['lang_info_pagina_informacoes_tab_menu_player_cache']; ?></td>
                  <td width="145" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/utilitario-renomear-musicas','conteudo');"><img src="img/icones/img-icone-ferramenta-renomear-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_renomear_musicas']; ?>" width="48" height="48" /> <br />
                <?php echo $lang['lang_info_pagina_informacoes_tab_menu_renomear_musicas']; ?></td>
                      <?php if($dados_stm["aacplus"] == 'sim') { ?>
                <td width="145" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="sincronizar_aacplus('<?php echo $porta_code;?>');"><img src="img/icones/img-icone-sincronizar-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_sinc_aacplus']; ?>" width="48" height="48" /> <br />
                <?php echo $lang['lang_info_pagina_informacoes_tab_menu_sinc_aacplus']; ?></td>
                <?php } ?>
              </tr>
          </table>
        </div>
      </div></td>
    </tr>
  </table>
  <table width="885" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:10px">
    <tr>
      <td width="885" height="50" align="center" valign="top"><div id="quadro">
          <div id="quadro-topo"><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_painel');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_painel']; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="875" border="0" align="center" cellpadding="0" cellspacing="0" style="display:block" id="tabela_painel">
              <tr>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-painel','conteudo');"><img src="img/icones/img-icone-configuracao.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_painel']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_config_painel']; ?>&nbsp;<span class="label label-amarelo"><?php echo $lang['lang_label_atualizado']; ?></span></td>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/logs','conteudo');"><img src="img/icones/img-icone-logs-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs']; ?>" width="48" height="48" /> <br />
                   <?php echo $lang['lang_info_pagina_informacoes_tab_menu_logs']; ?></td>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/streaming-api','conteudo');"><img src="img/icones/img-icone-api.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_api']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_api']; ?>&nbsp;</td>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/configuracoes-painel','conteudo');"><img src="img/icones/img-icone-idioma.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_idioma']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_idioma']; ?>&nbsp;</td>
                    <?php if($dados_revenda["stm_exibir_app_android_painel"] == 'sim') { ?>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/app-painel','conteudo');"><span class="texto_padrao_destaque" style="cursor:pointer"><img src="img/icones/img-icone-app-android-movel64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_android']; ?>" width="48" height="48" /></span><br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_app_painel']; ?>&nbsp;</td>
                    <?php } ?>
                <?php if($dados_revenda["stm_exibir_tutoriais"] == 'sim') { ?>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/ajuda','conteudo');"><img src="img/icones/img-icone-ajuda-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?></td>
                    <?php } ?>
                    <?php if($dados_revenda["stm_exibir_tutoriais"] == 'url') { ?>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="window.open('<?php echo $dados_revenda["url_tutoriais"]; ?>');"><img src="img/icones/img-icone-ajuda-64x64.png" title="<?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?>" width="48" height="48" /> <br />
                    <?php echo $lang['lang_info_pagina_informacoes_tab_menu_ajuda']; ?></td>
                    <?php } ?>
                    <?php if($dados_revenda["url_downloads"]) { ?>
                <td width="125" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="window.open('<?php echo $dados_revenda["url_downloads"]; ?>');"><img src="img/icones/img-icone-download-64x64.png" title="Downloads" width="48" height="48" /> <br />
                    Downloads</td>
                    <?php } ?>
              </tr>
            </table>
        </div>
      </div></td>
    </tr>
  </table>
  <?php } else { ?>
  <table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="40" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="840" align="left" class="texto_status_erro" scope="col"><?php echo $lang['lang_alerta_bloqueio']; ?></td>
    </tr>
    </table>
  <?php } ?>
  <?php } else { ?>
<table width="880" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:15%; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="180" height="150" align="center" scope="col"><img src="/img/icones/img-icone-manutencao-128x128.png" width="128" height="128" /></td>
      <td width="700" align="left" class="texto_status_erro_pequeno" scope="col" style="padding-left:5px; padding-right:5px"><?php echo $dados_servidor["mensagem_manutencao"];?></td>
    </tr>
    </table>
  <?php } ?>
  <br />
  <br />
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