<?php
// Proteção Login
require_once("inc/protecao-admin.php");

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["titulo"]) or empty($_POST["mensagem"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("INSERT INTO avisos (codigo_servidor,area,titulo,descricao,data,mensagem,status) VALUES ('".$_POST["codigo_servidor"]."','".$_POST["area"]."','".$_POST["titulo"]."','".$_POST["descricao"]."',NOW(),'".nl2br($_POST["mensagem"])."','".$_POST["status"]."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_aviso = mysql_insert_id();

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Aviso ".$_POST["titulo"]." cadastrado com sucesso.","ok");

header("Location: /admin/admin-avisos");
?>