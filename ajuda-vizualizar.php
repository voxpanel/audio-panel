<?php
require_once("admin/inc/protecao-final.php");

$tutorial_code = code_decode(query_string('1'),"D");

$dados_tutorial = mysql_fetch_array(mysql_query("SELECT * FROM tutoriais where codigo = '".$tutorial_code."'"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$tutorial = str_replace("[SERVIDOR]",dominio_servidor($dados_servidor["nome"]),$dados_tutorial["tutorial"]);
$tutorial = str_replace("[SERVIDORAACPLUS]",dominio_servidor($dados_servidor_aacplus["nome"]),$tutorial);
$tutorial = str_replace("[PORTA]",$dados_stm["porta"],$tutorial);
$tutorial = str_replace("[PORTADJ]",$dados_stm["porta_dj"],$tutorial);
$tutorial = str_replace("[PAINEL]",$_SERVER['HTTP_HOST'],$tutorial);

mysql_query("UPDATE tutoriais SET vizualizacoes = vizualizacoes+1 WHERE codigo = '".$dados_tutorial["codigo"]."'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $dados_tutorial["titulo"]; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
	<?php echo $tutorial; ?>
    <br />
	<br />
	<br />
	<strong><?php echo $lang['lang_info_streaming_ajuda_mais_vizualizados']; ?></strong>
    <br />
	<br />
<?php
$sql = mysql_query("SELECT * FROM tutoriais WHERE codigo != '".$dados_tutorial["codigo"]."' ORDER by vizualizacoes DESC LIMIT 5");
while ($dados_tutorial = mysql_fetch_array($sql)) {

echo "<img src='/img/icones/img-icone-ajuda-64x64.png' width='16' height='16' align='absmiddle' />&nbsp;&nbsp;<a href='/ajuda-vizualizar/".code_decode($dados_tutorial["codigo"],"E")."' title='".$lang['lang_info_streaming_ajuda_vizualizar']."'>".$dados_tutorial["titulo"]."</a> - ".$lang['lang_info_streaming_ajuda_vizualizacoes'].": ".$dados_tutorial["vizualizacoes"]."<br />";

}
?>
    </td>
  </tr>
</table>
    </div>
      </div>
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