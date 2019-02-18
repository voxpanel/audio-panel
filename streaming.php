<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));

if($dados_stm["status"] == 1 && $dados_servidor["status"] == "on") {
//$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"],1);
}

if($dados_stm["aacplus"] == 'sim') {
$formato = "AAC+ sem Plugin(rtmp)";
} elseif($dados_stm["aacplus"] == 'nao' && $dados_stm["encoder"] == 'aacp') {
$formato = "AAC+ simples";
} else {
$formato = "MP3";
}

if($_SESSION["code_user_logged"]) {
$verificacao_revenda_logada = ($dados_stm["codigo_cliente"] == $_SESSION["code_user_logged"]) ? true : false;
}

$porta_code = code_decode($dados_stm["porta"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>[<?php echo $dados_stm["porta"]; ?>] Gerenciamento de Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
   window.onload = function() {
    // Carregar avisos do streaming na inicialização
	<?php
	carregar_avisos_streaming_inicializacao($dados_stm["porta"],$dados_servidor["codigo"]);
	?>
	// Carregar informações do streaming na inicialização
	status_streaming('<?php echo $porta_code; ?>');
	setInterval("status_streaming('<?php echo $porta_code; ?>')",60000);
	<?php if($dados_servidor["status"] == "on") { ?>
	<?php if($dados_stm["aparencia_exibir_musica_atual"] == 'sim') { ?>
	musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','30');
	setInterval("musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','30')",180000);
	<?php } ?>
	<?php if($dados_stm["aparencia_exibir_stats_ouvintes"] == 'sim') { ?>
	estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','nao');
	setInterval("estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','nao')",30000);
	<?php } ?>
	<?php if($dados_stm["aparencia_exibir_stats_ftp"] == 'sim') { ?>
	estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao');
	setInterval("estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao')",300000);
	<?php } ?>
	<?php } ?>
	calcular_altura_iframe('conteudo');
   };
   window.onkeydown = function (event) {
		if (event.keyCode == 27) {
			document.getElementById('log-sistema-fundo').style.display = 'none';
			document.getElementById('log-sistema').style.display = 'none';
		}
	}
$.post( "/screen_size", { width: window.innerWidth, height: window.innerHeight })
  .done(function( data ) {
  });
</script>
<style>
body {
	overflow: hidden;
}
</style>
</head>

<body>
<?php if($dados_stm["status"] == 1) { ?>
<?php if($verificacao_revenda_logada === true) { ?>
<div class="texto_padrao_vermelho" id="barra-alerta-revenda-logada">
<img src="/img/icones/atencao.png" width="16" height="16" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_acessar_painel_revenda_logado']; ?>
</div>
<?php } ?>
<div id="topo">
<?php if(!isMobile()) { ?>
<div id="topo-logo"><img src="img/img-logo-shoutcast-topo.png" title="<?php echo $lang['lang_titulo_logo_sc']; ?>" width="96" height="96" border="0" onclick="window.open('http://<?php echo $dados_servidor["ip"]; ?>:<?php echo $dados_stm["porta"]; ?>','conteudo');" id="topo-logo-imagem" /></div>
<?php } ?>
<div id="topo-botao-ligar">
<img src="img/icones/img-icone-ligar-64x64.png" title="<?php echo $lang['lang_botao_titulo_ligar_stm']; ?>" width="64" height="64" style="cursor:pointer" onclick="ligar_streaming_autodj('<?php echo $porta_code;?>');" />
</div>
<div id="topo-botao-desligar">
<img src="img/icones/img-icone-desligar-48x48.png" title="<?php echo $lang['lang_botao_titulo_desligar_stm']; ?>" width="48" height="48" style="cursor:pointer" onclick="desligar_streaming_autodj('<?php echo $porta_code;?>');" />
</div>
<div id="topo-botao-reiniciar">
<img src="img/icones/img-icone-reiniciar-48x48.png" title="<?php echo $lang['lang_botao_titulo_reiniciar_stm']; ?>" width="48" height="48" style="cursor:pointer" onclick="reiniciar_streaming_autodj('<?php echo $porta_code;?>');" />
</div>
<div id="topo-menu" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_executar_acao']; ?></strong><br /><br />
<select class="topo-menu-select" id="<?php echo $porta_code;?>" onchange='executar_acao_streaming_autodj(this.id,this.value);' <?php if($dados_servidor["status"] == "off") { echo 'disabled="disabled"'; }?> style="border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px;">
<option value="" selected="selected"><?php echo $lang['lang_info_escolha_acao']; ?></option>
  <?php if($dados_stm["exibir_atalhos"] == 'sim') { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_atalhos']; ?>">
  <?php
  $sql_atalhos = mysql_query("SELECT * FROM atalhos WHERE codigo_stm = '".$dados_stm["codigo"]."' ORDER by ordem ASC");
  while ($dados_atalhos = mysql_fetch_array($sql_atalhos)) {
  
  echo "<option value='".$dados_atalhos["menu"]."'>".$lang[''.$dados_atalhos["lang"].'']."</option>\n";
  }
  ?>
  </optgroup>
  <?php } ?>
  <optgroup label="<?php echo $lang['lang_acao_label_streaming']; ?>">
  <option value='streaming-informacoes'><?php echo $lang['lang_acao_stm_info']; ?></option>
  <option value='streaming-dados-conexao'><?php echo $lang['lang_acao_stm_dados_conexao']; ?></option>
  <option value='streaming-configurar'><?php echo $lang['lang_acao_stm_config']; ?></option>
  <option value='streaming-configurar-relay'><?php echo $lang['lang_acao_stm_config_relay']; ?></option>
  <option value='streaming-players'><?php echo $lang['lang_acao_stm_players']; ?></option>
  
  <option value='streaming-kick'><?php echo $lang['lang_acao_stm_kick']; ?></option>
  <option value='streaming-protecao'><?php echo $lang['lang_acao_stm_protecao']; ?></option>
  <?php if($dados_stm["exibir_app_android"] == 'sim') { ?>
  <option value='streaming-app-android'><?php echo $lang['lang_acao_stm_app_android']; ?></option>
  <?php } ?>
  <option value='streaming-multipoint'><?php echo $lang['lang_acao_stm_multipoint']; ?></option>
  <option value='streaming-logs-servidor'><?php echo $lang['lang_acao_stm_logs_servidor']; ?></option>
  </optgroup>
  <optgroup label="<?php echo $lang['lang_acao_label_ouvintes']; ?>">
  <option value='ouvintes-ouvintes-conectados'><?php echo $lang['lang_acao_ouvintes_ouvintes_conectados']; ?></option>
  <option value='ouvintes-estatisticas'><?php echo $lang['lang_acao_ouvintes_stats']; ?></option>
  <option value='ouvintes-pedidos-musicais'><?php echo $lang['lang_acao_ouvintes_pedidos_musicais']; ?></option>
  <option value='ouvintes-chat'><?php echo $lang['lang_acao_ouvintes_chat']; ?></option>
  </optgroup>
  <?php if($dados_stm["autodj"] == "sim") { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_autodj']; ?>">
  <option value='autodj-recarregar-playlist'><?php echo $lang['lang_acao_autodj_recarregar_playlist']; ?></option>
  <option value='autodj-trocar-playlist'><?php echo $lang['lang_acao_autodj_trocar_playlist']; ?></option>
  <option value='autodj-pular-musica'><?php echo $lang['lang_acao_autodj_pular_musica']; ?></option>
  <option value='autodj-gerenciar-musicas'><?php echo $lang['lang_acao_autodj_gerenciar_musicas']; ?></option>
  <option value='autodj-gerenciar-playlists'><?php echo $lang['lang_acao_autodj_gerenciar_playlists']; ?></option>
  <option value='autodj-gerenciar-djs'><?php echo $lang['lang_acao_autodj_gerenciar_djs']; ?></option>
  <option value='autodj-gerenciar-agendamentos'><?php echo $lang['lang_acao_autodj_gerenciar_agendamentos']; ?></option>
  <option value='autodj-gerenciar-hora-certa'><?php echo $lang['lang_acao_autodj_gerenciar_hora_certa']; ?></option>
  <option value='autodj-gerenciar-vinhetas-comerciais'><?php echo $lang['lang_acao_autodj_gerenciar_vinhetas_comerciais']; ?></option>
  <option value='autodj-configurar'><?php echo $lang['lang_acao_autodj_config']; ?></option>
  <option value='autodj-logs-servidor'><?php echo $lang['lang_acao_autodj_logs_servidor']; ?></option>
  </optgroup>
  <?php } ?> 
  <optgroup label="<?php echo $lang['lang_acao_label_painel']; ?>">
  <option value='painel-configurar'><?php echo $lang['lang_acao_painel_config']; ?></option>
  <option value='painel-api'><?php echo $lang['lang_acao_painel_api']; ?></option>
  <option value='painel-logs'><?php echo $lang['lang_acao_painel_logs']; ?></option>
  <?php if($dados_revenda["stm_exibir_tutoriais"] == 'sim') { ?>
  <option value='painel-ajuda'><?php echo $lang['lang_acao_painel_ajuda']; ?></option>
  <?php } ?>
  <?php if($dados_revenda["stm_exibir_tutoriais"] == 'url') { ?>
  <option value='<?php echo $dados_revenda["url_tutoriais"]; ?>'><?php echo $lang['lang_acao_painel_ajuda']; ?></option>
  <?php } ?>
  <?php if($dados_revenda["stm_exibir_downloads"] == 'sim') { ?>
  <option value='painel-downloads'><?php echo $lang['lang_acao_painel_downloads']; ?></option>
  <?php } ?>
  </optgroup>
  <?php if($dados_stm["autodj"] == "sim") { ?>
  <optgroup label="<?php echo $lang['lang_acao_label_utilitarios']; ?>">
  <option value='utilitario-download-youtube'><?php echo $lang['lang_acao_utilitarios_download_youtube']; ?></option>
  <option value='utilitario-download-mp3'><?php echo $lang['lang_acao_utilitarios_download_mp3']; ?></option>
  <option value='utilitario-download-soundcloud'><?php echo $lang['lang_acao_utilitarios_download_soundcloud']; ?></option>
  <option value='utilitario-gravador'><?php echo $lang['lang_acao_utilitarios_gravador']; ?></option>
  <option value='utilitario-migrar-musicas'><?php echo $lang['lang_acao_utilitarios_migrar_musicas']; ?></option>
  <option value='utilitario-renomear-musicas'><?php echo $lang['lang_acao_utilitarios_renomear_musicas']; ?></option>
  </optgroup>
  <?php } ?>
  <optgroup label="<?php echo $lang['lang_acao_label_solucao_problemas']; ?>">
  <?php if($dados_stm["aacplus"] == 'sim') { ?>
  <option value='solucao-problemas-sincronizar-aacplus'><?php echo $lang['lang_acao_solucao_problemas_sincronizar_aacplus']; ?></option>
  <?php } ?>
  <!--
  <option value='solucao-problemas-player-facebook'><?php echo $lang['lang_acao_solucao_problemas_player_facebook']; ?></option>
  -->
  <option value='solucao-problemas-encoder'><?php echo $lang['lang_acao_solucao_problemas_encoder']; ?></option>
  <?php if($dados_stm["autodj"] == 'sim') { ?>
  <option value='solucao-problemas-diagnosticar-autodj'><?php echo $lang['lang_acao_solucao_problemas_diagnosticar_autodj']; ?></option>
  <?php } ?>
  <option value='solucao-problemas-player-cache'><?php echo $lang['lang_acao_solucao_problemas_player_cache']; ?></option>
  </optgroup>
</select>
</div>

<?php if($dados_stm["aparencia_exibir_stats_ouvintes"] == 'sim' || $dados_stm["aparencia_exibir_stats_ftp"] == 'sim') { ?>
<div id="topo-estatisticas">
<?php if($dados_stm["aparencia_exibir_stats_ouvintes"] == 'sim') { ?>
<div id="topo-estatisticas-ouvintes" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_ouvintes_conectados']; ?></strong><br /><br />
<span id="estatistica_uso_plano_ouvintes" style="cursor:pointer" onclick="estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','nao');"></span>
</div>
<?php } ?>
<?php if($dados_stm["aparencia_exibir_stats_ftp"] == 'sim') { ?>
<div id="topo-estatisticas-ftp" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_uso_ftp']; ?></strong><br /><br />
<span id="estatistica_uso_plano_ftp" style="cursor:pointer" onclick="estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao');"></span>
</div>
<?php } ?>

</div>
<?php } ?>
<?php if($dados_stm["aparencia_exibir_musica_atual"] == 'sim' || $dados_stm["aparencia_exibir_player"] == 'sim') { ?>
<div id="topo-musica-player">
<?php if($dados_stm["aparencia_exibir_musica_atual"] == 'sim') { ?>
<div id="topo-musica-atual" class="texto_padrao_pequeno" onclick="musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','35');"><strong><?php echo $lang['lang_info_musica_atual']; ?></strong><br /><br />
<span id="musica_atual"></span>
</div>
<?php } ?>
<?php if($dados_stm["aparencia_exibir_player"] == 'sim') { ?>
<div id="topo-player" class="texto_padrao_pequeno"><strong><?php echo $lang['lang_info_player']; ?></strong><br />
<audio style="width:100px; height:40px" preload="none" controls="play" src="http://<?php echo $dados_servidor["ip"]; ?>:<?php echo $dados_stm["porta"]; ?>/;">Seu navegador não tem suporte a HTML5</audio>
</div>
<?php } ?>
</div>
<?php } ?>
<div id="topo-botao-suporte-sair">
<?php if($dados_revenda["url_suporte"]) { ?>
<img src="img/icones/img-icone-suporte-64x64.png" title="<?php echo $lang['lang_titulo_suporte']; ?>" width="24" height="24" style="cursor:pointer" onclick="window.open('<?php echo $dados_revenda["url_suporte"]; ?>')" />&nbsp;
<?php } ?>
<img src="img/icones/img-icone-fechar.png" title="<?php echo $lang['lang_titulo_sair']; ?>" width="24" height="24" style="cursor:pointer" onclick="window.location = '/sair'" />
</div>

<div id="topo-status" class="texto_padrao">
<span id="status_streaming" style="cursor:pointer" onclick="status_streaming('<?php echo $porta_code; ?>')"></span>
</div>

</div>
<!-- Início iframe conteúdo -->
<iframe name="conteudo" id="conteudo" src="<?php echo $dados_stm["pagina_inicial"]; ?>" frameborder="0" width="100%" height="500"></iframe>
<!-- Fim iframe conteúdo -->
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
<?php } else { ?>
<div style="width:100%; height:700px;cursor: not-allowed; z-index:-1">
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo $lang['lang_alerta_bloqueio']; ?></td>
    </tr>
</table>
</div>
<?php } ?>
</body>
</html>