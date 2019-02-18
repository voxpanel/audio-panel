<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");
require_once("admin/inc/classe.ssh.php");

$porta_code = code_decode($_SESSION["porta_logada"],"E");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_playlist_selecionada = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".code_decode(query_string('1'),"D")."'"));

// Salva a Playlist
if($_POST["playlist"]) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$_POST["playlist"]."'"));

// Remove as músicas atuais da playlist para gravar as novas música
mysql_query("DELETE FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");

if(count($_POST["musicas_adicionadas"]) > 0) {

// Adiciona as musicas da playlist ao banco de dados
foreach($_POST["musicas_adicionadas"] as $ordem => $musica) {

list($path, $musica, $duracao, $duracao_segundos, $tipo) = explode("|",$musica);

// Adiciona música na playlist
mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".addslashes($path)."','".addslashes($musica)."','".$duracao."','".$duracao_segundos."','".$tipo."','".$ordem."')") or die("Ooops! Ocorreu um erro no mysql: ".mysql_error());

// Adiciona a música na lista para adicionar ao arquivo da playlist
if($tipo == "hc") {
$lista_musicas .= $path."\n";
} else {
$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$path."\n";
}
}

// Cria o arquivo da playlist para enviar ao servidor do streaming
$config_playlist = gerar_playlist($dados_playlist["arquivo"],$lista_musicas);

// Envia o arquivo da playlist para o servidor do streaming
// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_playlist."","/home/streaming/playlists/".$dados_playlist["arquivo"]."",0777);

// Remove o arquivo temporário usado para criar a playlist
unlink("/home/painel/public_html/temp/".$config_playlist."");

$resuldado_final = "<span class='texto_status_sucesso'>".lang_acao_gerenciador_playlists_tempo_real_resultado_ok."</span>";

}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciar Playlists</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/ajax-streaming-playlists-tempo-real.js"></script>
<script type="text/javascript">
   window.onload = function() {
    carregar_pastas('<?php echo $porta_code; ?>');
	carregar_musicas_playlist( '<?php echo $dados_playlist_selecionada["codigo"]; ?>' );
	// Atualizar informações do streaming
	musica_atual_proxima_musica( <?php echo $dados_stm["porta"]; ?>,'50');
	setInterval("musica_atual_proxima_musica( <?php echo $dados_stm["porta"]; ?>,'50')",5000);
	<?php if($resuldado_final) { ?>
    document.getElementById('log-sistema-conteudo').innerHTML = "<?php echo $resuldado_final; ?>";
    document.getElementById('log-sistema-fundo').style.display = "block";
    document.getElementById('log-sistema').style.display = "block";
	<?php } else { ?>
	fechar_log_sistema();
	<?php } ?>
   };
</script>
</head>

