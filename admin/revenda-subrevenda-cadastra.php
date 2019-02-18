<?php
require_once("inc/protecao-revenda.php");
require_once('inc/classe.ssh.php');

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["subrevenda_email"]) or empty($_POST["subrevenda_senha"]) or empty($_POST["streamings"]) or empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["espaco"])) {
die ("<script> alert(\"".lang_info_campos_vazios."\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Verifica os limites da revenda
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$ouvintes_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

// Verifica se excedeu o limite de sub revendas
$total_subrevendas = $total_subrevendas+1;

if($total_subrevendas > $dados_revenda["subrevendas"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_subrevendas,"alerta");
header("Location: /admin/revenda-subrevenda-cadastrar");
exit;
}

// Verifica se excedeu limite de subrevendas ao liberar subrevendas para esta subrevenda
$total_subrevendas_sub = $total_subrevendas+$_POST["subrevendas"];

if($total_subrevendas_sub > $dados_revenda["subrevendas"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_subrevendas,"alerta");
header("Location: /admin/revenda-subrevenda-cadastrar");
exit;
}

// Verifica se excedeu o limite de streamings
$total_streamings_revenda = $total_streamings_revenda+$total_streamings_subrevenda["total"]+$_POST["streamings"];

if($total_streamings_revenda > $dados_revenda["streamings"] && $dados_revenda["streamings"] != 999999) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_streamings,"alerta");
header("Location: /admin/revenda-subrevenda-cadastrar");
exit;
}

// Verifica se excedeu o limite de ouvintes
$total_ouvintes_revenda = $ouvintes_revenda["total"]+$ouvintes_subrevenda_revenda["total"]+$_POST["ouvintes"];

if($total_ouvintes_revenda > $dados_revenda["ouvintes"] && $dados_revenda["ouvintes"] != 999999) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_ouvintes,"alerta");
header("Location: /admin/revenda-subrevenda-cadastrar");
exit;
}

// Verifica se excedeu o limite de espaco FTP
$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$_POST["espaco"];

if($total_espaco_revenda > $dados_revenda["espaco"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_espaco_ftp,"alerta");
header("Location: /admin/revenda-subrevenda-cadastrar");
exit;
}

// Verifica se excedeu o limite de bitrate
if($_POST["bitrate"] > $dados_revenda["bitrate"]) {
$_SESSION["status_acao"] .= status_acao(lang_info_pagina_cadastrar_subrevenda_alerta_limite_bitrate,"alerta");
header("Location: /admin/revenda-subrevenda-cadastrar");
exit;
}

$id = gera_id();

// 1 - revenda | 2 - subrevenda da revenda | 3 - subrevenda da subrevenda
$tipo = (empty($dados_revenda["codigo_revenda"])) ? 2 : 3;

mysql_query("INSERT INTO revendas (codigo_revenda,id,nome,email,senha,subrevendas,streamings,ouvintes,bitrate,espaco,chave_api,aacplus,idioma_painel,tipo,data_cadastro) VALUES ('".$dados_revenda["codigo"]."','".$id."','".$dados_revenda["nome"]."','".$_POST["subrevenda_email"]."',PASSWORD('".$_POST["subrevenda_senha"]."'),'".$_POST["subrevendas"]."','".$_POST["streamings"]."','".$_POST["ouvintes"]."','".$_POST["bitrate"]."','".$_POST["espaco"]."','".code_decode($_POST["subrevenda_email"],"E")."','".$_POST["aacplus"]."','".$_POST["idioma_painel"]."','".$tipo."',NOW())") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_subrevenda = mysql_insert_id();

// Insere a ação executada no registro de logs.
logar_acao("[".$id."] Sub revenda criada com sucesso para revenda ".$dados_revenda["nome"]." - ".$dados_revenda["id"]."");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao(sprintf(lang_info_pagina_cadastrar_subrevenda_resultado_ok,$id),"ok");

echo '<script type="text/javascript">top.location = "/admin/revenda/subrevenda/'.code_decode($codigo_subrevenda,"E").'"</script>';
?>