<?php
set_time_limit(0);
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="/inc/ajax-streaming.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
   function limitTextarea(textarea, maxLines) {
        var lines = textarea.value.replace(/\r/g, '').split('\n'), lines_removed, i;
        if (maxLines && lines.length > maxLines) {
            lines = lines.slice(0, maxLines);
            lines_removed = 1
        }

            if (lines_removed) {
                textarea.value = lines.join('\n')
            }
    }
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
<div id="quadro_requisicao" style="display:none">
  <div id="quadro">
            <div id="quadro-topo"><strong><?php echo $lang['lang_info_utilitario_youtube_tab_resultado']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td align="center" class="texto_padrao"><img src="/img/ajax-loader.gif" width="220" height="19" id="img_loader" /><br />
                  <div id="resultado_requisicao" style="width:98%; height:150px; border:#999999 1px solid; text-align:left; overflow-y:scroll; padding:5px; background-color:#F4F4F7" class="texto_padrao"></div></td>
                </tr>
              </table>
          </div>
        </div>
<br />
</div>
<form action="/utilitario-youtube-processa" method="post" name="youtube">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_utilitario_youtube_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_utilitario_youtube_aba_geral']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
        <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_youtube_pasta']; ?></td>
        <td width="560" align="left" class="texto_padrao">
        <select name="pasta" class="input" id="pasta" style="width:400px;">
          <optgroup label="<?php echo $lang['lang_info_utilitario_youtube_pasta_opcao_pastas']; ?>">
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
        <td width="130" height="120" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilitario_youtube_url_video']; ?></td>
        <td width="560" align="left" class="texto_padrao">
          <textarea name="videos" id="videos" rows="7" style="width:393px; color:#C1C1C1" onclick="this.value='';this.style.color = '#000000'" onkeyup="limitTextarea(this,5)" onkeydown="limitTextarea(this,5)" onchange="limitTextarea(this,5)">https://www.youtube.com/watch?v=ZgXb-JdQkEw
https://www.youtube.com/watch?v=ZgXb-JdQkEw
https://www.youtube.com/watch?v=ZgXb-JdQkEw
https://www.youtube.com/watch?v=ZgXb-JdQkEw
https://www.youtube.com/watch?v=ZgXb-JdQkEw
</textarea>
        </td>
      </tr>
    </table>
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; margin-bottom:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
  	  		<tr>
        		<td width="30" height="30" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        		<td width="860" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_utilitario_youtube_info_limite']; ?></td>
     		</tr>
    	</table>
    </div>
   	  </div></td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="submit" class="botao" value="<?php echo $lang['lang_info_utilitario_youtube_botao_download']; ?>" />
      <input name="download" type="hidden" id="download" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
    </div>
</form>
</div>
<br />
<br />
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo">
<div class="meter">
	<span style="width: 100%"></span>
</div>
</div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
