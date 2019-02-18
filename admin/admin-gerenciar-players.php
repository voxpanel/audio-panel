<?php
require_once("inc/protecao-admin.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$host_sources = ($dados_config["usar_cdn"] == "sim") ? $dados_config["dominio_cdn"] : $_SERVER['HTTP_HOST'];

$cor_player_topo = ($_POST["cor"]) ? $_POST["cor"] : '000000';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciar Players</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
</div>
<div id="menu">
<div id="menu-links">
  	<ul>
      <li style="width:150px">&nbsp;</li>
  		<li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
  		<li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
        <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
        <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
        <li><em></em><a href="/admin/admin-avisos" class="texto_menu">Avisos</a></li>
        <li><em></em><a href="/admin/admin-tutoriais" class="texto_menu">Tutoriais</a></li>
        <li><em></em><a href="/admin/admin-apps" class="texto_menu">Apps</a></li>
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configurações</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
<table width="885" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td width="885" height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Flash Player</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <?php if($dados_stm["aacplus"] == 'sim') { ?>
    <embed src="http://<?php echo $host_sources; ?>/player-aacplus.swf" width="280" height="20" allowscriptaccess="always" allowfullscreen="true" flashvars="file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=false" type="application/x-shockwave-flash" /></embed><br /><br />
    <textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><embed src="http://<?php echo $host_sources; ?>/player-aacplus.swf" width="280" height="20" allowscriptaccess="always" allowfullscreen="true" flashvars="file=rtmp://<?php echo dominio_servidor($dados_servidor_aacplus["nome"]); ?>/<?php echo $dados_stm["porta"]; ?>&id=<?php echo $dados_stm["porta"]; ?>.stream&autostart=true" type="application/x-shockwave-flash" /></embed></textarea>
    <?php } else { ?>
	<embed height="17" width="260" flashvars="file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&volume=100&bufferlength=0" allowscriptaccess="always" quality="high" src="http://<?php echo $host_sources; ?>/player.swf" type="application/x-shockwave-flash"></embed><br /><br />
    <textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><embed height="17" width="260" flashvars="file=http://<?php echo dominio_servidor($dados_servidor["nome"]); ?>:<?php echo $dados_stm["porta"]; ?>/;type=mp3&volume=100&bufferlength=0&autostart=true" allowscriptaccess="always" quality="high" src="http://<?php echo $host_sources; ?>/player.swf" type="application/x-shockwave-flash"></embed></textarea>    
    <?php } ?>    </td>
    </tr>
</table>
    </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Flash Player Topo</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
<iframe src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player-barra/<?php echo $dados_stm["porta"]; ?>/<?php echo $cor_player_topo; ?>" frameborder="0" width="100%" height="31"></iframe><br /><br />
    <textarea name="textarea" readonly="readonly" style="width:99%; height:30px;font-size:11px" onmouseover="this.select()"><iframe src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player-barra/<?php echo $dados_stm["porta"]; ?>/<?php echo $cor_player_topo; ?>" frameborder="0" width="100%" height="31"></iframe></textarea>
    <br />
    <br />
    <form action="/admin/admin-gerenciar-players/<?php echo query_string('2'); ?>" name="cor_player_topo" method="post">
    <select name="cor" onchange="document.cor_player_topo.submit();">
    <option value="000000">Cor</option>
    <option value="000000" style="background:#000000; color:#FFFFFF">Preto</option>
    <option value="FF0000" style="background:#FF0000; color:#FFFFFF">Vermelho</option>
    <option value="FF00FF" style="background:#FF00FF; color:#FFFFFF">Pink</option>
    <option value="0000FF" style="background:#0000FF; color:#FFFFFF">Azul</option>
    </select>
    </form>
    <br />
    <span class="texto_pequeno_sucesso">Você pode mudar a cor do player indicando a cor no valor hexadecimal no final da URL(trocando o 000000 pela cor desejada) </span>
     </td>
    </tr>
</table>
          </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Link Player para Winamp</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/winamp.pls"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-winamp.png" width="32" height="32" border="0" /></a><br />
    <br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/winamp.pls"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-winamp.png" width="32" height="32" title="Ouvir no Winamp" /></a></textarea>    </td>
    </tr>
</table>
    </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Link Player para Media Player</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/mediaplayer.asx"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-mediaplayer.png" width="32" height="32" border="0" /></a><br />
    <br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/mediaplayer.asx"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-mediaplayer.png" width="32" height="32" title="Ouvir no MediaPlayer" /></a></textarea>    </td>
    </tr>
</table>
    </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Link Player para Real Player</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/realplayer.rm"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-realplayer.png" width="32" height="32" border="0" /></a><br />
    <br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/realplayer.rm"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-realplayer.png" width="32" height="32" title="Ouvir no RealPlayer" /></a></textarea>    </td>
    </tr>
</table>
    </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Link Player para iTunes</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/itunes.pls"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-itunes.png" width="32" height="32" border="0" /></a><br />
    <br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/itunes.pls"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-itunes.png" width="32" height="32" title="Ouvir no Winamp" /></a></textarea>    </td>
    </tr>
</table>
    </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Link Player para  iPhone/iPad/iPod</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/iphone.m3u"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" border="0" /></a><br />
    <br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/iphone.m3u"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-iphone.png" width="32" height="32" title="Ouvir no iphone" /></a></textarea>    </td>
    </tr>
</table>
    </div>
      </div>      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Link Player para Android</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <?php if($dados_stm["aacplus"] == 'sim') { ?>
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/android.m3u"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-android.png" width="32" height="32" border="0" /></a><br />
    <br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/player/<?php echo $dados_stm["porta"]; ?>/android.m3u"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-android.png" width="32" height="32" title="Ouvir no Android" /></a></textarea>
    <?php } else { ?>
    <span class="texto_status_erro_pequeno">Este player esta disponível apenas para streamings com AAC+ RTMP ativado.</span>
	<?php } ?>    </td>
    </tr>
</table>
    </div>
      </div>
      </td>
    </tr>
    <tr>
      <td height="5" align="center" valign="top" style="padding-left:5px; padding-right:5px">&nbsp;</td>
    </tr>
    <tr>
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong>Postar Player no FaceBook</strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="870" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    <a href="javascript:abrir_janela('http://www.facebook.com/dialog/feed?app_id=522557647825370&display=popup&redirect_uri=http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>/fechar&link=http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>',500,300);" class="share confirm j_share" style="margin-top:1px; float:left;">Compartilhar no FaceBook</a>
    <br />
    <span class="texto_padrao_destaque"><br />
    Copie o código HTML abaixo e insira em seu site para que seus visitantes postem no FaceBook deles.</span><br /><br />
	<textarea name="textarea" readonly="readonly" style="width:99%; height:60px;font-size:11px" onmouseover="this.select()"><a href="http://www.facebook.com/dialog/feed?app_id=522557647825370&display=popup&redirect_uri=http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>/fechar&link=http://<?php echo $dados_config["dominio_padrao"]; ?>/player-facebook/<?php echo $dados_stm["porta"]; ?>"><img src="http://<?php echo $host_sources; ?>/img/icones/img-icone-player-facebook.png" width="32" height="32" title="Player FaceBook" /></a></textarea> </td>
    </tr>
</table>
    </div>
      </div>
      </td>
    </tr>
  </table>
  <br />
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="Fechar" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>