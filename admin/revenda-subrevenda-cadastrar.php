<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

// Estatísticas
$total_streamings_ativos = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."' AND status = '1'"));
$total_streamings_bloqueados = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."' AND status != '1'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_streamings = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

$total_ouvintes = $ouvintes["total"]+$ouvintes_subrevendas["total"];

$porcentagem_uso_subrevendas = ($dados_revenda["subrevendas"] == 0) ? "0" : $total_subrevendas*100/$dados_revenda["subrevendas"];
$porcentagem_uso_streamings = ($dados_revenda["streamings"] == 0) ? "0" : $total_streamings*100/$dados_revenda["streamings"];
$porcentagem_uso_ouvintes = ($dados_revenda["ouvintes"] == 0) ? "0" : $total_ouvintes*100/$dados_revenda["ouvintes"];
$porcentagem_uso_espaco = ($dados_revenda["espaco"] == 0) ? "0" : ($espaco_subrevendas["total"]+$espaco_streamings["total"])*100/$dados_revenda["espaco"];

$stat_subrevendas_descricao = "".$total_subrevendas." / ".$dados_revenda["subrevendas"]."";
$stat_streamings_descricao = "".$total_streamings." / ".$dados_revenda["streamings"]."";
$stat_ouvintes_descricao = "".$total_ouvintes." / ".$dados_revenda["ouvintes"]."";
$stat_espaco_descricao = "".tamanho(($espaco_subrevendas["total"]+$espaco_streamings["total"]))." / ".tamanho($dados_revenda["espaco"])."";

