<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

// 1 - revenda | 2 - subrevenda da revenda | 3 - subrevenda da subrevenda
$tipo_subrevenda = (empty($dados_revenda["codigo_revenda"])) ? 2 : 3;

$query_stg_1 = query_string('2');
$query_stg_2 = query_string('3');

if(query_string('2')) {

if($query_stg_1 != "subrevenda") {

$verifica_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'"));

// Verifica se o streaming existe
if($verifica_stm == 0) {

header("Location: /admin/revenda");
exit;

}

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings WHERE porta = '".code_decode(query_string('2'),"D")."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores WHERE codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores WHERE codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$porta_code = code_decode($dados_stm["porta"],"E");

// Verifica se o streaming é do cliente
if($dados_stm["codigo_cliente"] != $_SESSION["code_user_logged"]) {

header("Location: /admin/revenda");
exit;

}

} else {

$verifica_subrevenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode(query_string('3'),"D")."') AND tipo = '".$tipo_subrevenda."'"));

// Verifica se a sub revenda existe
if($verifica_subrevenda == 0) {

header("Location: /admin/revenda");
exit;

}

$dados_subrevenda_atual = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".code_decode(query_string('3'),"D")."') AND tipo = '".$tipo_subrevenda."'"));

// Estatísticas de Uso do Plano
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_subrevenda_atual["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_subrevenda_atual["codigo"]."'"));
$espaco = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_subrevenda_atual["codigo"]."'"));

$porcentagem_uso_plano_stm = ($dados_subrevenda_atual["streamings"] == 0 || $dados_subrevenda_atual["streamings"] == 999999) ? "0" : $total_streamings*100/$dados_subrevenda_atual["streamings"];
$porcentagem_uso_plano_ouvintes = ($dados_subrevenda_atual["ouvintes"] == 0 || $dados_subrevenda_atual["ouvintes"] == 999999) ? "0" : $ouvintes["total"]*100/$dados_subrevenda_atual["ouvintes"];
$porcentagem_uso_plano_espaco_ftp = ($dados_subrevenda_atual["espaco"] == 0 || $dados_subrevenda_atual["espaco"] == 999999) ? "0" : $espaco["total"]*100/$dados_subrevenda_atual["espaco"];

}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax-revenda.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!--[if IE]><script type="text/javascript" src="/inc/excanvas.js"></script><![endif]-->
<script src="/inc/jquery.knob.min.js"></script>
<script type="text/javascript">
   window.onload = function() {
   <?php if($dados_stm["codigo"] && $dados_stm["status"] == '1') { ?>
	// Carregar informações do streaming na inicialização
	status_streaming('<?php echo $porta_code; ?>');
	<?php if($dados_servidor["status"] == "on") { ?>
	musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','25');
	estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','nao');
	estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao');

	// Atualizar informações do streaming
	setInterval("status_streaming('<?php echo $porta_code; ?>')",60000);
	setInterval("musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','25')",180000);
	setInterval("estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','nao')",30000);
	setInterval("estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao')",300000);
	<?php } ?>
   <?php } ?>
   };
</script>
<style>
body {
	overflow: hidden;
}
</style>
</head>

<body>
<?php if($dados_revenda["status"] == 1) { ?>
<div id="topo">

<div id="topo-logo">
<?php if(!empty($query_stg_1)) { ?>
<img src="/admin/img/img-home-topo.png" width="64" height="64" border="0" id="topo-logo-imagem" onclick="window.location = '/admin/revenda'" alt="Home" title="Home" />
<?php } else { ?>
<img src="/admin/img/img-logo-shoutcast-topo.png" width="96" height="96" border="0" id="topo-logo-imagem" onclick="window.location = '/admin/revenda'" alt="Home" title="Home" />
<?php } ?>
</div>

<div id="topo-botoes">
<img src="/admin/img/icones/img-icone-cadastrar-64x64.png" title="<?php echo lang_acao_revenda_cadastrar_stm; ?>" width="64" height="64" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-cadastrar-streaming','conteudo');" />&nbsp;&nbsp;
<?php if($dados_revenda["subrevendas"] > 0 && ($dados_revenda["tipo"] == 1 || $dados_revenda["tipo"] == 2)) { ?>
<img src="/admin/img/icones/img-icone-cadastrar-subrevenda-64x64.png" title="<?php echo lang_acao_revenda_cadastrar_subrevenda; ?>" width="60" height="60" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-subrevenda-cadastrar','conteudo');" />&nbsp;&nbsp;
<?php } ?>
<img src="/admin/img/icones/img-icone-busca-avancada-64x64.png" title="<?php echo lang_acao_revenda_busca_avancada; ?>" width="64" height="64" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-busca-avancada','conteudo');" />&nbsp;&nbsp;<img src="/admin/img/icones/img-icone-configuracoes-64x64.png" title="<?php echo lang_acao_revenda_config; ?>" width="64" height="64" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-configuracoes','conteudo');" />
</div>

