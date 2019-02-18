<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["nome"]) or empty($_POST["email"]) or empty($_POST["senha"]) or empty($_POST["streamings"]) or empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["espaco"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("INSERT INTO revendas (id,nome,email,senha,subrevendas,streamings,ouvintes,bitrate,espaco,chave_api,aacplus,data_cadastro) VALUES ('".$_POST["id"]."','".$_POST["nome"]."','".$_POST["email"]."',PASSWORD('".$_POST["senha"]."'),'".$_POST["subrevendas"]."','".$_POST["streamings"]."','".$_POST["ouvintes"]."','".$_POST["bitrate"]."','".$_POST["espaco"]."','".code_decode($_POST["email"],"E")."','".$_POST["aacplus"]."',NOW())") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_revenda = mysql_insert_id();

// Loga a ação executada
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('cadastro_revenda',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrada revenda ".$_POST["nome"]." email ".$_POST["email"]."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Revenda ".$_POST["nome"]." cadastrada com sucesso.","ok");

header("Location: /admin/admin-revendas/resultado/".$_POST["id"]."");
?>