<?php
// Prote��o Login
require_once("inc/protecao-admin.php");

// Prote��o contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Aten��o! Acesso n�o autorizado, favor entrar em contato com nosso atendimento para maiores informa��es!</span>");
}
	
if(empty($_POST["ip"]) or empty($_POST["senha"]) or empty($_POST["porta_ssh"])) {
die ("<script> alert(\"Voc� deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

mysql_query("INSERT INTO servidores (nome,ip,senha,porta_ssh,portapro,tipo,limite_streamings,grafico_trafego) VALUES ('".$_POST["nome"]."','".$_POST["ip"]."','".code_decode($_POST["senha"],"E")."','".$_POST["porta_ssh"]."','".$_POST["portapro"]."','".$_POST["tipo"]."','".$_POST["limite_streamings"]."','".$_POST["grafico_trafego"]."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

// Loga a a��o executada
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('cadastro_servidor',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado servidor ".$_POST["nome"]." IP ".$_POST["ip"]."')");

// Adiciona o servidor na zona de DNS do dom�nio padr�o
system('echo "'.strtolower($_POST["nome"]).' 900 IN A '.$_POST["ip"].'" >> /var/named/audiocast.ml.db');

// Cria o sess�o do status das a��es executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Servidor ".$_POST["ip"]." cadastrado com sucesso.","ok");

header("Location: /admin/admin-servidores");
?>