<div id="topo-menu">

<?php if($query_stg_1 != "subrevenda" && empty($query_stg_2)) { ?>
<div id="topo-menu-streamings" class="texto_padrao_pequeno"><strong><?php echo lang_info_gerenciar_streaming; ?></strong><br />
<select class="topo-menu-streamings-select" id="streamings" onchange="selecionar_streaming_gerenciamento(this.value,'revenda');">
<option value="" selected="selected"><?php echo lang_info_escolha_porta; ?></option>
  <optgroup label="<?php echo lang_info_stm_ativos; ?>">
<?php
$i = 0;
$sql = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."' AND status = '1' ORDER by porta ASC");
while ($dados_lista_stm = mysql_fetch_array($sql)) {
	
	$dados_servidor_lista_stm = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_lista_stm["codigo_servidor"]."'"));
	$limite_ouvintes = ($dados_lista_stm["ouvintes"] == 999999) ? lang_info_ilimitado : $dados_lista_stm["ouvintes"];

	echo "<option value='".code_decode($dados_lista_stm["porta"],"E")."' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."'>".$dados_lista_stm["porta"]." - ".dominio_servidor($dados_servidor_lista_stm["nome"])." - ".$limite_ouvintes." ouvintes/".tamanho($dados_lista_stm["espaco"])."/".$dados_lista_stm["bitrate"]."Kbps (".$dados_lista_stm["identificacao"].")</option>";

$i++;
}
?>
  </optgroup>
  <optgroup label="<?php echo lang_info_stm_bloqueados; ?>">
<?php
$sql = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."' AND status != '1' ORDER by porta ASC");
while ($dados_lista_stm = mysql_fetch_array($sql)) {

	$dados_servidor_lista_stm = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_lista_stm["codigo_servidor"]."'"));
	$limite_ouvintes = ($dados_lista_stm["ouvintes"] == 999999) ? lang_info_ilimitado : $dados_lista_stm["ouvintes"];

	if($dados_lista_stm["status"] == '2') {
	echo "<option value='' disabled='disabled' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."'>".$dados_lista_stm["porta"]." - ".dominio_servidor($dados_servidor_lista_stm["nome"])." - ".$limite_ouvintes." ouvintes/".tamanho($dados_lista_stm["espaco"])."/".$dados_lista_stm["bitrate"]."Kbps (".$dados_lista_stm["identificacao"].")</option>";
	} else {
	echo "<option value='".code_decode($dados_lista_stm["porta"],"E")."' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."'>".$dados_lista_stm["porta"]." - ".dominio_servidor($dados_servidor_lista_stm["nome"])." - ".str_replace("999999","Ilimitados",$dados_lista_stm["ouvintes"])." ouvintes/".tamanho($dados_lista_stm["espaco"])."/".$dados_lista_stm["bitrate"]."Kbps (".$dados_lista_stm["identificacao"].")</option>";
	}
	
$i++;
}
?>
  </optgroup>
</select>&nbsp;<img src="/admin/img/icones/img-icone-buscar-64x64.png" title="<?php echo lang_info_buscar_porta; ?>" width="24" height="24" style="cursor:pointer" onclick="buscar_streaming_revenda();" align="absmiddle" />
</div> <!-- Fim topo-menu-streamings -->
<?php } ?>

