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

if($_POST["cadastrar"]) {

if($_POST["pasta_vinhetas"] != "") {

$xml_vinhetas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$_POST["pasta_vinhetas"]."&ordenar=nao");
	
$total_vinhetas = count($xml_vinhetas->musica);

if($total_vinhetas > 0) {

	for($i=0;$i<$total_vinhetas;$i++){
	
		$array_vinhetas[] = utf8_decode($xml_vinhetas->musica[$i]->nome)."|".$xml_vinhetas->musica[$i]->duracao."|".$xml_vinhetas->musica[$i]->duracao_segundos;
	
	}
}

}

if($_POST["pasta_comerciais"] != "") {

$xml_comerciais = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$_POST["pasta_comerciais"]."&ordenar=nao");
	
$total_comerciais = count($xml_comerciais->musica);

if($total_comerciais > 0) {

	for($i=0;$i<$total_comerciais;$i++){
	
		$array_comerciais[] = utf8_decode($xml_comerciais->musica[$i]->nome)."|".$xml_comerciais->musica[$i]->duracao."|".$xml_comerciais->musica[$i]->duracao_segundos;
	
	}
}

}

// Vinhetas
if($total_vinhetas > 0 && $_POST["pasta_vinhetas"] != "") {

// Remove as vinhetas atuais
mysql_query("DELETE FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'vinheta'");

if($_POST["frequencia_vinhetas_tipo"] == "musicas") {

$total_vinhetas = count($array_vinhetas);

$contador_musicas = 1;
$contador_insercoes = 0;
$contador_vinhetas_inseridas = 0;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

if($contador_musicas == $_POST["frequencia_vinhetas2"]) {

for($i=1; $i < $_POST["frequencia_vinhetas"]+1; $i++) {

$contador_vinhetas_inseridas++;

$ordem = "".$dados_musica["ordem"].".".$i."";

list($vinheta, $duracao, $duracao_segundos) = explode("|",$array_vinhetas[$contador_insercoes]);

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$_POST["pasta_vinhetas"]."/".$vinheta."','".$vinheta."','".$duracao."','".$duracao_segundos."','vinheta','".$ordem."')");

if($contador_vinhetas_inseridas == $total_vinhetas) {
$contador_insercoes = 0;
$contador_vinhetas_inseridas = 0;
} else {
$contador_insercoes++;
}

}

$contador_musicas = 0;
}

$contador_musicas++;
}

} else { // if tipo frequencia -> minutos

$total_vinhetas = count($array_vinhetas);

$contador_musicas = 0;
$contador_insercoes = 0;
$contador_vinhetas_inseridas = 1;

$segundos = $_POST["frequencia_vinhetas2"]*60;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

$contador_musicas += $dados_musica["duracao_segundos"];

if($contador_musicas >= $segundos) {

for($i=1; $i < $_POST["frequencia_vinhetas"]+1; $i++) {

$ordem = "".$dados_musica["ordem"].".".$i."";

list($vinheta, $duracao, $duracao_segundos) = explode("|",$array_vinhetas[$contador_insercoes]);

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$_POST["pasta_vinhetas"]."/".$vinheta."','".$vinheta."','".$duracao."','".$duracao_segundos."','vinheta','".$ordem."')");

if($contador_vinhetas_inseridas == $total_vinhetas) {
$contador_insercoes = 0;
$contador_vinhetas_inseridas = 1;
} else {
$contador_insercoes++;
$contador_vinhetas_inseridas++;
}

}

$contador_musicas = 0;
}

$contador_musicas++;
}

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_info_gerenciador_playlists_vinhetas_comerciais_resultado_vinhetas_ok']."","ok");

}

