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

if(mysql_error()) {
$_SESSION["resuldado"] = "<span class='texto_status_erro'>".$lang['lang_acao_gerenciador_playlists_resultado_erro']."<br>".mysql_error()."</span>";
header("Location: /gerenciar-playlists/".query_string('1')."");
exit();
}

if(count($_POST["musicas_adicionadas"]) > 0) {

// Adiciona as musicas da playlist ao banco de dados
foreach($_POST["musicas_adicionadas"] as $ordem => $musica) {

list($path, $musica, $duracao, $duracao_segundos, $tipo) = explode("|",$musica);

// Adiciona música na playlist
mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".addslashes($path)."','".addslashes($musica)."','".$duracao."','".$duracao_segundos."','".$tipo."','".$ordem."')");

if(mysql_error()) {
$_SESSION["resuldado"] = "<span class='texto_status_erro'>".$lang['lang_acao_gerenciador_playlists_resultado_erro']."<br>".mysql_error()."</span>";
header("Location: /gerenciar-playlists/".query_string('1')."");
exit();
}

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
@unlink("/home/painel/public_html/temp/".$config_playlist."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_gerenciador_playlists_resultado_ok']."","ok");

// Inicia a playlist
if($_POST["iniciar_playlist"] == "sim") {

// Verifica se o autodj esta ligado
$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
if($status_autodj == "ligado") {
	
$resultado = $ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls;/home/streaming/gerenciar_autodj recarregar_playlist ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");

if(preg_match('/'.$dados_stm["porta"].'/i',$resultado)) {
	
// Atualiza a última playlist tocada e o bitrate do autodj
mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".str_replace("<br>"," ",$lang['lang_acao_trocar_playlist_autodj_resultado_ok'])."","ok");

} else {
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".str_replace("<br>"," ",$lang['lang_acao_trocar_playlist_autodj_resultado_erro'])."","erro");
}

} else {
// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_acao_trocar_playlist_autodj_resultado_alerta']."","alerta");
}
}

header("Location: /playlists");
exit();

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
<script type="text/javascript" src="/inc/ajax-streaming-playlists.js"></script>
<script type="text/javascript">
   window.onload = function() {
    carregar_pastas('<?php echo $porta_code; ?>');
	carregar_musicas_playlist( '<?php echo $dados_playlist_selecionada["codigo"]; ?>' );
	<?php if($_SESSION["resuldado"]) { ?>
    document.getElementById('log-sistema-conteudo').innerHTML = "<?php echo $_SESSION["resuldado"]; ?>";
    document.getElementById('log-sistema-fundo').style.display = "block";
    document.getElementById('log-sistema').style.display = "block";
	<?php unset($_SESSION["resuldado"]); ?>
	<?php } else { ?>
	fechar_log_sistema();
	<?php } ?>
   };
</script>
</head>

<body> 
<div id="sub-conteudo">
  <form method="post" action="/gerenciar-playlists/<?php echo query_string('1'); ?>" style="padding:0px; margin:0px" name="gerenciador" enctype="multipart/form-data">
    <table width="890" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:10px; margin-bottom:10px;">
      <tr>
        <td width="217" scope="col"><table width="200" border="0" align="left" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_playlist_atual']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao" scope="col"><?php echo $dados_playlist_selecionada["nome"]; ?></td>
          </tr>
        </table></td>
        <td width="230" align="center" scope="col"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_playlists']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col">
            <select name="gerenciar_playlist" class="input" id="gerenciar_playlist" style="width:190px;" onchange="abrir_log_sistema();window.location = '/gerenciar-playlists/'+this.value+'';">
            <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_playlists']; ?>">
              <?php

$query = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
while ($dados_playlist_gerenciar = mysql_fetch_array($query)) {

$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_gerenciar["codigo"]."'"));

$playlist_gerenciar_code = code_decode($dados_playlist_gerenciar["codigo"],"E");

if($dados_playlist_gerenciar["codigo"] == code_decode(query_string('1'),"D")) {
echo '<option value="' . $playlist_gerenciar_code . '" selected="selected">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_musicas . ')</option>';
} else {
echo '<option value="' . $playlist_gerenciar_code . '">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_musicas . ')</option>';

}

}
?>
            </optgroup>
            </select>
            </td>
          </tr>
        </table></td>
        <td width="230" align="center" scope="col"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;" id="quadro_quantidade_musicas_playlist">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_musicas_playlist']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col"><span id="quantidade_musicas_playlist">0</span></td>
          </tr>
        </table></td>
        <td width="217" align="center" scope="col"><table width="200" border="0" align="right" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_tempo_execucao']; ?>
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
        <td colspan="2"><div style="width:272px; text-align:left; float:left; padding:5px 0px 5px 5px; overflow: auto;" class="texto_padrao_destaque"> <?php echo $lang['lang_info_gerenciador_playlists_pastas']; ?> </div>
            <div style="width:20px; text-align:left; float:left; padding:5px 0px 5px 5px; overflow: auto;" class="texto_padrao_destaque"> <img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" align="absmiddle" onclick="hide_show('quadro_pastas_musicas_ftp');" style="cursor:pointer" title="Ocultar/Hide" /> </div>
          <div style="width:372px; text-align:right; float:right; padding:5px 0px 5px 0px; overflow: auto;" class="texto_padrao_vermelho">
              <input name="ordenar_musicas_pasta" id="ordenar_musicas_pasta" type="checkbox" value="sim" style="vertical-align: middle;" checked="checked" />
            &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_musicas_pasta_ordenar']; ?> </div>
          <div style="width:200px; text-align:left; float:right; padding:5px 0px 5px 0px; overflow: auto;" class="texto_padrao_destaque"> <?php echo $lang['lang_info_gerenciador_playlists_musicas_pasta']; ?> </div></td>
      </tr>
      <tr>
        <td colspan="2" align="left" valign="top"><div id="quadro_pastas_musicas_ftp" style="display:block">
            <div id="quadro_lista_pastas" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:285px; height:200px; text-align:left; float:left; padding:5px; overflow: auto; resize: vertical"> <span id="status_lista_pastas" class="texto_padrao_pequeno"></span>
                <ul id="lista-pastas">
                </ul>
            </div>
          <div id="musicas_ftp" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:560px; height:200px; text-align:left; float:right; padding:5px; overflow: auto; resize: vertical"> <span id="msg_pasta" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_info_lista_musicas_pasta']; ?></span>
                <ul id="lista-musicas-pasta">
                </ul>
          </div>
          <div style="width:297px; text-align:right; float:left; padding:5px 0px 5px 0px; overflow: auto;"> <img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_pastas('<?php echo $porta_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_recarregar_pastas']; ?></a></div>
          <div style="width:572px; text-align:right; float:right; padding:5px 0px 5px 0px; overflow: auto;"> <img src="/img/icones/img-icone-pasta-adicionar.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:adicionar_tudo();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_adicionar_tudo']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-lixo.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:limpar_lista_musicas('ftp');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_limpar_lista']; ?></a></div>
        </div></td>
      </tr>
      
      <tr>
        <td colspan="2"><div style="width:883px; text-align:left; float:left; padding:5px 0px 9px 5px; overflow: auto;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_playlists_musicas_playlist']; ?></div>
        <div id="musicas_playlist" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:877px; height:550px; text-align:left; float:left; padding:5px; overflow: auto; resize: vertical">
        <span id="msg_playlist" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_info_lista_musicas_playlists']; ?></span>
        <span id="msg_playlist_nova" class="texto_padrao_pequeno" style="display:none"><?php echo $lang['lang_info_gerenciador_playlists_info_lista_musicas_playlist_nova']; ?></span>
        <ul id="lista-musicas-playlist">
        </ul>
        </div>
        </td>
      </tr>
      <tr>
        <td width="310" height="30" align="left"><img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_musicas_playlist( '<?php echo $dados_playlist_selecionada["codigo"]; ?>' );" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_recarregar_playlists']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-janela-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:abrir_janela('/gerenciar-playlists/<?php echo query_string('1'); ?>',920,650 );" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_nova_janela']; ?></a></td>
        <td width="580" height="30" align="right"><img src="/img/icones/img-icone-salvar.png" width="16" height="16" align="absmiddle" onclick="salvar_playlist();" />&nbsp;<a href="javascript:salvar_playlist();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_salvar_playlist']; ?></a>&nbsp;&nbsp;<a href="#" onclick="document.getElementById('iniciar_playlist').value = 'sim';salvar_playlist();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_salvar_playlist_iniciar']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-shuffle-64x64.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:misturar_musicas('lista-musicas-playlist');" class="texto_padrao_vermelho"><?php echo $lang['lang_info_gerenciador_playlists_botao_misturar_musicas']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-lixo.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:limpar_lista_musicas('playlist');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_botao_limpar_lista']; ?></a>
        <input name="playlist" type="hidden" id="playlist" value="<?php echo $dados_playlist_selecionada["codigo"]; ?>" />
        <input name="iniciar_playlist" type="hidden" id="iniciar_playlist" value="nao" /></td>
      </tr>
    </table>
  </form>
  <table width="840" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; border: #CCCCCC 1px solid">
    <tr>
      <td width="140" height="25" align="center" class="texto_padrao_pequeno" scope="col"><img src="/img/icones/img-icone-arquivo-musica.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_musica']; ?></td>
      <td width="140" align="center" class="lista-musicas-playlist-vinheta" scope="col"><img src="/img/icones/img-icone-vinheta.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_vinheta']; ?></td>
      <td width="140" align="center" class="lista-musicas-playlist-comercial" scope="col"><img src="/img/icones/img-icone-vinheta.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_comercial']; ?></td>
      <td width="140" align="center" class="lista-musicas-playlist-hora-certa" scope="col"><img src="/img/icones/img-icone-hora-certa.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_hora_certa']; ?></td>
      <td width="140" align="center" class="lista-musicas-playlist-intercalado" scope="col"><img src="/img/icones/img-icone-musica-intercalado.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_intercalado']; ?></td>
      <td width="140" align="center" class="lista-musicas-playlist-vinheta" scope="col"><img src="/img/icones/img-icone-bloqueado.png" width="16" height="16" border="0" align="absmiddle" />&nbsp;<?php echo $lang['lang_info_gerenciador_playlists_legenda_bloqueado']; ?></td>
    </tr>
  </table>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_info_timezone']; ?></td>
  </tr>
</table>
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_info_caracteres_especiais']; ?></td>
  </tr>
</table>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';document.getElementById('log-sistema-conteudo').innerHTML = '';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>