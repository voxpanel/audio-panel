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
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<div id="quadro_requisicao" style="display:none">
  <div id="quadro">
            <div id="quadro-topo"><strong><?php echo $lang['lang_info_utilidade_download_soundcloud_tab_resultado']; ?></strong></div>
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
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_utilidade_download_soundcloud_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; background-color: #C1E0FF; border: #006699 1px solid">
      <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/img/icones/ajuda.gif" width="16" height="16" /></td>
        <td width="660" align="left" class="texto_padrao" scope="col"><?php echo $lang['lang_info_utilidade_download_soundcloud_info']; ?></td>
      </tr>
    </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_utilidade_download_soundcloud_aba_geral']; ?></h2>
        <form id="download_soundcloud" name="download_soundcloud">
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
      <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilidade_download_soundcloud_url_musica']; ?></td>
        <td width="560" align="left" class="texto_padrao"><input name="url" type="text" class="input" id="url" style="width:550px;" value="https://soundcloud.com/...." onclick="this.value=''" /></td>
      </tr>
      <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_utilidade_download_soundcloud_pasta']; ?></td>
        <td width="560" align="left" class="texto_padrao">
        <select name="pasta" class="input" id="pasta" style="width:350px;">
          <optgroup label="<?php echo $lang['lang_info_utilidade_download_soundcloud_pasta_opcao_pastas']; ?>">
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
    </table>
        </form>
        </div>
   	  </div></td>
  </tr>
  <tr>
    <td height="40" align="center"><input type="button" class="botao" value="<?php echo $lang['lang_info_utilidade_download_soundcloud_botao_download']; ?>" onclick="soundcloud_downloader( '<?php echo $dados_servidor["ip"]; ?>', '<?php echo $dados_stm["porta"]; ?>');" />
      <input name="download" type="hidden" id="download" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
    </div>
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
