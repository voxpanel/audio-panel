<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 300);

require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");
require_once("admin/inc/classe.ssh.php");

//$porta = code_decode(query_string('1'),"D");
//$porta_code = query_string('1');
$porta_code = code_decode($_SESSION["porta_logada"],"E");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/ajax-streaming-musicas.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
    carregar_pastas('<?php echo $porta_code; ?>');
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
    <table width="890" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="310" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_musicas_pastas']; ?></td>
        <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;"><?php echo $lang['lang_info_gerenciador_musicas_musicas_pasta']; ?></td>
      </tr>
      <tr>
        <td align="left" style="padding-left:5px;">
        <div id="borda_lista_pastas" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:285px; height:250px; text-align:left; float:left; padding:5px; overflow: auto;">
        <span id="status_lista_pastas" class="texto_padrao_pequeno"></span>
		<ul id="lista-pastas">
		</ul>
		</div>
		</td>
        <td align="left">
        <div id="musicas" style="background-color:#FFFFFF; border: #CCCCCC 1px solid; width:560px; height:250px; text-align:left; float:right; padding:5px; overflow: auto;">
        <span id="msg_pasta" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_musicas_info_lista_musicas']; ?></span>
        <ul id="lista-musicas-pasta">
        </ul>
        </div>
        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;"><img src="/img/icones/img-icone-cadastrar.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript:criar_pasta('<?php echo $porta_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_musicas_botao_criar_pasta']; ?></a>&nbsp;&nbsp;<img src="/img/icones/img-icone-atualizar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:carregar_pastas('<?php echo $porta_code; ?>');" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_musicas_botao_recarregar_pastas']; ?></a>&nbsp;</td>
        <td width="580" align="left" class="texto_padrao_destaque" style="padding-left:9px;"><?php echo $lang['lang_info_gerenciador_musicas_pasta_selecionada']; ?>&nbsp;<span id="msg_pasta_selecionada" class="texto_padrao_vermelho"><?php echo $lang['lang_info_gerenciador_musicas_pasta_selecionada_nenhuma']; ?></span></td>
      </tr>
      <tr>
        <td align="center" valign="top">
        <div style="padding-top:20px;padding-left:80px;"><span id="estatistica_uso_plano_ftp"></span></div>
		<span class="texto_padrao_pequeno">(<?php echo tamanho($dados_stm["espaco_usado"]); ?> / <?php echo tamanho($dados_stm["espaco"]); ?>)</span>
        </td>
        <td align="left" style="padding-left:9px;">
        <img src="/img/icones/img-icone-enviar.png" width="16" height="16" align="absmiddle" border="0" />&nbsp;<a href="javascript:enviar_musicas(document.getElementById('pasta_selecionada').value);" class="texto_padrao"><?php echo $lang['lang_info_gerenciador_musicas_botao_abrir_janela_upload']; ?></a>
        <input name="pasta_selecionada" type="hidden" id="pasta_selecionada" value="" />
        </td>
      </tr>
    </table>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_musicas_info_ftp']; ?></td>
    </tr>
  </table>
  <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:20px; background-color:#FFFF66; border:#DFDF00 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="660" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_musicas_info_caracteres_especiais']; ?></td>
    </tr>
  </table>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';document.getElementById('log-sistema-conteudo').innerHTML = '';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
<script type="text/javascript">
// Checar o status dos streamings
estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao');
</script>
</body>
</html>