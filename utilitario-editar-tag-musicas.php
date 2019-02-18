<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

if(isset($_POST["executar"])) {

$resultado = file_get_contents("http://".$dados_servidor["ip"].":555/editar-tag-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$_POST["pasta"]."&titulo=".$_POST["titulo"]."&artista=".$_POST["artista"]."");

if(!preg_match('/FF0000/i',$resultado)) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".$lang['lang_info_utilidade_editar_tag_musicas_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_utilidade_editar_tag_musicas_resultado_erro']."","alerta");

}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form method="post" action="/utilitario-editar-tag-musicas" style="padding:0px; margin:0px" name="ferramenta-editar-tag-musicas">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_utilidade_editar_tag_musicas_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><span class="texto_padrao"><?php echo $lang['lang_info_utilidade_editar_tag_musicas_info']; ?></span></td>
      </tr>
    </table>
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
	  <tr>
        <td width="180" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilidade_editar_tag_musicas_pasta']; ?></td>
        <td width="510" align="left" class="texto_padrao">
          <select name="pasta" class="input" id="pasta" style="width:250px;">
          <option value="" selected="selected"><?php echo $lang['lang_info_utilidade_editar_tag_musicas_pasta_opcao_selecionar_pasta']; ?></option>
          <optgroup label="<?php echo $lang['lang_info_utilidade_editar_tag_musicas_opcao_pastas']; ?>">
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
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilidade_editar_tag_musicas_titulo']; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="titulo" type="text" class="input" id="titulo" style="width:245px;" onkeyup="bloquear_acentos(this);" />&nbsp;<?php echo $lang['lang_info_utilidade_editar_tag_musicas_info_acentos']; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilidade_editar_tag_musicas_artista']; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="artista" type="text" class="input" id="artista" style="width:245px;" onkeyup="bloquear_acentos(this);" />&nbsp;<?php echo $lang['lang_info_utilidade_editar_tag_musicas_info_acentos']; ?></td>
      </tr>
      <tr>
        <td height="40"></td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_configurar']; ?>" />&nbsp;<input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_voltar']; ?>" onclick="window.location = '/playlists';" /><input name="executar" type="hidden" id="executar" value="sim" /></td>
      </tr>
    </table>
    </div>
    </div>
  </form>
<?php if(!empty($resultado)) { ?>
<br />
  <div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_utilidade_editar_tag_musicas_resultado_tab_titulo']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="25" class="texto_padrao_pequeno"><?php echo $resultado; ?></td>
                </tr>
              </table>
          </div>
        </div>
<?php } ?>
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