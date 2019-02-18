<?php
ini_set("max_execution_time", 0);
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ssh.php");

$porta_code = code_decode($_SESSION["porta_logada"],"E");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_playlist_selecionada = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".code_decode(query_string('1'),"D")."'"));
$total_musicas_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."'"));
$duracao = mysql_fetch_array(mysql_query("SELECT SUM(duracao_segundos) as total FROM playlists_musicas where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."'"));

if($_POST["playlist"] != "" && $_POST["pastas"] == "") {

$_SESSION["resuldado_final"] =  "<span class='texto_status_alerta'>".$lang['lang_acao_gerenciador_playlists_basico_resultado_alerta']."</span>";

header("Location: /gerenciar-playlists-basico/".query_string('1')."");
exit();

}

// Salva a Playlist
if($_POST["playlist"]) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$_POST["playlist"]."'"));

foreach($_POST["pastas"] as $pasta) {

$xml_musicas_pasta = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$pasta."&ordenar=nao");
	
$total_musicas_pasta = count($xml_musicas_pasta->musica);

if($total_musicas_pasta > 0) {

	for($i=0;$i<$total_musicas_pasta;$i++){
	
		$array_musicas[] = $pasta."/".$xml_musicas_pasta->musica[$i]->nome."|".utf8_decode($xml_musicas_pasta->musica[$i]->nome)."|".$xml_musicas_pasta->musica[$i]->duracao."|".$xml_musicas_pasta->musica[$i]->duracao_segundos;
		
	}
}

}

if($_POST["misturar"] == 'sim') {
shuffle($array_musicas);
} else {
sort($array_musicas);
}


$total_musicas_atuais_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_selecionada["codigo"]."'"));

$contagem = $total_musicas_atuais_playlist;

// Adiciona as musicas da playlist ao banco de dados
foreach($array_musicas as $dados) {

list($path, $musica, $duracao, $duracao_segundos) = explode("|",$dados);

// Adiciona música na playlist
mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".addslashes($path)."','".addslashes($musica)."','".$duracao."','".$duracao_segundos."','musica','".$contagem."')") or die("Ooops! Ocorreu um erro no mysql: ".mysql_error());

// Adiciona a música na lista para adicionar ao arquivo da playlist
$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$path."\n";

$contagem++;
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

$_SESSION["resuldado_final"] =  "<span class='texto_status_sucesso'>".$lang['lang_acao_gerenciador_playlists_basico_resultado_ok']."</span>";

header("Location: /gerenciar-playlists-basico/".query_string('1')."");
exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciar Playlists(Básico)</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	<?php if($_SESSION["resuldado_final"]) { ?>
    document.getElementById('log-sistema-conteudo').innerHTML = "<?php echo $_SESSION["resuldado_final"]; ?>";
    document.getElementById('log-sistema-fundo').style.display = "block";
    document.getElementById('log-sistema').style.display = "block";
	<?php unset($_SESSION["resuldado_final"]); ?>
	<?php } else { ?>
	fechar_log_sistema();
	<?php } ?>
   };
</script>
</head>

<body> 
<div id="sub-conteudo">
  <form method="post" action="/gerenciar-playlists-basico/<?php echo query_string('1'); ?>" style="padding:0px; margin:0px" name="gerenciador" enctype="multipart/form-data">
    <table width="890" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:10px; margin-bottom:10px;">
      <tr>
        <td width="217" scope="col"><table width="200" border="0" align="left" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_basico_playlist_atual']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao" scope="col"><?php echo $dados_playlist_selecionada["nome"]; ?></td>
          </tr>
        </table></td>
        <td width="230" align="center" scope="col"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_basico_playlists']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col">
            <select name="gerenciar_playlist" class="input" id="gerenciar_playlist" style="width:190px;" onchange="abrir_log_sistema();window.location = '/gerenciar-playlists-basico/'+this.value+'';">
            <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_basico_playlists']; ?>">
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
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_basico_musicas_playlist']; ?></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col"><?php echo $total_musicas_playlist; ?></td>
          </tr>
        </table></td>
        <td width="217" align="center" scope="col"><table width="200" border="0" align="right" cellpadding="0" cellspacing="0" style="border: #CCCCCC 1px solid;">
          <tr>
            <td width="" height="25" align="center" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_basico_tempo_execucao']; ?>
                <input name="tempo" type="hidden" id="tempo" value="0" /></td>
          </tr>
          <tr>
            <td height="40" align="center" class="texto_padrao_titulo" scope="col"><?php echo gmdate("H:i:s", $duracao["total"]); ?></td>
          </tr>
        </table></td>
      </tr>
    </table>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px; margin-bottom:30px; border: #CCCCCC 1px solid;">
      <tr>
        <td height="210" colspan="2" align="left" class="texto_padrao_destaque" style="padding-left:5px;padding-right:5px;"><select name="pastas[]" multiple="multiple" class="input" id="pastas" style="width:100%; height:200px">
            <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_basico_opcao_pastas']; ?>">
            <?php
$xml_pastas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-pastas.php?porta=".$dados_stm["porta"]."");
	
$total_pastas = count($xml_pastas->pasta);

if($total_pastas > 0) {

	for($i=0;$i<$total_pastas;$i++){
	
		echo '<option value="' . $xml_pastas->pasta[$i]->nome . '">' . $xml_pastas->pasta[$i]->nome . ' (' . $xml_pastas->pasta[$i]->total . ')</option>';
	
	}
	
}
?>
            </optgroup>
                  </select>        </td>
      </tr>
      <tr>
        <td width="444" height="35" align="left" class="texto_padrao" style="padding-left:5px;"><input name="misturar" type="checkbox" id="misturar" value="sim" checked="checked" />          &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_basico_misturar_musicas']; ?></td>
        <td width="444" align="right" class="texto_padrao" style="padding-right:5px;"><img src="/img/icones/img-icone-salvar.png" width="16" height="16" align="absmiddle" onclick="misturar_musicas('lista-musicas-playlist');" />&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('log-sistema-conteudo').innerHTML = '<img src=\'/img/ajax-loader.gif\' />';document.getElementById('log-sistema-fundo').style.display = 'block';document.getElementById('log-sistema').style.display = 'block';document.gerenciador.submit();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_basico_botao_salvar_playlists']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:location.reload();" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_basico_botao_recarregar_pastas']; ?></a>
        <input name="playlist" type="hidden" id="playlist" value="<?php echo $dados_playlist_selecionada["codigo"]; ?>" /></td>
      </tr>
    </table>
  </form>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="860" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_basico_info']; ?></td>
  </tr>
</table>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
      <td width="860" align="left" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_basico_info_multi_selecao']; ?></td>
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