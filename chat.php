<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

$porta_code = code_decode($dados_stm["porta"],"E");

$url_chat = (!empty($dados_revenda["dominio_padrao"])) ? "player.".$dados_revenda["dominio_padrao"]."" : "player.".$dados_config["dominio_padrao"]."";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/inc/chat.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_streaming_chat_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_chat_tab_titulo']; ?></h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid; border-bottom:#D5D5D5 1px solid;">
  <tr>
    <td class="texto_padrao"><iframe style="margin-top:5px" src="<?php echo "http://".$url_chat."/chat/".$dados_stm["porta"].""; ?>" frameborder="0" width="100%" height="500"></iframe></td>
    </tr>
</table>
  </div>
  <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_streaming_chat_widget_tab_titulo']; ?></h2>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">
 			 <tr>
   				 <td align="left" class="texto_padrao" scope="col" style="padding-left:5px"><br />
                 <?php echo $lang['lang_info_streaming_chat_widget_info']; ?><br /><br />
                 <span class="texto_padrao_destaque">URL:</span><br />
<input type="text" value="<?php echo "http://".$url_chat."/chat/".$dados_stm["porta"].""; ?>" style="width:99%; height:30px"  onclick="this.select()" readonly="readonly" /><br />
<br />
<span class="texto_padrao_destaque">Widget:</span><br />
<textarea readonly="readonly" style="width:99%; height:300px"  onclick="this.select()">
<script type="text/javascript">
function abrir_chat( url,largura,altura ) {
window.open( url, "","width="+largura+",height="+altura+",toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO" );
}
</script>

<a href="javascript:abrir_chat( '<?php echo "http://".$url_chat."/chat/".$dados_stm["porta"].""; ?>','750','465' );">Chat</a>
</textarea>
                 </td>
  			</tr>
		</table>
  </div>
    </div>
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