<body> 
<div id="sub-conteudo">
  <form method="post" action="/gerenciar-playlists-tempo-real/<?php echo query_string('1'); ?>" style="padding:0px; margin:0px" name="gerenciador" enctype="multipart/form-data">
    <table width="890" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:5px; margin-bottom:10px;">
      <tr>
        <td width="285" scope="col"><table width="280" border="0" align="left" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_musica_atual']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_pequeno" scope="col"><span id="musica_atual"></span></td>
          </tr>
        </table></td>
        <td width="285" scope="col"><table width="280" border="0" align="right" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_proxima_musica']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_pequeno" scope="col"><span id="proxima_musica"></span></td>
          </tr>
        </table></td>
        <td width="160" scope="col"><table width="150" border="0" align="right" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_executar_acao']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col">
            <select name="executar_acao" class="input" id="executar_acao" style="width:145px;" onchange="">
            <option value=""><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_executar_acao']; ?></option>
            <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_executar_acao_acao']; ?>">
            <option value="pular-musica"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_executar_acao_pular_musica']; ?></option>
            </optgroup>
            <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_playlists']; ?>">
            <?php

			$query = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
			while ($dados_playlist_gerenciar = mysql_fetch_array($query)) {

				$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_gerenciar["codigo"]."'"));

				$playlist_gerenciar_code = code_decode($dados_playlist_gerenciar["codigo"],"E");

				echo '<option value="' . $playlist_gerenciar_code . '">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_musicas . ')</option>';

			}
			?>
            </optgroup>
            </select>
            </td>
          </tr>
        </table></td>
        <td width="160" scope="col"><table width="150" border="0" align="right" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_tempo_execucao']; ?>
                <input name="tempo" type="hidden" id="tempo" value="0" /></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col"><span id="tempo_playlist">00:00:00</span></td>
          </tr>
        </table></td>
      </tr>
    </table>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2"><div style="width:272px; text-align:left; float:left; padding:5px 0px 5px 5px; overflow: auto;" class="texto_padrao_destaque"> <?php echo $lang['lang_info_gerenciador_playlists_tempo_real_pastas']; ?> </div>
            <div style="width:20px; text-align:left; float:left; padding:5px 0px 5px 5px; overflow: auto;" class="texto_padrao_destaque"> <img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" align="absmiddle" onclick="hide_show('quadro_pastas_musicas_ftp');" style="cursor:pointer" title="Ocultar/Hide" /> </div>
          <div style="width:372px; text-align:right; float:right; padding:5px 0px 5px 0px; overflow: auto;" class="texto_padrao_vermelho">
              <input name="ordenar_musicas_pasta" id="ordenar_musicas_pasta" type="checkbox" value="sim" style="vertical-align: middle;" checked="checked" />
            &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_musicas_pasta_ordenar']; ?> </div>
          <div style="width:200px; text-align:left; float:right; padding:5px 0px 5px 0px; overflow: auto;" class="texto_padrao_destaque"> <?php echo $lang['lang_info_gerenciador_playlists_tempo_real_musicas_pasta']; ?> </div></td>
      </tr>
      <tr>
        <td colspan="2" align="left" valign="top"><div id="quadro_pastas_musicas_ftp" style="display:block">
            <div id="quadro_lista_pastas" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:285px; height:150px; text-align:left; float:left; padding:5px; overflow: auto; resize: vertical"> <span id="status_lista_pastas" class="texto_padrao_pequeno"></span>
                <ul id="lista-pastas">
                </ul>
            </div>
          <div id="musicas_ftp" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:560px; height:150px; text-align:left; float:right; padding:5px; overflow: auto; resize: vertical"> <span id="msg_pasta" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_info_lista_musicas_pasta']; ?></span>
                <ul id="lista-musicas-pasta">
                </ul>
          </div>
          <div style="width:297px; text-align:right; float:left; padding:5px 0px 5px 0px; overflow: auto;"> <img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_pastas('<?php echo $porta_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_recarregar_pastas']; ?></a></div>
          <div style="width:572px; text-align:right; float:right; padding:5px 0px 5px 0px; overflow: auto;"><img src="/img/icones/img-icone-buscar-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:buscar_musica_pasta();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_buscar_musica']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-pasta-adicionar.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:adicionar_tudo();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_adicionar_tudo']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-lixo.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:limpar_lista_musicas('ftp');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_limpar_lista']; ?></a></div>
        </div></td>
      </tr>
      
      <tr>
        <td colspan="2"><div style="width:883px; text-align:left; float:left; padding:5px 0px 9px 5px; overflow: auto;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_musicas_playlist']; ?></div>
        <div id="musicas_playlist" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:877px; height:550px; text-align:left; float:left; padding:5px; overflow: auto; resize: vertical">
        <span id="msg_playlist" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_info_lista_musicas_playlists']; ?></span>
        <span id="msg_playlist_nova" class="texto_padrao_pequeno" style="display:none"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_info_lista_musicas_playlist_nova']; ?></span>
        <ul id="lista-musicas-playlist">
        </ul>
        </div>
        </td>
      </tr>
      <tr>
        <td width="310" height="30" align="left"><img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_musicas_playlist( '<?php echo $dados_playlist_selecionada["codigo"]; ?>' );" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_recarregar_playlists']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-janela-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:abrir_janela('/gerenciar-playlists-tempo-real/<?php echo query_string('1'); ?>',920,650 );" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_nova_janela']; ?></a></td>
        <td width="580" height="30" align="right"><img src="/img/icones/img-icone-salvar.png" width="16" height="16" align="absmiddle" onclick="misturar_musicas('lista-musicas-playlist');" />&nbsp;<a href="javascript:salvar_playlist();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_salvar_playlists']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-shuffle-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:misturar_musicas('lista-musicas-playlist');" class="texto_padrao_vermelho"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_misturar_musicas']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-lixo.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:limpar_lista_musicas('playlist');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_botao_limpar_lista']; ?></a>
        <input name="playlist" type="hidden" id="playlist" value="<?php echo $dados_playlist_selecionada["codigo"]; ?>" /></td>
      </tr>
    </table>
  </form>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; border: #CCCCCC 1px solid">
    <tr>
      <td width="138" height="25" align="center" class="texto_padrao_pequeno" scope="col"><img src="/img/icones/img-icone-arquivo-musica.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_legenda_musica']; ?></td>
      <td width="138" align="center" class="lista-musicas-playlist-vinheta" scope="col"><img src="/img/icones/img-icone-vinheta.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_legenda_vinheta']; ?></td>
      <td width="138" align="center" class="lista-musicas-playlist-comercial" scope="col"><img src="/img/icones/img-icone-vinheta.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_legenda_comercial']; ?></td>
      <td width="138" align="center" class="lista-musicas-playlist-hora-certa" scope="col"><img src="/img/icones/img-icone-hora-certa.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_legenda_hora_certa']; ?></td>
      <td width="138" align="center" class="lista-musicas-playlist-intercalado" scope="col"><img src="/img/icones/img-icone-musica-intercalado.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_tempo_real_legenda_intercalado']; ?></td>
    </tr>
  </table>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_info_timezone']; ?></td>
  </tr>
</table>
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_real_info_caracteres_especiais']; ?></td>
  </tr>
</table>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>