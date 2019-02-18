<?php
require_once("inc/protecao-revenda.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax-revenda.js"></script>
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
            	<div id="quadro-topo"> <strong><?php echo lang_info_pagina_dominio_proprio_tab_titulo; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo lang_info_pagina_dominio_proprio_info1; ?>
          <br />
          <br />
          <span class="texto_padrao_vermelho_destaque"><?php echo lang_info_pagina_dominio_proprio_info2; ?></span><br />
              <br />
            <span class="texto_padrao_destaque"><?php echo lang_info_pagina_dominio_proprio_info3; ?></span><br />
              <br />
            Status: <?php echo ($dados_revenda["dominio_padrao"]) ? '<span class="texto_padrao_verde_destaque">'.lang_info_pagina_dominio_proprio_status1.' - '.$dados_revenda["dominio_padrao"].'</span>' : '<span class="texto_padrao_vermelho_destaque">'.lang_info_pagina_dominio_proprio_status2.'</span> <a href="#" onclick="abrir_log_sistema();window.open(\'/admin/revenda-configuracoes\',\'conteudo\');">'.lang_info_pagina_dominio_proprio_status_botao.'</a>'; ?><br />
            <br />
          <span class="texto_padrao_vermelho_destaque"><?php echo lang_info_pagina_dominio_proprio_info4; ?></span><br />
              <br />
            <span class="texto_padrao_destaque"><?php echo lang_info_pagina_dominio_proprio_info5; ?></span><br />
              <br />
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="18%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_dominio_proprio_servidores_nome; ?></td>
      <td width="15%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_dominio_proprio_servidores_ttl; ?></td>
      <td width="15%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_dominio_proprio_servidores_tipo; ?></td>
      <td width="25%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_dominio_proprio_servidores_servidor; ?></td>
      <td width="27%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo lang_info_pagina_dominio_proprio_servidores_status; ?></td>
    </tr>
<?php

$servidor_api = "api.".$dados_config["dominio_padrao"];
$checagem_servidor_api = dns_get_record("api.".$dados_revenda["dominio_padrao"], DNS_CNAME);

if($checagem_servidor_api[0]["target"] == $servidor_api) {
$status_servidor_api = '<span class="texto_padrao_verde_destaque">'.lang_info_pagina_dominio_proprio_status1.'</span>';
} else {
$status_servidor_api = '<span class="texto_padrao_vermelho_destaque">'.lang_info_pagina_dominio_proprio_status3.'</span>';
}

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;api</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;900</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;CNAME</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;api.".$dados_config["dominio_padrao"].".</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$status_servidor_api."</td>
</tr>";

$servidor_player = "player.".$dados_config["dominio_padrao"];
$checagem_servidor_player = dns_get_record("player.".$dados_revenda["dominio_padrao"], DNS_CNAME);

if($checagem_servidor_player[0]["target"] == $servidor_player) {
$status_servidor_player = '<span class="texto_padrao_verde_destaque">'.lang_info_pagina_dominio_proprio_status1.'</span>';
} else {
$status_servidor_player = '<span class="texto_padrao_vermelho_destaque">'.lang_info_pagina_dominio_proprio_status3.'</span>';
}

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;player</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;900</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;CNAME</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;player.".$dados_config["dominio_padrao"].".</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$status_servidor_player."</td>
</tr>";

$sql = mysql_query("SELECT * FROM servidores WHERE status = 'on' ORDER by ordem ASC");
while ($dados_servidor = mysql_fetch_array($sql)) {

$servidor = strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];

$checagem = dns_get_record(strtolower($dados_servidor["nome"]).".".$dados_revenda["dominio_padrao"], DNS_CNAME);

if($checagem[0]["target"] == $servidor) {
$status = '<span class="texto_padrao_verde_destaque">'.lang_info_pagina_dominio_proprio_status1.'</span>';
} else {
$status = '<span class="texto_padrao_vermelho_destaque">'.lang_info_pagina_dominio_proprio_status3.'</span>';
}

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".strtolower($dados_servidor["nome"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;900</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;CNAME</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"].".</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$status."</td>
</tr>";

}
?>
  </table>
<br />
<span class="texto_padrao_vermelho_destaque"><?php echo lang_info_pagina_dominio_proprio_info6; ?></span><br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_dominio_proprio_info7; ?></span>&nbsp;<span class="label label-amarelo"><?php echo lang_label_atualizado; ?></span><br />
<br />
<?php echo lang_info_pagina_dominio_proprio_info8; ?>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_dominio_proprio_info9; ?></span><br />
<br />
<?php echo lang_info_pagina_dominio_proprio_info10; ?>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_dominio_proprio_info11; ?></span><br />
<br />
<?php echo lang_info_pagina_dominio_proprio_info12; ?>
<br />
<br />
<img src="img/img-tutorial-dns-servidores1.JPG" alt="Adicionar Entrada de DNS" width="400" height="309" border="1" /><br />
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
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>