<?php if((empty($dados_stm["codigo"]) && $dados_revenda["subrevendas"] > 0) && ($dados_revenda["tipo"] == 1 || $dados_revenda["tipo"] == 2)) { ?>

<div id="topo-menu-subrevendas" class="texto_padrao_pequeno"><strong><?php echo lang_info_gerenciar_subrevenda; ?></strong><br />
<select class="topo-menu-subrevendas-select" id="subrevendas" onchange="selecionar_subrevenda_gerenciamento(this.value);">
<option value="" selected="selected"><?php echo lang_info_escolha_subrevenda; ?></option>
  <optgroup label="<?php echo lang_info_subrevendas_ativas; ?>">
<?php
$i = 0;
$sql = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_revenda["codigo"]."' AND status = '1' AND tipo = '".$tipo_subrevenda."' ORDER by codigo ASC");
while ($dados_subrevendas_lista = mysql_fetch_array($sql)) {

	echo "<option value='".code_decode($dados_subrevendas_lista["codigo"],"E")."' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."'>Código: ".$dados_subrevendas_lista["id"]." - ".str_replace("999999","Ilimitados",$dados_subrevendas_lista["streamings"])." streamings/".str_replace("999999","Ilimitados",$dados_subrevendas_lista["ouvintes"])." ouvintes/".tamanho($dados_subrevendas_lista["espaco"])."/".$dados_subrevendas_lista["bitrate"]."Kbps</option>";

$i++;
}
?>
  </optgroup>
  <optgroup label="<?php echo lang_info_subrevendas_bloqueadas; ?>">
<?php
$sql = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_revenda["codigo"]."' AND status != '1' AND tipo = '".$tipo_subrevenda."' ORDER by codigo ASC");
while ($dados_subrevendas_lista = mysql_fetch_array($sql)) {
	
	if($dados_subrevendas_lista["status"] == '3') {
	
	echo "<option value='".code_decode($dados_subrevendas_lista["codigo"],"E")."' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."'>Código: ".$dados_subrevendas_lista["id"]." - ".str_replace("999999","Ilimitados",$dados_subrevendas_lista["streamings"])." streamings/".str_replace("999999","Ilimitados",$dados_subrevendas_lista["ouvintes"])." ouvintes/".tamanho($dados_subrevendas_lista["espaco"])."/".$dados_subrevendas_lista["bitrate"]."Kbps</option>";
	
	} else {
	
	echo "<option value='".code_decode($dados_subrevendas_lista["codigo"],"E")."' style='background-color: ".zebrar($i, "#F5F5F5", "#FFFFFF")."' disabled='disabled'>Código: ".$dados_subrevendas_lista["id"]." - ".str_replace("999999","Ilimitados",$dados_subrevendas_lista["streamings"])." streamings/".str_replace("999999","Ilimitados",$dados_subrevendas_lista["ouvintes"])." ouvintes/".tamanho($dados_subrevendas_lista["espaco"])."/".$dados_subrevendas_lista["bitrate"]."Kbps</option>";
	
	}
	
$i++;
}
?>
  </optgroup>
</select>&nbsp;<img src="/admin/img/icones/img-icone-buscar-64x64.png" title="<?php echo lang_info_buscar_subrevenda; ?>" width="24" height="24" style="cursor:pointer" onclick="buscar_subrevenda();" align="absmiddle" />
</div> <!-- Fim topo-menu-subrevendas -->
<?php } ?>

<?php if($query_stg_1 == "subrevenda" && !empty($query_stg_2)) { ?>
<div id="topo-menu-gerenciamento-subrevenda" class="texto_padrao_pequeno"><strong><?php echo lang_info_executar_acao; ?> <?php echo $dados_subrevenda_atual["id"]; ?></strong><br />
<select class="topo-menu-gerenciamento-subrevenda-select" id='<?php echo query_string('3'); ?>' onchange='executar_acao_subrevenda(this.id,this.value);'>
  <option value='' selected='selected'><?php echo lang_info_escolha_acao; ?></option>
  <optgroup label='<?php echo lang_acao_label_admin; ?>'>
  <option value='informacoes'><?php echo lang_acao_subrevenda_informacoes; ?></option>
  <option value='configurar'><?php echo lang_acao_subrevenda_alterar_config; ?></option>
  <option value='listar-streamings'><?php echo lang_acao_subrevenda_adm_streamings; ?></option>
  <option value='bloquear'><?php echo lang_acao_subrevenda_adm_bloquear; ?></option>
  <option value='desbloquear'><?php echo lang_acao_subrevenda_adm_desbloquear; ?></option>
  <option value='remover'><?php echo lang_acao_subrevenda_adm_remover; ?></option>
  </optgroup>
</select>
</div> <!-- Fim topo-menu-gerenciamento-subrevenda -->
<?php } ?>

