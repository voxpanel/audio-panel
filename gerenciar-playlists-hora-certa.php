<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ssh.php");

if($_POST["codigo_playlist"]) {
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".code_decode($_POST["codigo_playlist"],"D")."'"));
} else {
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".code_decode(query_string('1'),"D")."'"));
}

if($_POST["cadastrar"]) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($_POST["voz"] == "masculino") {
$voz_hora_certa = '#!/home/streaming/hora-certa masculino '.$dados_stm["timezone"].'';
$legenda_hora_certa = $lang['lang_info_gerenciador_playlists_hora_certa_legenda_voz_masculina'];
} elseif($_POST["voz"] == "masculino_es") {
$voz_hora_certa = '#!/home/streaming/hora-certa masculino_es '.$dados_stm["timezone"].'';
$legenda_hora_certa = $lang['lang_info_gerenciador_playlists_hora_certa_legenda_voz_masculina'];
} else {
$voz_hora_certa = '#!/home/streaming/hora-certa feminino '.$dados_stm["timezone"].'';
$legenda_hora_certa = $lang['lang_info_gerenciador_playlists_hora_certa_legenda_voz_feminina'];
}

if($_POST["frequencia_tipo"] == "musicas") {

// Remove a hora certa atual
mysql_query("DELETE FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'hc'");

$contador = 1;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

if($contador == $_POST["frequencia"]) {

$ordem = "".$dados_musica["ordem"].".1";

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$voz_hora_certa."','".$legenda_hora_certa."','00:00:05','5','hc','".$ordem."')");

$contador = 0;
}

$contador++;

}

} else {

// Remove a hora certa atual
mysql_query("DELETE FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'hc'");

$contador = 0;

$segundos = $_POST["frequencia"]*60;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

$contador += $dados_musica["duracao_segundos"];

if($contador >= $segundos) {

$ordem = "".$dados_musica["ordem"].".1";

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$voz_hora_certa."','".$legenda_hora_certa."','00:00:05','5','hc','".$ordem."')");

$contador = 0;
}

}

}

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

// Adiciona a música na lista para adicionar ao arquivo da playlist
if($dados_musica["tipo"] == "hc") {
$lista_musicas .= $dados_musica["path_musica"]."\n";
} else {
$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$dados_musica["path_musica"]."\n";
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

// Marca o Hora Certa como ativado na playlist
mysql_query("Update playlists set hora_certa = 'sim' where codigo = '".$dados_playlist["codigo"]."'");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_info_gerenciador_playlists_hora_certa_resultado_ok']."","ok");

header("Location: /playlists");
exit();
}

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
<div id="sub-conteudo-pequeno">
<form method="post" action="/gerenciar-playlists-hora-certa/<?php echo code_decode($dados_playlist["codigo"],"E"); ?>" style="padding:0px; margin:0px" name="hora-certa">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_info']; ?></td>
      </tr>
    </table>
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
<?php if(query_string('1') == "") { ?>
      <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_playlist']; ?></td>
        <td width="560" align="left" class="texto_padrao">
          <select name="codigo_playlist" class="input" id="codigo_playlist" style="width:255px;">
<?php

$query = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
while ($dados_playlist_gerenciar = mysql_fetch_array($query)) {

$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_gerenciar["codigo"]."'"));

$playlist_code = code_decode($dados_playlist_gerenciar["codigo"],"E");

echo '<option value="' . $playlist_code . '">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_musicas . ')</option>';


}
?>
            </select>
          </td>
      </tr>
<?php } ?>
      <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_voz']; ?></td>
        <td width="560" align="left" class="texto_padrao">
          <select name="voz" class="input" id="voz" style="width:255px;">
          <optgroup label="Português">
            <option value="masculino"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_voz_masculina']; ?></option>
            <option value="feminino"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_voz_feminina']; ?></option>
          </optgroup>
          <optgroup label="Español">
            <option value="masculino_es"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_voz_masculina']; ?></option>
          </optgroup>
          </select>
          </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_frequencia']; ?></td>
        <td align="left" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_frequencia_executar']; ?>&nbsp;<input name="frequencia" type="text" class="input" id="frequencia" style="width:40px;" value="0" />
        <select name="frequencia_tipo" class="input" id="frequencia_tipo" style="width:116px;">
            <option value="musicas"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_frequencia_musicas']; ?></option>
            <option value="minutos"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_frequencia_minutos']; ?></option>
          </select>
        <img src="/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_playlists_hora_certa_frequencia_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
      
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_configurar']; ?>" />&nbsp;<input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_voltar']; ?>" onclick="window.location = '/playlists';" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />
          </td>
      </tr>
    </table>
    </div>
    </div>
  </form>
<br />
        <div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_instrucoes_tab_titulo']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_hora_certa_instrucoes']; ?></td>
                </tr>
              </table>
          </div>
        </div>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
