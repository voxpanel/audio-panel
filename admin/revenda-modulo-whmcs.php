<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

$versao_modulo_whmcs_streaming = "2.4 (09-10-2017)";
$versao_modulo_whmcs_subrevenda = "2.1 (09-10-2017)";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo lang_info_pagina_modulo_whmcs_tab_titulo_streaming; ?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo sprintf(lang_info_pagina_modulo_whmcs_streaming_texto1,$versao_modulo_whmcs_streaming); ?><br />
      <br />
      <input type="text" value="<?php echo $dados_revenda["chave_api"]; ?>" style="width:99%; height:30px;"  onclick="this.select()" readonly="readonly" /><br />
<br /> 
<?php echo sprintf(lang_info_pagina_modulo_whmcs_streaming_texto2,$versao_modulo_whmcs_streaming); ?><br /><br />
<img src="img/img-tutorial-modulo-whmcs1.jpg" alt="M&oacute;dulo WHMCS" width="369" height="327" /><br />
<br />
<?php echo sprintf(lang_info_pagina_modulo_whmcs_streaming_texto3,$dados_revenda["chave_api"]); ?>
<br /><br />
<img src="img/img-tutorial-modulo-whmcs2.jpg" alt="M&oacute;dulo WHMCS" width="508" height="190" /><br /><br />
<?php echo lang_info_pagina_modulo_whmcs_streaming_texto4; ?>
<br />
<br />
<br /></td>
    </tr>
</table>
    </div>
      </div>
<br />
<?php if($dados_revenda["subrevendas"] > 0) { ?>
<div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo lang_info_pagina_modulo_whmcs_tab_titulo_subrevenda; ?>&nbsp;<span class="label label-amarelo"><?php echo lang_label_atualizado; ?></span></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo sprintf(lang_info_pagina_modulo_whmcs_subrevenda_texto1,$versao_modulo_whmcs_subrevenda); ?><br />
      <br />
      <input type="text" value="<?php echo $dados_revenda["chave_api"]; ?>" style="width:99%; height:30px;"  onclick="this.select()" readonly="readonly" /><br />
<br /> 
<?php echo sprintf(lang_info_pagina_modulo_whmcs_subrevenda_texto2,$dados_revenda["chave_api"]); ?><br /><br />
<?php echo lang_info_pagina_modulo_whmcs_subrevenda_texto3; ?>
</td>
    </tr>
</table>
    </div>
      </div>
<?php } ?>
<br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