<?php if(!empty($dados_stm["codigo"])) { ?>

<div id="topo-menu-gerenciamento-streaming" class="texto_padrao_pequeno"><strong><?php echo lang_info_executar_acao; ?> <?php echo $dados_stm["porta"]; ?></strong><br />
<select class="topo-menu-gerenciamento-streaming-select" id='<?php echo query_string('2'); ?>' onchange='executar_acao_streaming_revenda(this.id,this.value);' <?php if($dados_servidor["status"] == "off") { echo 'disabled="disabled"'; }?>>
  <option value='' selected='selected'><?php echo lang_info_escolha_acao; ?></option>
<?php if($dados_stm["status"] == '1') { ?>
  <optgroup label='<?php echo lang_acao_label_stm; ?>'>
  <option value='streaming-ligar'><?php echo lang_acao_stm_ligar; ?></option>
  <option value='streaming-desligar'><?php echo lang_acao_stm_desligar; ?></option>
  <option value='streaming-informacoes'><?php echo lang_acao_stm_info; ?></option>
  <option value='streaming-ativar-protecao'><?php echo lang_acao_stm_protecao; ?></option>
  <option value='streaming-logs-servidor'><?php echo lang_acao_stm_logs_servidor; ?></option>
  </optgroup>
  <?php if($dados_stm["autodj"] == "sim") { ?>
  <optgroup label='<?php echo lang_acao_label_autodj; ?>'>
  <option value='autodj-ligar'><?php echo lang_acao_stm_ligar; ?></option>
  <option value='autodj-desligar'><?php echo lang_acao_stm_desligar; ?></option>
  <option value='autodj-logs-servidor'><?php echo lang_acao_autodj_logs_servidor; ?></option>
  </optgroup>
  <?php } ?> 
  <optgroup label='<?php echo lang_acao_label_solucao_problemas; ?>'>
  <?php if($dados_stm["aacplus"] == 'sim') { ?>
  <option value='solucao-problemas-sincronizar-aacplus'><?php echo lang_acao_solucao_problemas_sincronizar_aacplus; ?></option>
  <?php } ?>
  <!--
  <option value='solucao-problemas-player-facebook'><?php echo lang_acao_solucao_problemas_player_facebook; ?></option>
  -->
  <option value='solucao-problemas-encoder'><?php echo lang_acao_solucao_problemas_encoder; ?></option>
  <?php if($dados_stm["autodj"] == "sim") { ?>
  <option value='solucao-problemas-diagnosticar-autodj'><?php echo lang_acao_solucao_problemas_diagnosticar_autodj; ?></option>
  <option value='solucao-problemas-sincronizar-playlists'><?php echo lang_acao_solucao_problemas_sincronizar_playlists; ?></option>
  <?php } ?> 
  </optgroup>
<?php } ?>
  <optgroup label='<?php echo lang_acao_label_admin; ?>'>
  <option value='admin-acessar-painel-streaming'><?php echo lang_acao_stm_acessar_painel_streaming; ?></option>
  <option value='admin-configurar'><?php echo lang_acao_stm_alterar_config; ?></option>
  <option value='admin-mover-streaming'><?php echo lang_acao_stm_adm_mover; ?></option>
  <option value='admin-bloquear'><?php echo lang_acao_stm_adm_bloquear; ?></option>
  <option value='admin-desbloquear'><?php echo lang_acao_stm_adm_desbloquear; ?></option>
  <option value='admin-logs'><?php echo lang_acao_stm_adm_logs; ?></option>
  <option value='admin-remover'><?php echo lang_acao_stm_adm_remover; ?></option>
  </optgroup>
</select>
</div> <!-- Fim topo-menus-streaming-gerenciamento -->

</div> <!-- Fim topo-menus -->
<?php if(!empty($dados_stm["codigo"]) && $dados_stm["status"] == '1') { ?>
<div id="topo-estatisticas">

<div id="topo-estatisticas-ouvintes" class="texto_padrao_pequeno"><strong><?php echo lang_info_ouvintes_conectados; ?></strong><br /><br />
<span id="estatistica_uso_plano_ouvintes" style="cursor:pointer" onclick="estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','nao');"></span>
</div>

<div id="topo-estatisticas-ftp" class="texto_padrao_pequeno"><strong><?php echo lang_info_uso_ftp; ?></strong><br /><br />
<span id="estatistica_uso_plano_ftp" style="cursor:pointer" onclick="estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao');"></span>
</div>

</div>  <!-- Fim topo-estatisticas -->

<div id="topo-musica-player">

<div id="topo-musica-atual" class="texto_padrao_pequeno" onclick="musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual', '25');"><strong><?php echo lang_info_musica_atual; ?></strong><br /><br />
<span id="musica_atual"></span>
</div>

</div> <!-- Fim topo-musica-player -->
<?php } ?>

<?php
} else {
// Fim topo-menus
echo "</div>"; 
}
?>