$limite_subrevendas = ($dados_revenda["subrevendas"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_subrevendas,$stat_subrevendas_descricao);

$limite_streamings = ($dados_revenda["streamings"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_streamings,$stat_streamings_descricao);

$limite_ouvintes = ($dados_revenda["ouvintes"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_ouvintes,$stat_ouvintes_descricao);

if($dados_revenda["subrevendas"] < 0 && $dados_revenda["tipo"] == 3) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["aviso_acesso_negado"] = status_acao(lang_info_acesso_stm_nao_permitido,"erro");

header("Location: /admin/revenda-informacoes");
exit;

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastrar Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
<script type="text/javascript">
function configurar_ilimitado(campo,recurso) {

if(document.getElementById(campo).checked) {
document.getElementById(recurso).value = '999999';
} else {
document.getElementById(recurso).value = '';
}

}
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="650" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<?php if($dados_revenda["subrevendas"] > 0) { ?>
<table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <th scope="col"><div id="quadro">
            	<div id="quadro-topo"><strong><?php echo lang_info_pagina_cadastrar_subrevenda_tab_titulo; ?></strong></div>
                <div class="texto_medio" id="quadro-conteudo">
  <form method="post" action="/admin/revenda-subrevenda-cadastra" style="padding:0px; margin:0px">
    <table width="640" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_email; ?></td>
        <td width="440" align="left" class="texto_padrao_pequeno"><input name="subrevenda_email" type="text" class="input" id="subrevenda_email" style="width:250px;" /></td>
      </tr>
	  <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_senha; ?></td>
        <td align="left"><input name="subrevenda_senha" type="text" class="input" id="subrevenda_senha" style="width:250px;" />&nbsp;<img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Senha/Password" width="16" height="16" align="absmiddle" onclick="gerar_senha('subrevenda_senha');" style="cursor:pointer" /></td>
      </tr>
      <?php if($dados_revenda["subrevendas"] > 0 && ($dados_revenda["tipo"] == 1 || $dados_revenda["tipo"] == 2)) { ?>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_subrevendas; ?></td>
        <td align="left"><input name="subrevendas" type="text" class="input" id="subrevendas" style="width:250px;" value="" /> </td>
      </tr>
      <?php } ?>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_stm; ?></td>
        <td align="left"><input name="streamings" type="text" class="input" id="streamings" style="width:250px;" value="" />&nbsp;
        <?php if($dados_revenda["streamings"] == '999999') { ?>
        <input type="checkbox" id="streamings_ilimitados" onclick="configurar_ilimitado('streamings_ilimitados','streamings');" style="vertical-align:middle" />&nbsp;<span class="texto_ilimitado"><?php echo lang_info_pagina_cadastrar_subrevenda_ilimitado; ?></span>
        <?php } ?>        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_ouvintes; ?></td>
        <td align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" value="" />&nbsp;
        <?php if($dados_revenda["ouvintes"] == '999999') { ?>
        <input type="checkbox" id="ouvintes_ilimitados" onclick="configurar_ilimitado('ouvintes_ilimitados','ouvintes');" style="vertical-align:middle" />&nbsp;<span class="texto_ilimitado"><?php echo lang_info_pagina_cadastrar_subrevenda_ilimitado; ?></span>
        <?php } ?>        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_bitrate; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="bitrate" class="input" id="bitrate" style="width:255px;">
  <?php
		   foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
		   
		   if($bitrate <= $dados_revenda["bitrate"]) {
		   
		   if($bitrate == $dados_revenda["bitrate"]) {
		   echo "<option value=\"".$bitrate."\">".$bitrate." Kbps(máximo)</option>\n";
		   } else {
		   echo "<option value=\"".$bitrate."\">".$bitrate." Kbps</option>\n";
		   }

		   }
		    
		   }
		  ?>
</select>
	</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_limite_espaco_ftp; ?></td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="text" class="input" id="espaco" style="width:250px;" />&nbsp;Megabytes</td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_idioma; ?></td>
        <td align="left">
        <select name="idioma_painel" id="idioma_painel" style="width:255px;">
          <option value="pt-br" selected="selected">Português(Brasil)</option>
          <option value="es">Español</option>
          <option value="en-us">English(USA)</option>
        </select>
        </td>
      </tr>
      <?php if($dados_revenda["aacplus"] == 'sim') { ?>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_subrevenda_aacplus; ?></td>
        <td align="left" class="texto_padrao">
          <input name="aacplus" type="radio" id="aacplus" value="sim" checked="checked" />&nbsp;<?php echo lang_opcao_sim; ?>
          <input type="radio" name="aacplus" id="aacplus" value="nao" />&nbsp;<?php echo lang_opcao_nao; ?>
          </td>
      </tr>
      <?php } else { ?>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_subrevenda_aacplus; ?></td>
        <td align="left" class="texto_log_sistema_erro"><?php echo lang_info_pagina_cadastrar_subrevenda_aacplus_info_desativado; ?>
          <input name="aacplus" id="aacplus" type="hidden" value="nao" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo lang_botao_titulo_cadastrar; ?>" /></td>
      </tr>
    </table>
<table width="340" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid; margin-top:10px; margin-bottom:5px;">
          <tr>
            <td height="30" align="center" class="texto_padrao_destaque"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_tab_titulo; ?></td>
          </tr>
    </table>
        <table width="340" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
        <tr>
          <td width="100" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_subrevendas; ?></td>
          <td width="240" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $limite_subrevendas; ?></td>
        </tr>
        <tr>
          <td width="100" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_stm; ?></td>
          <td width="240" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $limite_streamings; ?></td>
        </tr>
        <tr>
          <td height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_ouvintes; ?></td>
          <td align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $limite_ouvintes; ?></td>
        </tr>
        <tr>
          <td height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_cadastrar_subrevenda_estatisticas_espaco_ftp; ?></td>
          <td align="left" class="texto_padrao" style="padding-left:5px;"><?php echo barra_uso_plano($porcentagem_uso_espaco,$stat_espaco_descricao); ?></td>
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
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_info_pagina_cadastrar_subrevenda_aviso_subrevenda_desativado; ?></td>
    </tr>
</table>
<?php } ?>
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
