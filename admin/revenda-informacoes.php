<?php
require_once("inc/protecao-revenda.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

// Estatísticas de uso do plano
$total_streamings_ativos = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."' AND status = '1'"));
$total_streamings_bloqueados = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."' AND status != '1'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevendas = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_streamings = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

$porcentagem_uso_subrevendas = ($dados_revenda["subrevendas"] == 0) ? "0" : $total_subrevendas*100/$dados_revenda["subrevendas"];
$porcentagem_uso_streamings = ($dados_revenda["streamings"] == 0) ? "0" : $total_streamings*100/$dados_revenda["streamings"];
$porcentagem_uso_ouvintes = ($dados_revenda["ouvintes"] == 0) ? "0" : $ouvintes["total"]*100/$dados_revenda["ouvintes"];
$porcentagem_uso_espaco = ($dados_revenda["espaco"] == 0) ? "0" : ($espaco_subrevendas["total"]+$espaco_streamings["total"])*100/$dados_revenda["espaco"];

$stat_subrevendas_descricao = "".$total_subrevendas." / ".$dados_revenda["subrevendas"]."";
$stat_streamings_descricao = "".$total_streamings." / ".$dados_revenda["streamings"]."";
$stat_ouvintes_descricao = "".$ouvintes["total"]." / ".$dados_revenda["ouvintes"]."";
$stat_espaco_descricao = "".tamanho(($espaco_subrevendas["total"]+$espaco_streamings["total"]))." / ".tamanho($dados_revenda["espaco"])."";

