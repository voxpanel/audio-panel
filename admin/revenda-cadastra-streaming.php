<?php
require_once("inc/protecao-revenda.php");
require_once('inc/classe.ssh.php');

// Proteção contra acesso direto
if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
die("<span class='texto_status_erro'>Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
}

if(empty($_POST["ouvintes"]) or empty($_POST["bitrate"]) or empty($_POST["senha"])) {
die ("<script> alert(\"Você deixou campos em branco!\\n \\nPor favor volte e tente novamente.\"); 
		 window.location = 'javascript:history.back(1)'; </script>");
}

// Portas inválidas/bloqueadas
$portas_invalidas = array("6000","6665","6666","6667","6668","6669","6984","6985");

// Verifica a última gerada e gera a próxima
$porta_livre_stm = false;
$porta_livre_dj = false;

$nova_porta_stm = 6998;
$nova_porta_dj = 34998;

// Porta Streaming
while(!$porta_livre_stm) {

$nova_porta_stm += 2;

if(!in_array($nova_porta_stm, $portas_invalidas)) {

$total_porta_livre_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta = '".$nova_porta_stm."' ORDER BY porta"));

if($total_porta_livre_stm == 0) {
$porta_livre_stm = true;
}

}

}

// Porta DJ
while(!$porta_livre_dj) {

$nova_porta_dj += 2;

$total_porta_livre_dj = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta_dj = '".$nova_porta_dj."' ORDER BY porta_dj"));

if($total_porta_livre_dj == 0) {
$porta_livre_dj = true;
}

}

$porta = $nova_porta_stm;
$porta_dj = $nova_porta_dj;

// Verifica os limites do cliente
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$ouvintes_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$ouvintes_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."' AND tipo = '2'"));
$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));

// Verifica se excedeu o limite de streamings do cliente
$total_streamings_revenda = $total_streamings_revenda+$total_streamings_subrevenda["total"]+1;

if($total_streamings_revenda > $dados_revenda["streamings"] && $dados_revenda["streamings"] != 999999) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_streamings."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de ouvintes do cliente
$total_ouvintes_revenda = $ouvintes_revenda["total"]+$ouvintes_subrevenda_revenda["total"]+$_POST["ouvintes"];

if($total_ouvintes_revenda > $dados_revenda["ouvintes"] && $dados_revenda["ouvintes"] != 999999) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_ouvintes."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de ouvintes do cliente
$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$_POST["espaco"];

if($total_espaco_revenda > $dados_revenda["espaco"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_espaco_autodj."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

// Verifica se excedeu o limite de bitrate do cliente
if($_POST["bitrate"] > $dados_revenda["bitrate"]) {
die ("<script> alert(\"".lang_info_pagina_cadastrar_streaming_resultado_alerta_limite_bitrate."\"); 
		 window.location = '/admin/revenda-cadastrar-streaming'; </script>");
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

if($_POST["aacplus"] == 'sim' && $_POST["encoder_aacplus"] == 'sim') {
$encoder = "aacp";
$servidor_aacplus = $dados_config["codigo_servidor_aacplus_atual"];
} else {
$encoder = "mp3";
$servidor_aacplus = 0;
}

$encoder_mp3 = ($_POST["encoder_mp3"] == "sim") ? $_POST["encoder_mp3"] : "nao";
$encoder_aacplus = ($_POST["encoder_aacplus"]) ? $_POST["encoder_aacplus"] : "nao";

$senha_admin = gera_id(12);

mysql_query("INSERT INTO streamings (codigo_cliente,codigo_servidor,codigo_servidor_aacplus,porta,porta_dj,ouvintes,bitrate,bitrate_autodj,encoder_mp3,encoder_aacplus,encoder,espaco,senha,senha_admin,ftp_dir,identificacao,aacplus,data_cadastro,hora_cadastro,local_cadastro,ip_cadastro,idioma_painel,email,exibir_app_android,exibir_mini_site,permitir_alterar_senha,autodj,publicserver) VALUES ('".$dados_revenda["codigo"]."','".$dados_config["codigo_servidor_atual"]."','".$servidor_aacplus."','".$porta."','".$porta_dj."','".$_POST["ouvintes"]."','".$_POST["bitrate"]."','".$_POST["bitrate"]."','".$encoder_mp3."','".$encoder_aacplus."','".$encoder."','".$_POST["espaco"]."','".$_POST["senha"]."','".$senha_admin."','/home/streaming/".$porta."','".$_POST["identificacao"]."','".$_POST["aacplus"]."',NOW(),NOW(),'painel-revenda','".$_SERVER['REMOTE_ADDR']."','".$_POST["idioma_painel"]."','".$_POST["email"]."','".$_POST["exibir_app_android"]."','".$_POST["exibir_mini_site"]."','".$_POST["permitir_alterar_senha"]."','".$_POST["autodj"]."','never')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());
$codigo_streaming = mysql_insert_id();

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_config["codigo_servidor_atual"]."'"));

// Ativa o relay no servidor aacplus
if($_POST["aacplus"] == 'sim') {

$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_config["codigo_servidor_aacplus_atual"]."'"));

// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

$ssh->executar("/usr/local/WowzaMediaServer/ativar-aacplus ".$porta." ".$dados_servidor["ip"]." ".$_POST["ouvintes"]."");
}

// Loga a ação executada
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('cadastro_streaming',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado streaming ".$porta." no servidor ".$dados_servidor["ip"]." pela revenda ".$dados_revenda["nome"]."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao(sprintf(lang_info_pagina_cadastrar_streaming_resultado_ok,$porta),"ok");

echo '<script type="text/javascript">top.location = "/admin/revenda/'.code_decode($porta,"E").'"</script>';
?>