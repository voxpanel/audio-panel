<?php
require_once("inc/protecao-admin.php");

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("inc/classe.ssh.php");

if(empty($_POST["porta"]) or empty($_POST["porta_dj"]) or empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["senha"]) or empty($_POST["identificacao"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Verifica se a porta já esta em uso
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta = '".$_POST["porta"]."'"));

if($total_streamings > 0) {
die ("<script> alert(\"A porta ".$_POST["porta"]." já esta em uso\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = '/admin/admin-cadastrar-streaming'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

if($_POST["aacplus"] == 'sim' && $_POST["servidor_aacplus"] == 0) {
die ("<script> alert(\"Você não selecionou o servidor AAC+\\n \\nPor favor volte e selecione.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

$senha_admin = gera_id(12);

mysql_query("INSERT INTO streamings (codigo_servidor,codigo_servidor_aacplus,porta,porta_dj,ouvintes,bitrate,bitrate_autodj,encoder,espaco,senha,senha_admin,ftp_dir,identificacao,aacplus,email,autodj,data_cadastro,hora_cadastro,local_cadastro,ip_cadastro,programetes) VALUES ('".$_POST["servidor"]."','".$_POST["servidor_aacplus"]."','".$_POST["porta"]."','".$_POST["porta_dj"]."','".$_POST["ouvintes"]."','".$_POST["bitrate"]."','".$_POST["bitrate"]."','".$_POST["encoder"]."','".$_POST["espaco"]."','".$_POST["senha"]."','".$senha_admin."','/home/streaming/".$_POST["porta"]."','".$_POST["identificacao"]."','".$_POST["aacplus"]."','".$_POST["email"]."','".$_POST["autodj"]."',NOW(),NOW(),'painel-admin','".$_SERVER['REMOTE_ADDR']."', '".$_POST['programetes']."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_streaming = mysql_insert_id();

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$_POST["servidor"]."'"));

// Ativa o relay no servidor aacplus
if($_POST["aacplus"] == 'sim') {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$_POST["servidor_aacplus"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/ativar-aacplus ".$_POST["porta"]." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");
}

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("Streaming ".$_POST["porta"]." cadastrado com sucesso.","ok");

header("Location: /admin/admin-streamings/resultado/".$_POST["porta"]."");
?>