<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ssh.php");

if($_POST["codigo_playlist"]) {
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".code_decode($_POST["codigo_playlist"],"D")."'"));
} else {
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".code_decode(query_string('1'),"D")."'"));
}

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if($_POST["executar"]) {

if($_POST["pasta_musicas"] != "") {

$xml_musicas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$_POST["pasta_musicas"]."&ordenar=nao");
	
$total_musicas = count($xml_musicas->musica);

if($total_musicas > 0) {

	for($i=0;$i<$total_musicas;$i++){
	
		$array_musicas[] = utf8_decode($xml_musicas->musica[$i]->nome)."|".$xml_musicas->musica[$i]->duracao."|".$xml_musicas->musica[$i]->duracao_segundos;
	
	}
}

}

// Músicas
if($total_musicas > 0 && $_POST["pasta_musicas"] != "") {

if($_POST["frequencia_tipo"] == "musicas") {

$total_musicas = count($array_musicas);

$contador_musicas = 1;
$contador_insercoes = 0;
$contador_musicas_inseridas = 0;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

if($contador_musicas == $_POST["frequencia_musicas2"]) {

for($i=1; $i < $_POST["frequencia_musicas"]+1; $i++) {

$contador_musicas_inseridas++;

$ordem = "".$dados_musica["ordem"].".".$i."";

list($musica, $duracao, $duracao_segundos) = explode("|",$array_musicas[$contador_insercoes]);

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$_POST["pasta_musicas"]."/".$musica."','".$musica."','".$duracao."','".$duracao_segundos."','intercalado','".$ordem."')");

if($contador_musicas_inseridas == $total_musicas) {
$contador_insercoes = 0;
$contador_musicas_inseridas = 0;
} else {
$contador_insercoes++;
}

}

$contador_musicas = 0;
}

$contador_musicas++;
}

} else { // if tipo frequencia -> minutos

$total_musicas = count($array_musicas);

$contador_musicas = 0;
$contador_insercoes = 0;
$contador_musicas_inseridas = 1;

$segundos = $_POST["frequencia_musicas2"]*60;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

$contador_musicas += $dados_musica["duracao_segundos"];

if($contador_musicas >= $segundos) {

for($i=1; $i < $_POST["frequencia_musicas"]+1; $i++) {

$ordem = "".$dados_musica["ordem"].".".$i."";

list($musica, $duracao, $duracao_segundos) = explode("|",$array_musicas[$contador_insercoes]);

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$_POST["pasta_musicas"]."/".$musica."','".$musica."','".$duracao."','".$duracao_segundos."','intercalado','".$ordem."')");

if($contador_musicas_inseridas == $total_musicas) {
$contador_insercoes = 0;
$contador_musicas_inseridas = 1;
} else {
$contador_insercoes++;
$contador_musicas_inseridas++;
}

}

$contador_musicas = 0;
}

$contador_musicas++;
}

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".lang_info_gerenciador_playlists_intercalar_musicas_resultado_ok."","ok");

}


if($total_musicas > 0) {
// Cria o arquivo da playlist para enviar para o servidor
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

}

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
<form method="post" action="/gerenciar-playlists-intercalar-musicas/<?php echo code_decode($dados_playlist["codigo"],"E"); ?>" style="padding:0px; margin:0px" name="hora-certa">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
<?php if(query_string('1') == "") { ?>
      <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_playlist']; ?></td>
        <td width="510" align="left" class="texto_padrao">
          <select name="codigo_playlist" class="input" id="codigo_playlist" style="width:410px;">
<?php

$query = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
while ($dados_playlist_gerenciar = mysql_fetch_array($query)) {

$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_gerenciar["codigo"]."'"));

$playlist_code = code_decode($dados_playlist_gerenciar["codigo"],"E");

echo '<option value="' . $playlist_code . '">' . $dados_playlist_gerenciar["nome"] . ' (' . $total_musicas . ')</option>';


}
?>
            </select>          </td>
      </tr>
<?php } ?>
	  <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_musicas']; ?></td>
        <td align="left" class="texto_padrao">
          <select name="pasta_musicas" class="input" id="pasta_musicas" style="width:410px;">
          <option value="" selected="selected"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_opcao_selecionar_musicas']; ?></option>
          <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_opcao_pastas']; ?>">
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
          </select>          </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_executar']; ?>&nbsp;
          <input name="frequencia_musicas" type="text" class="input" id="frequencia_musicas" style="width:50px;" value="0" />
        &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_a_cada']; ?>&nbsp;
        <input name="frequencia_musicas2" type="text" class="input" id="frequencia_musicas2" style="width:50px;" value="10" />
        <select name="frequencia_tipo" class="input" id="frequencia_tipo" style="width:193px;">
              <option value="musicas"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_frequencia_musica']; ?></option>
              <option value="minutos"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_frequencia_minuto']; ?></option>
            </select>
            <img src="/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_frequencia_info']; ?>');" style="cursor:pointer" /></td>
        </tr>
      
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_configurar']; ?>" />&nbsp;<input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_voltar']; ?>" onclick="window.location = '/playlists';" />
          <input name="executar" type="hidden" id="executar" value="sim" />          </td>
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
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_intercalar_musicas_instrucoes']; ?></td>
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
