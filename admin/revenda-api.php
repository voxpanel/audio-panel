<?php
require_once("inc/protecao-revenda.php");

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
            	<div id="quadro-topo"> <strong><?php echo lang_info_pagina_api_tab_titulo; ?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao"><?php echo lang_info_pagina_api_info; ?><br /><br />
<input type="text" value="<?php echo $dados_revenda["chave_api"]; ?>" style="width:99%; height:30px;"  onclick="this.select()" readonly="readonly" /><br />
<br /> 
<?php echo lang_info_pagina_api_info2; ?>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_api_acao_cadastrar; ?></span><br />
<br />
<textarea readonly="readonly" style="width:99%; height:630px;"  onclick="this.select()">
// Função para cadastrar um streaming
// Function to create a streaming
// Función para agregar uno streaming
function cadastrar_streaming($chave,$ouvintes,$bitrate,$espaco,$senha,$aacplus,$idioma_painel,$app_android,$encoder_mp3,$encoder_aacplus) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/api/".$chave."/cadastrar/".$ouvintes."/".$bitrate."/".$espaco."/".$senha."/".$aacplus."/".$idioma_painel."/".$app_android."/".$encoder_mp3."/".$encoder_aacplus."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, "API 1.0 (<?php echo $_SERVER['HTTP_HOST']; ?>)");
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "<?php echo lang_info_pagina_api_acao_info_erro; ?>";
} else {

list($status,$porta,$msg) = explode("|",$resultado);

if($status == 1) {
//Sucesso
return $porta;
} else {
//Erro
return $msg;
}

}

}

// Formato: chave / ouvintes / bitrate(numero apenas) / espaço autodj(megabytes) / senha / AAC+ RTMP (sim ou nao) / Idioma (pt-br / en / es) / App Android (sim ou nao) / Encoder MP3 (sim ou nao) / Encoder AACPlus (sim ou nao)
echo cadastrar_streaming("<?php echo $dados_revenda["chave_api"]; ?>","100","64","1000","xxxxxx","nao","pt-br","sim","sim","sim");

</textarea>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_api_acao_bloquear; ?></span><br />
<br />
<textarea readonly="readonly" style="width:99%; height:570px"  onclick="this.select()">
// Função para bloquear o streaming
// Function to suspend the streaming
// Función para bloquear el streaming
function bloquear_streaming($chave,$porta) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/api/".$chave."/bloquear/".$porta."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, "API 1.0 (<?php echo $_SERVER['HTTP_HOST']; ?>)");
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "<?php echo lang_info_pagina_api_acao_info_erro; ?>";
} else {

list($status,$porta,$msg) = explode("|",$resultado);

if($status == 1) {
//Sucesso
return $msg;
} else {
//Erro
return $msg;
}

}

}

// Formato: chave / porta
echo bloquear_streaming("<?php echo $dados_revenda["chave_api"]; ?>","8000");

</textarea>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_api_acao_debloquear; ?></span><br />
<br />
<textarea readonly="readonly" style="width:99%; height:570px"  onclick="this.select()">
// Função para desbloquear o streaming
// Function to unsuspend the streaming
// Función para desbloquear el streaming
function desbloquear_streaming($chave,$porta) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/api/".$chave."/desbloquear/".$porta."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, "API 1.0 (<?php echo $_SERVER['HTTP_HOST']; ?>)");
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "<?php echo lang_info_pagina_api_acao_info_erro; ?>";
} else {

list($status,$porta,$msg) = explode("|",$resultado);

if($status == 1) {
//Sucesso
return $msg;
} else {
//Erro
return $msg;
}

}

}

// Formato: chave / porta
echo desbloquear_streaming("<?php echo $dados_revenda["chave_api"]; ?>","8000");

</textarea>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_api_acao_alterar_senha; ?></span><br />
<br />
<textarea readonly="readonly" style="width:99%; height:570px"  onclick="this.select()">
// Função para alterar senha do streaming
// Function to change password of streaming
// Función para cambiar la contraseña de streaming
function alterar_senha($chave,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/api/".$chave."/alterar_senha/".$porta."/".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, "API 1.0 (<?php echo $_SERVER['HTTP_HOST']; ?>)");
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "<?php echo lang_info_pagina_api_acao_info_erro; ?>";
} else {

list($status,$porta,$msg) = explode("|",$resultado);

if($status == 1) {
//Sucesso
return $msg;
} else {
//Erro
return $msg;
}

}

}

// Formato: chave / porta / nova senha
echo alterar_senha("<?php echo $dados_revenda["chave_api"]; ?>","8000","xxxx");

</textarea>
<br />
<br />
<span class="texto_padrao_destaque"><?php echo lang_info_pagina_api_acao_remover; ?></span><br />
<br />
<textarea readonly="readonly" style="width:99%; height:550px"  onclick="this.select()">
// Função para remover o streaming
// Function to remove the streaming
// Función para eliminar el streaming
function remover_streaming($chave,$porta) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/api/".$chave."/remover/".$porta."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, "API 1.0 (<?php echo $_SERVER['HTTP_HOST']; ?>)");
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "<?php echo lang_info_pagina_api_acao_info_erro; ?>";
} else {

list($status,$porta,$msg) = explode("|",$resultado);

if($status == 1) {
//Sucesso
return $msg;
} else {
//Erro
return $msg;
}

}

}

// Formato: chave / porta
echo remover_streaming("<?php echo $dados_revenda["chave_api"]; ?>","8000");

</textarea>
<br />
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
