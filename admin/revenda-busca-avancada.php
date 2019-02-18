<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastrar Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <th scope="col"><div id="quadro">
            	<div id="quadro-topo"><strong><?php echo lang_info_pagina_busca_avancada_tab_titulo; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
  <form method="post" action="/admin/revenda-busca-avancada-resultado" style="padding:0px; margin:0px">
    <table width="590" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_busca_avancada_palavra_chave; ?></td>
        <td width="390" align="left"><input name="chave" type="text" class="input" id="chave" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_busca_avancada_local; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="local" class="input" id="local" style="width:255px;">
        <optgroup label="<?php echo lang_info_pagina_busca_avancada_local_grupo_streaming; ?>">
        <option value="porta"><?php echo lang_info_pagina_busca_avancada_local_porta; ?></option>
        <option value="porta_dj"><?php echo lang_info_pagina_busca_avancada_local_porta_dj; ?></option>
        <option value="ouvintes"><?php echo lang_info_pagina_busca_avancada_local_ouvintes; ?></option>
        <option value="bitrate"><?php echo lang_info_pagina_busca_avancada_local_bitrate; ?></option>
        <option value="encoder"><?php echo lang_info_pagina_busca_avancada_local_encoder; ?></option>
        <option value="identificacao"><?php echo lang_info_pagina_busca_avancada_local_identificacao; ?></option>
        <option value="email"><?php echo lang_info_pagina_busca_avancada_local_email; ?></option>
        <option value="data_cadastro"><?php echo lang_info_pagina_busca_avancada_local_data_cadastro; ?></option>
        <option value="servidor_ip"><?php echo lang_info_pagina_busca_avancada_local_ip; ?></option>
        <option value="servidor_nome"><?php echo lang_info_pagina_busca_avancada_local_nome; ?></option>
        </optgroup>
        <optgroup label="<?php echo lang_info_pagina_busca_avancada_local_grupo_subrevenda; ?>">
        <option value="id" disabled="disabled"><?php echo lang_info_pagina_busca_avancada_local_id; ?></option>
        <option value="email_subrevenda" disabled="disabled"><?php echo lang_info_pagina_busca_avancada_local_email; ?></option>
        </optgroup>
		</select>
        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo lang_info_pagina_enviar_email_botao_buscar; ?>" /></td>
      </tr>
    </table>
  </form>
  </div>
  </div></th>
    </tr>
  </table>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</div>
</body>
</html>