<?php if($query_stg_1 == "subrevenda" && !empty($query_stg_2)) { ?>
<div id="topo-estatisticas-subrevenda">
<table width="345" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td width="115" height="100" align="center" scope="col"><input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="80" data-height="80" value="<?php echo round($porcentagem_uso_plano_stm); ?>" id="grafico_uso_plano_stm" /></td>
              <td width="115" height="100" align="center" scope="col"><input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="80" data-height="80" value="<?php echo round($porcentagem_uso_plano_ouvintes); ?>" id="grafico_uso_plano_ouvintes" /></td>
              <td width="115" height="100" align="center" scope="col"><input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="80" data-height="80" value="<?php echo round($porcentagem_uso_plano_espaco_ftp); ?>" id="grafico_uso_plano_espaco_ftp" /></td>
            </tr>
            <tr>
              <td height="20" align="center" class="texto_padrao_pequeno" scope="col"><?php echo lang_info_pagina_informacoes_subrevenda_stats_plano_stm; ?></td>
              <td height="20" align="center" class="texto_padrao_pequeno" scope="col"><?php echo lang_info_pagina_informacoes_subrevenda_stats_plano_ouvintes; ?></td>
              <td height="20" align="center" class="texto_padrao_pequeno" scope="col"><?php echo lang_info_pagina_informacoes_subrevenda_stats_plano_espaco_ftp; ?></td>
            </tr>
          </table>
</div> <!-- Fim topo-estatisticas-subrevenda -->
<script type="text/javascript">
// Barra de Progresso Ouvintes
$(function() {
	$(".knob").knob();
	document.getElementById('grafico_uso_plano_stm').value=document.getElementById('grafico_uso_plano_stm').value+'%';
	document.getElementById('grafico_uso_plano_ouvintes').value=document.getElementById('grafico_uso_plano_ouvintes').value+'%';
	document.getElementById('grafico_uso_plano_espaco_ftp').value=document.getElementById('grafico_uso_plano_espaco_ftp').value+'%';
});
</script>
<?php } ?>

<div id="topo-botao-sair">
<img src="/admin/img/icones/img-icone-fechar.png" title="<?php echo lang_titulo_sair; ?>" width="24" height="24" style="cursor:pointer" onclick="window.location = '/admin/sair'" />
</div>

<div id="topo-status" class="texto_padrao">
<span id="status_streaming" style="cursor:pointer" onclick="status_streaming('<?php echo $porta_code; ?>')"></span>
</div>

</div> <!-- Fim topo -->

</div> <!-- Fim topo -->


<!-- Início iframe conteúdo -->
<?php if($query_stg_1 == "subrevenda" && !empty($query_stg_2)) { ?>
<iframe name="conteudo" id="conteudo" src="/admin/revenda-subrevenda-informacoes/<?php echo query_string('3'); ?>" frameborder="0" width="100%" height="500"></iframe>
<?php } elseif($query_stg_1 != "subrevenda" && !empty($dados_stm["codigo"])) { ?>
<iframe name="conteudo" id="conteudo" src="/admin/revenda-streaming-informacoes/<?php echo query_string('2'); ?>" frameborder="0" width="100%" height="500"></iframe>
<?php } else { ?>
<iframe name="conteudo" id="conteudo" src="/admin/revenda-informacoes" frameborder="0" width="100%" height="500"></iframe>
<?php } ?>
<!-- Fim iframe conteúdo -->
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</body>
</html>