$limite_subrevendas = ($dados_revenda["subrevendas"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_subrevendas,$stat_subrevendas_descricao);

$limite_streamings = ($dados_revenda["streamings"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_streamings,$stat_streamings_descricao);

$limite_ouvintes = ($dados_revenda["ouvintes"] == 999999) ? '<span class="texto_ilimitado">ILIMITADO</span>' : barra_uso_plano($porcentagem_uso_ouvintes,$stat_ouvintes_descricao);

if($dados_revenda["dominio_padrao"] != "") {

$servidor_player = "player.".$dados_config["dominio_padrao"];
$checagem = dns_get_record("player.".$dados_revenda["dominio_padrao"], DNS_CNAME);

$exibir_aviso_dns_player = ($checagem[0]["target"] == $servidor_player) ? 'nao' : 'sim';

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Informações da Revenda</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/ajax-revenda.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<?php
if($_SESSION['aviso_acesso_negado']) {

$aviso_acesso_negado = stripslashes($_SESSION['aviso_acesso_negado']);

echo '<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$aviso_acesso_negado.'</table>';

unset($_SESSION['aviso_acesso_negado']);
}
?>
<?php if($exibir_aviso_dns_player == "sim") { ?>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="50" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="850" align="left" class="texto_status_erro" scope="col">Você ainda não configurou o DNS para "player.<?php echo $dados_revenda["dominio_padrao"]; ?>" <a href="/admin/revenda-dominio">clique aqui!</a></td>
  </tr>
</table>
<?php } ?>
<table width="900" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom:10px">
  <tr>
    <td width="900" height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
      <div id="quadro-topo"><strong><?php echo lang_info_pagina_informacoes_revenda_tab_avisos; ?></strong></div>
      <div class="texto_medio" id="quadro-conteudo">
        <table width="875" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td height="25" class="texto_status_erro_pequeno"><?php
carregar_avisos_revenda();
?></td>
          </tr>
        </table>
      </div>
    </div></td>
  </tr>
</table>
<table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td width="300" height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_revenda_tab_plano; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="275" border="0" cellpadding="0" cellspacing="0">
            <?php if($dados_revenda["subrevendas"] > 0) { ?>
              <tr>
                <td width="95" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_revenda_tab_plano_subrevendas; ?></td>
                <td width="180" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $limite_subrevendas ?>&nbsp;</td>
              </tr>
              <?php } ?>
              <tr>
                <td width="80" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_revenda_tab_plano_streamings; ?></td>
                <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $limite_streamings ?>&nbsp;</td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_revenda_tab_plano_ouvintes; ?></td>
                <td align="left" class="texto_padrao"><?php echo $limite_ouvintes ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_revenda_tab_plano_ftp; ?></td>
                <td align="left" class="texto_padrao"><?php echo barra_uso_plano($porcentagem_uso_espaco,$stat_espaco_descricao); ?></td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_revenda_tab_plano_bitrate; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_revenda["bitrate"]; ?> Kbps</td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo lang_info_pagina_informacoes_revenda_tab_plano_stats; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><span class="texto_padrao_pequeno"><?php echo $total_streamings_ativos; ?> <?php echo lang_info_pagina_informacoes_revenda_tab_plano_stats_ativos; ?> / <?php echo $total_streamings_bloqueados; ?> <?php echo lang_info_pagina_informacoes_revenda_tab_plano_stats_bloqueados; ?></span></td>
              </tr>
              <?php if($dados_revenda["subrevendas"] == 0) { ?>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque">&nbsp;</td>
                <td align="left" class="texto_padrao">&nbsp;</td>
              </tr>
              <?php } ?>
             </table>
        </div>
      </div></td>
      <td width="270" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_revenda_tab_ferramentas; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
          <table width="260" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="130" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-dominio','conteudo');"><img src="img/icones/img-icone-globo.png" title="Use seu dom&iacute;nio no painel" width="48" height="48" /> <br />
                <?php echo lang_info_pagina_informacoes_revenda_tab_ferramentas_dominio; ?></td>
              <td width="130" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-app-android','conteudo');"><img src="img/icones/img-icone-app-android-64x64.png" title="Solicitar App Android Gr&aacute;tis" width="48" height="48" /> <br />
                  <?php echo lang_info_pagina_informacoes_revenda_tab_ferramentas_app_android; ?></td>
              </tr>
            <tr>
              <td width="130" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-configuracoes','conteudo');"><img src="img/icones/img-icone-idioma.png" title="<?php echo lang_info_pagina_informacoes_tab_menu_idioma; ?>" width="48" height="48" /> <br />
                  <?php echo lang_info_pagina_informacoes_revenda_tab_ferramentas_idioma; ?></td>
              <td width="130" align="center">&nbsp;</td>
              </tr>
          </table>
        </div>
      </div></td>
      <td width="140" align="center" valign="top" style="padding-left:5px; padding-right:5px"><div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_revenda_tab_integracao; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="137" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="130" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-api','conteudo');"><img src="img/icones/img-icone-api.png" title="API" width="48" height="48" /> <br />
                    <?php echo lang_info_pagina_informacoes_revenda_tab_integracao_api; ?></td>
              </tr>
              <tr>
                <td width="130" height="75" align="center" bgcolor="#FFA8A8" class="texto_padrao_destaque" style="cursor:pointer" onclick="abrir_log_sistema();window.open('/admin/revenda-modulo-whmcs','conteudo');"><img src="img/icones/img-icone-whmcs.jpg" title="M&oacute;dulo WHMCS" width="48" height="48" /> <br />
                    <?php echo lang_info_pagina_informacoes_revenda_tab_integracao_whmcs; ?>&nbsp;<span class="label label-amarelo"><?php echo lang_label_atualizado; ?></span></td>
              </tr>
            </table>
        </div>
      </div></td>
      <td width="140" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
          <div id="quadro-topo"> <strong><?php echo lang_info_pagina_informacoes_revenda_tab_acao_stm; ?></strong></div>
        <div class="texto_medio" id="quadro-conteudo">
            <table width="137" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="130" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="alert('<?php echo lang_alerta_processo_demorado; ?>');ligar_streamings_revenda();"><img src="img/icones/img-icone-play.png" title="Start All" width="48" height="48" /> <br />
                <?php echo lang_info_pagina_informacoes_revenda_tab_acao_stm_ligar_stms; ?></td>
              </tr>
              <tr>
                <td width="130" height="75" align="center" class="texto_padrao_destaque" style="cursor:pointer" onclick="alert('<?php echo lang_alerta_processo_demorado; ?>');ligar_autodjs_revenda();"><img src="img/icones/img-icone-play.png" title="Start All" width="48" height="48" /> <br />
                <?php echo lang_info_pagina_informacoes_revenda_tab_acao_stm_ligar_autodjs; ?></td>
              </tr>
            </table>
        </div>
      </div>
      </td>
    </tr>
  </table>
</div>
<?php if($dados_revenda["status"] != 1) { ?>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="50" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="850" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
  </tr>
</table>
<?php } ?>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo lang_titulo_fechar; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>