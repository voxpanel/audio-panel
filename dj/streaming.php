<?php
require_once("inc/protecao-final-dj.php");

list($codigo_stm, $dj_login, $dj_senha) = explode("|",$_SESSION["dj_logado"]);

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$codigo_stm."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));

if($dados_stm["status"] == 1 && $dados_servidor["status"] == "on") {
$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
}

$cor_status = ($dados_stm["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$porta_code = code_decode($dados_stm["porta"],"E");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Painel do DJ - <?php echo $dados_stm["porta"]; ?> - <?php echo $dj_login; ?></title>
<meta http-equiv="cache-control" content="no-cache">
<link href="inc/estilo-dj.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-dj.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript">
   window.onload = function() {
   status_streaming('<?php echo $porta_code; ?>');
   musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','35');
   estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','sim');
   estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','sim');
   setInterval("musica_atual( <?php echo $dados_stm["porta"]; ?>,'musica_atual','35')",180000);
   setInterval("status_streaming('<?php echo $porta_code; ?>')",60000);
   setInterval("estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','sim')",30000);
   };
</script>
</head>

<body style="background-color:#000000;">
<div id="conteudo">
<?php if($dados_servidor["status"] == "on") { ?>
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#000000; margin-top:5px;">
  <tr>
    <td align="center">
    <div id="quadro">
       	  <div id="quadro-topo"><strong><?php echo $lang['lang_info_pagina_informacoes_tab_streaming']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
					  <table width="290" border="0" cellpadding="0" cellspacing="0">
   						<tr>
						  <td width="95" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_porta']; ?></td>
						  <td width="195" align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo $dados_stm["porta"]; ?></td>
   						</tr>
   						<tr>
						  <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_porta_dj']; ?></td>
      							<td align="left" class="texto_padrao"><?php echo $dados_stm["porta_dj"]; ?></td>
   						</tr>
   						<tr>
						  <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ip_conexao']; ?></td>
						  <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><?php echo dominio_servidor($dados_servidor["nome"]); ?></td>
   						</tr>
                            <tr>
      							<td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_shoutcast']; ?></td>
      							<td align="left" class="texto_padrao"><a href="http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>" target="_blank">http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?></a></td>
    						</tr>
                            <?php if($dados_stm["aacplus"] == 'sim') { ?>
                            <tr>
      							<td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_rtmp']; ?></td>
      							<td align="left" class="texto_padrao">rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?></td>
    						</tr>
                            <?php } ?>
					  </table>
		  </div>
      </div>    </td>
  </tr>
  <tr>
    <td align="center" height="5"></td>
  </tr>
  <tr>
    <td align="center">
    <div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $lang['lang_info_pagina_informacoes_tab_plano_uso']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
					  <table width="290" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="95" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;Status</td>
   				<td width="195" height="25" align="left" bgcolor="#F8F8F8" scope="col" class="texto_padrao"><span id="<?php echo $porta_code; ?>" style="cursor:pointer" onclick="status_streaming('<?php echo $porta_code; ?>')"></span></td>
   						</tr>
    						<tr>
                              <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_ouvintes']; ?></td>
    						  <td align="left" class="texto_padrao"><span id="estatistica_uso_plano_ouvintes" style="cursor:pointer" onclick="estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ouvintes','sim','nao');"></span></td>
  						  </tr>
    						<tr>
                              <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_espaco_ftp']; ?></td>
                              <td align="left" bgcolor="#F8F8F8" class="texto_padrao"><span id="estatistica_uso_plano_ftp" style="cursor:pointer" onclick="estatistica_uso_plano( <?php echo $dados_stm["porta"]; ?>,'ftp','nao');"></span></td>
  						  </tr>
                          <tr>
                              <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<?php echo $lang['lang_info_bitrate']; ?></td>
    						  <td align="left" class="texto_padrao"><?php echo $dados_stm["bitrate"]; ?>&nbsp;<span class="texto_padrao_pequeno">Kbps</span></td>
  						  </tr>
                          <tr>
    						  <td height="25" align="left" class="texto_padrao_destaque">&nbsp;<strong><?php echo $lang['lang_info_musica_atual']; ?></strong></td>
    						  <td align="left" class="texto_padrao"><span class="texto_padrao_pequeno" id="musica_atual"></span></td>
  						  </tr>
					  </table>
		  </div>
      </div>
      </td>
  </tr>
  <tr>
    <td align="center" height="5"></td>
  </tr>
  <tr>
    <td align="center">
    <div id="quadro">
            	<div id="quadro-topo"><strong><?php echo $lang['lang_info_pagina_informacoes_tab_gerenciamento_streaming']; ?></strong></div>
   		  <div class="texto_medio" id="quadro-conteudo">
   		    <?php if($dados_stm["status"] == '1') { ?>
					<select style="width:100%" id="<?php echo $porta_code; ?>" onchange="executar_acao_streaming_dj(this.id,this.value);">
  					<option value="" selected="selected"><?php echo $lang['lang_info_escolha_acao']; ?></option>
					<optgroup label="<?php echo $lang['lang_acao_label_streaming']; ?>">
		  			<option value="ligar"><?php echo $lang['lang_botao_titulo_ligar']; ?></option>
		  			<option value="desligar"><?php echo $lang['lang_botao_titulo_desligar']; ?></option>
          			<option value="kick"><?php echo $lang['lang_acao_stm_kick']; ?></option>
                    <option value="ouvintes-conectados"><?php echo $lang['lang_acao_ouvintes_ouvintes_conectados']; ?></option>
					</optgroup>
        			<optgroup label="<?php echo $lang['lang_acao_label_ouvintes']; ?>">
                    <option value="ouvintes-conectados"><?php echo $lang['lang_acao_ouvintes_ouvintes_conectados']; ?></option>
                    <option value="pedidos-musicais"><?php echo $lang['lang_acao_ouvintes_pedidos_musicais']; ?></option>
                    </optgroup>
        			<optgroup label="<?php echo $lang['lang_acao_label_autodj']; ?>">
          			<option value="ligar-autodj"><?php echo $lang['lang_botao_titulo_ligar']; ?></option>
          			<option value="pular-musica"><?php echo $lang['lang_acao_autodj_pular_musica']; ?></option>
		  			<option value="recarregar-playlist"><?php echo $lang['lang_acao_autodj_recarregar_playlist']; ?></option>
                    <option value="desligar-autodj"><?php echo $lang['lang_botao_titulo_desligar']; ?></option>
					</optgroup>
					</select>
				<?php } else { ?>
					<span class="texto_status_erro">&nbsp;<?php echo $lang['lang_alerta_bloqueio']; ?></span>
				<?php } ?>
   		  </div>
      </div>
      </td>
  </tr>
</table>
  <?php } else { ?>
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:15%; background-color:#FFFF66; border:#DFDF00 4px dashed">
  <tr>
        <td height="30" align="left" class="texto_status_streaming_offline" style="padding:3px;" scope="col">
		<center><img src="../img/icones/img-icone-manutencao-128x128.png" width="64" height="64" /></center>
        <br />
		<?php echo $dados_servidor["mensagem_manutencao"];?>
        </td>
    </tr>
    </table>
  <?php } ?>
</div>
<br />
<br />
<br />
<br />
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="Fechar" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
