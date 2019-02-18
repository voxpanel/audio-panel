<?php
require_once("admin/inc/protecao-final.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
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
            	<div id="quadro-topo"><strong>Downloads</strong></div>
<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td width="25%" height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-simplecast.png" alt="Download" width="64" height="64" /></td>
    <td width="25%" height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-simplecast.png" alt="Download" width="64" height="64" /></td>
    <td width="25%" height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-samcast.png" alt="Download" width="64" height="64" /></td>
    <td width="25%" height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-orban.png" alt="Download" width="64" height="64" /></td>
  </tr>
  <tr>
    <td height="70" align="center" class="texto_padrao">SimpleCast 3.1<br />
      <span class="texto_padrao_pequeno">Windows XP/7</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/SimpleCast-3.1.0.rar" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">SimpleCast 3.2<br />
      <span class="texto_padrao_pequeno">Windows 8</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/SimpleCast-3.2.0.rar" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">Sam Broadcaster 4.9.1<br />
      <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/SamBroadcaster-4.9.1.rar" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">Opticodec PC SE<br />
      <span class="texto_padrao_pequeno">Windows XP</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/Opticodec-PC-SE-winxp.rar" target="_blank" class="texto_padrao_verde">[download]</a></td>
  </tr>
  <tr>
    <td height="20" colspan="4" align="center" class="texto_padrao">&nbsp;</td>
    </tr>
  <tr>
    <td height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-orban.png" alt="Download" width="64" height="64" /></td>
    <td height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-zararadio.png" alt="Download" width="64" height="64" /></td>
    <td height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-filezilla.png" alt="Download" width="64" height="64" /></td>
    <td height="70" align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-winamp.png" alt="Download" width="64" height="64" /></td>
  </tr>
  <tr>
    <td height="70" align="center" class="texto_padrao">Opticodec PC SE<br />
      <span class="texto_padrao_pequeno">Windows 7/8</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/Opticodec-PC-SE-win7-8.rar" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">Zara Radio Portable<br />
      <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/ZaraRadio-1.6.2-portable.exe" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">FileZilla FTP<br />
      <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/FileZilla-3.10.3.exe" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">Winamp<br />
      <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
  <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/winamp.exe" target="_blank" class="texto_padrao_verde">[download]</a></td>
  </tr>
  <tr>
    <td height="20" colspan="4" align="center" class="texto_padrao">&nbsp;</td>
    </tr>
  <tr>
    <td align="center" class="texto_padrao"><img src="img/icones/img-icone-programa-winamp.png" alt="Download" width="64" height="64" /></td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="texto_padrao">ShoutCast DSP Plugin<br />
      <span class="texto_padrao_pequeno">Windows XP/7/8</span><br />
  <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dlp/plugin-dsp.exe" target="_blank" class="texto_padrao_verde">[download]</a></td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
    <td height="70" align="center" class="texto_padrao">&nbsp;</td>
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
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>