// Comerciais
if($total_comerciais > 0 && $_POST["pasta_comerciais"] != "") {

// Remove os comerciais atuais
mysql_query("DELETE FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'comercial'");

if($_POST["frequencia_comerciais_tipo"] == "musicas") {

$total_comerciais = count($array_comerciais);

$contador_musicas = 1;
$contador_insercoes = 0;
$contador_comerciais_inseridos = 0;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

if($contador_musicas == $_POST["frequencia_comerciais2"]) {

for($i=1; $i < $_POST["frequencia_comerciais"]+1; $i++) {

$contador_comerciais_inseridos++;

$ordem = "".$dados_musica["ordem"].".".$i."";

list($comercial, $duracao, $duracao_segundos) = explode("|",$array_comerciais[$contador_insercoes]);

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$_POST["pasta_comerciais"]."/".$comercial."','".$comercial."','".$duracao."','".$duracao_segundos."','comercial','".$ordem."')");

if($contador_comerciais_inseridos == $total_comerciais) {
$contador_insercoes = 0;
$contador_comerciais_inseridos = 0;
} else {
$contador_insercoes++;
}

}

$contador_musicas = 0;
}

$contador_musicas++;
}

} else { // if tipo frequencia -> minutos

$total_comerciais = count($array_comerciais);

$contador_musicas = 0;
$contador_insercoes = 0;
$contador_comerciais_inseridos = 1;

$segundos = $_POST["frequencia_comerciais2"]*60;

$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo = 'musica' ORDER by ordem+0 ASC");
while ($dados_musica = mysql_fetch_array($query)) {

$contador_musicas += $dados_musica["duracao_segundos"];

if($contador_musicas >= $segundos) {

for($i=1; $i < $_POST["frequencia_comerciais"]+1; $i++) {

$ordem = "".$dados_musica["ordem"].".".$i."";

list($comercial, $duracao, $duracao_segundos) = explode("|",$array_comerciais[$contador_insercoes]);

mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".$_POST["pasta_comerciais"]."/".$comercial."','".$comercial."','".$duracao."','".$duracao_segundos."','comercial','".$ordem."')");

if($contador_comerciais_inseridos == $total_comerciais) {
$contador_insercoes = 0;
$contador_comerciais_inseridos = 1;
} else {
$contador_insercoes++;
$contador_comerciais_inseridos++;
}

}

$contador_musicas = 0;
}

$contador_musicas++;
}

}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_info_gerenciador_playlists_vinhetas_comerciais_resultado_comerciais_ok']."","ok");

}

if($total_vinhetas > 0 || $total_comerciais > 0) {
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

// Marca como ativado na playlist
mysql_query("Update playlists set vinhetas_comerciais = 'sim' where codigo = '".$dados_playlist["codigo"]."'");

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
<form method="post" action="/gerenciar-playlists-vinhetas-comerciais/<?php echo code_decode($dados_playlist["codigo"],"E"); ?>" style="padding:0px; margin:0px" name="hora-certa">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px; margin-bottom:10px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><span class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_info']; ?></span></td>
      </tr>
    </table>
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
<?php if(query_string('1') == "") { ?>
      <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_playlist']; ?></td>
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
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_vinhetas']; ?></td>
        <td align="left" class="texto_padrao">
          <select name="pasta_vinhetas" class="input" id="pasta_vinhetas" style="width:410px;">
          <option value="" selected="selected"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_opcao_selecionar_vinhetas']; ?></option>
          <option value=""><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_opcao_nao_configurar']; ?></option>
          <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_opcao_pastas']; ?>">
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
          </select>
          </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_executar']; ?>&nbsp;
          <input name="frequencia_vinhetas" type="text" class="input" id="frequencia_vinhetas" style="width:50px;" value="0" />
        &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_a_cada']; ?>&nbsp;
        <input name="frequencia_vinhetas2" type="text" class="input" id="frequencia_vinhetas2" style="width:50px;" value="10" />
        <select name="frequencia_vinhetas_tipo" class="input" id="frequencia_vinhetas_tipo" style="width:193px;">
              <option value="musicas"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_frequencia_musica']; ?></option>
              <option value="minutos"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_frequencia_minuto']; ?></option>
            </select>
            <img src="/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_frequencia_info']; ?>');" style="cursor:pointer" /></td>
        </tr>
        <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_comerciais']; ?></td>
        <td align="left" class="texto_padrao">
          <select name="pasta_comerciais" class="input" id="pasta_comerciais" style="width:410px;">
          <option value="" selected="selected"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_opcao_selecionar_comerciais']; ?></option>
          <option value=""><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_opcao_nao_configurar']; ?></option>
          <optgroup label="<?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_opcao_pastas']; ?>">
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
          </select>
          </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">&nbsp;</td>
        <td align="left" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_executar']; ?>&nbsp;
          <input name="frequencia_comerciais" type="text" class="input" id="frequencia_comerciais" style="width:50px;" value="0" />
        &nbsp;<?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_a_cada']; ?>&nbsp;
        <input name="frequencia_comerciais2" type="text" class="input" id="frequencia_comerciais2" style="width:50px;" value="10" />
        <select name="frequencia_comerciais_tipo" class="input" id="frequencia_comerciais_tipo" style="width:193px;">
            <option value="musicas"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_frequencia_musica']; ?></option>
            <option value="minutos"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_frequencia_minuto']; ?></option>
          </select>
        <img src="/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('<?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_frequencia_info']; ?>');" style="cursor:pointer" /></td>
      </tr>
      
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_configurar']; ?>" />&nbsp;<input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_voltar']; ?>" onclick="window.location = '/playlists';" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />          </td>
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
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_playlists_vinhetas_comerciais_instrucoes']; ?></td>
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
