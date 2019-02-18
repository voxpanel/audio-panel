<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("inc/classe.ssh.php");
require_once("inc/classe.ftp.php");
require_once("inc/classe.mail.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('2');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar streaming
if($acao == "ligar_streaming") {


	$porta = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "desligado") {
	
	$total_multipoint = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($total_multipoint > 0) {
	
	$sql_multipoint = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
	while ($dados_ponto = mysql_fetch_array($sql_multipoint)) {
	
	$config_streaming_multipoint .= "streamid_".$dados_ponto["id"]."=".$dados_ponto["id"]."\n";
	$config_streaming_multipoint .= "streampath_".$dados_ponto["id"]."=".$dados_ponto["ponto"]."\n";
	$config_streaming_multipoint .= "streammaxuser_".$dados_ponto["id"]."=".$dados_ponto["ouvintes"]."\n\n";

	}
	
	}
	
	$senha_admin = ($dados_stm["senha_admin"]) ? $dados_stm["senha_admin"] : microtime();
	
	$config_streaming = ";DADOS GERAIS\n";
	
	if($total_multipoint == 0) {
	$config_streaming .= "streamid=1\n";
	}
	
	$config_streaming .= "portbase=".$dados_stm["porta"]."\n";
	$config_streaming .= "maxuser=".$dados_stm["ouvintes"]."\n";
	$config_streaming .= "adminpassword=".$senha_admin."\n";
	$config_streaming .= "password=".$dados_stm["senha"]."\n";
	$config_streaming .= "srcip=any\n";
	$config_streaming .= "destip=any\n";
	$config_streaming .= "yport=80\n";
	$config_streaming .= "namelookups=0\n";
	$config_streaming .= "publicserver=".$dados_stm["publicserver"]."\n";
	$config_streaming .= "allowrelay=".$dados_stm["allowrelay"]."\n";
	$config_streaming .= "allowpublicrelay=1\n";
	$config_streaming .= "metainterval=32768\n";
	$config_streaming .= ";LOGS\n";
	$config_streaming .= "logfile=/home/streaming/logs/log-".$dados_stm["porta"].".log\n";
	$config_streaming .= "banfile=/home/streaming/configs/".$dados_stm["porta"].".ban\n";
	$config_streaming .= "banfile=/home/streaming/configs/".$dados_stm["porta"].".agent\n";
	$config_streaming .= "flashpolicyfile=/home/streaming/configs/crossdomain.xml\n";
	$config_streaming .= "pidfile=/home/streaming/logs/".$dados_stm["porta"].".pid\n";
	$config_streaming .= "w3cenable=0\n";
	$config_streaming .= "logclients=0\n";
	$config_streaming .= "showlastsongs=".$dados_stm["showlastsongs"]."\n";
	$config_streaming .= ";YPSERVER\n";
	$config_streaming .= "ypaddr=46.105.114.166\n";
	$config_streaming .= "ypport=80\n";
	$config_streaming .= "ypPath=/yp2\n";
	$config_streaming .= "ypTimeout=10\n";
	$config_streaming .= "ypmaxretries=10\n";
	$config_streaming .= "ypreportinterval=3600\n";
	$config_streaming .= "ypminreportinterval=1800\n";
	
	if($dados_stm["arquivo_intro"]) {
	$config_streaming .= "introfile=/home/streaming/".$dados_stm["porta"]."/streaming_intro.mp3\n";
	}
	
	if($dados_stm["arquivo_backup"]) {
	$config_streaming .= "backupfile=/home/streaming/".$dados_stm["porta"]."/streaming_backup.mp3\n";
	}
	
	if($dados_stm["relay"] == "sim") {
	$config_streaming .= ";RELAY\n";
	$config_streaming .= "streamrelayurl=http://".$dados_stm["relay_ip"].":".$dados_stm["relay_porta"]."\n";
	$config_streaming .= "relayreconnecttime=1\n";	
	}
	
	if($total_multipoint > 0) {
	$config_streaming .= "\n;MULTI POINT\n";
	$config_streaming .= $config_streaming_multipoint;	
	}
	
	$config_streaming = gerar_conf_streaming($dados_stm["porta"],$config_streaming);
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->enviar_arquivo("../temp/".$config_streaming."","/home/streaming/configs/".$config_streaming."",0777);
	
	unlink("../temp/".$config_streaming."");
	
	$resultado = $ssh->executar("/home/streaming/ligar_streaming /home/streaming/configs/".$config_streaming."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if(is_numeric($resultado)) {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." ligado com sucesso.</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Streaming ligado com sucesso.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível ligar o streaming ".$dados_stm["porta"]."<br><strong>Log:</strong> Erro desconhecido, tente novamente em alguns segundos.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível ligar o streaming, erro desconhecido.");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível ligar o streaming ".$dados_stm["porta"]."<br><strong>Log:</strong>".$resultado."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível ligar o streaming, erro: ".$resultado."");
	
	}
	
	} else {
	echo "<span class='texto_status_alerta'>Não foi possível ligar o streaming ".$dados_stm["porta"].", ele já esta ligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível ligar o streaming, ele já esta ligado");
	
	}
	
	exit();
}

// Função para desligar streaming
if($acao == "desligar_streaming") {


	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Desliga o autodj caso esteja ligado
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = $ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "desligado") {
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." desligado com sucesso.</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Streaming desligado com sucesso.");
	
	
	} else {
	echo "<span class='texto_status_erro'>Não foi possível desligar o streaming ".$dados_stm["porta"]."<br>Log:  Erro desconhecido, tente novamente em alguns segundos.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível desligar o streaming, erro desconhecido.");
	
	}
	
	} else {
	echo "<span class='texto_status_erro'>".$resultado."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível desligar o streaming, erro: ".$resultado."");
	}
	
	} else {
	echo "<span class='texto_status_alerta'>Não foi possível desligar o streaming ".$dados_stm["porta"].", ele não esta ligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível desligar o streaming, ele não esta ligado.");
	
	}
	
	exit();
}

// Função para bloquear streaming
if($acao == "bloquear_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}


	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	exit();	
	}
	
	$status = ($_SESSION["type_logged_user"] == "operador") ? 2 : 3;
	$status_log = ($_SESSION["type_logged_user"] == "operador") ? "pelo administrador." : "pela revenda.";
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	
	mysql_query("Update streamings set status ='".$status."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." bloqueado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Streaming bloqueado com sucesso ".$status_log."");
	
	// Loga data do bloqueio
	@mysql_query("Update streamings set data_bloqueio = NOW() WHERE codigo = '".$dados_stm["codigo"]."'");
	
	}
	
	exit();
}

// Função para desbloquear streaming
if($acao == "desbloquear_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}


	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	$status_log = ($_SESSION["type_logged_user"] == "operador") ? "pelo administrador." : "pela revenda.";
	
	if($_SESSION["type_logged_user"] == "cliente" && $dados_stm["status"] == 2) {
	
	echo "<span class='texto_status_erro'>Você não tem permissão para desbloquear o streaming <strong>".$porta."</strong> contate o suporte.</span>";
	
	exit();	
	}

	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." desbloqueado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	mysql_query("Update streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Streaming desbloqueado com sucesso ".$status_log."");
	
	}
	
	exit();
}

// Função para remover streaming
if($acao == "remover_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}



	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$checar_streaming = mysql_num_rows(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	if($checar_streaming == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Streaming ".$porta." não encontrado.</span>";
	
	exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	$status_log = ($_SESSION["type_logged_user"] == "operador") ? "pelo administrador." : "pela revenda.";
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	// Desliga o autodj caso esteja ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	}

	$resultado = $ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
		
	$resultado = str_replace("\n","",$resultado);
	
	} else {
	$resultado = "ok";
	}
	
	if($resultado == "ok") {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "desligado") {
	
	// Desbloqueia o streaming no servidor
	$porta = $dados_stm["porta"];
	$porta_ouvinte = $dados_stm["porta"]+1;
	$porta_dj = $dados_stm["porta_dj"];
	
	$ssh->executar("iptables-save > /tmp/iptables.rules;sed -i '/".$porta."/d' /tmp/iptables.rules;sed -i '/".$porta_ouvinte."/d' /tmp/iptables.rules;iptables-restore < /tmp/iptables.rules;service iptables save;rm -f /tmp/iptables.rules;echo ok");
	
	$ssh->executar("rm -rf /home/streaming/".$dados_stm["porta"]." /home/streaming/configs/*".$dados_stm["porta"]."* /home/streaming/logs/*".$dados_stm["porta"]."* /home/streaming/playlists/".$dados_stm["porta"]."-*.pls;echo ok");
	
	mysql_query("Delete From streamings where codigo = '".$dados_stm["codigo"]."'");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	mysql_query("Delete From playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	}
	
	// Remove as estatisticas
	mysql_query("Delete From estatisticas where codigo_stm = '".$dados_stm["codigo"]."'");

	// Remove os DJs
	mysql_query("Delete From djs where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove os Agendamentos
	mysql_query("Delete From playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove solicitações de app
	mysql_query("Delete From apps where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove logs
	mysql_query("Delete From logs_streamings where codigo_stm = '".$dados_stm["codigo"]."'");
	mysql_query("Delete From logs_migracoes where codigo_stm = '".$dados_stm["codigo"]."'");
	mysql_query("Delete From dicas_rapidas_acessos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove migração de musicas
	mysql_query("Delete FROM migracoes where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Loga a ação executada
    mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('".$acao."',NOW(),'".$_SERVER['REMOTE_ADDR']."','Remoção do streaming ".$dados_stm["porta"]." ".$status_log."')");
	
	// Remove app android
	mysql_query("Delete From apps where codigo_stm = '".$dados_stm["codigo"]."'");
	@unlink("../app_android/apps/".$dados_app["zip"]."");
	@unlink("../".$dados_app["print"]."");	
	
	// Desativa o relay no servidor RTMP	
	if($dados_stm["aacplus"] == 'sim') {
	
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

	$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$dados_stm["porta"]."");
	
	}
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." removido com sucesso.</span><br /><br/><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Streaming removido com sucesso ".$status_log."");
	
	} else {
	echo "<span class='texto_status_erro'>Não foi possível remover o streaming ".$dados_stm["porta"]."<br>Log:  Erro desconhecido, tente novamente em alguns segundos.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível remover o streaming, erro desconhecido.");
	
	}
	
	} else {
	echo "<span class='texto_status_erro'>Não foi possível remover o streaming ".$dados_stm["porta"]."<br>Log: ".$resultado."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível remover o streaming, erro: ".$resultado."");
	
	}
	
	}
	
	exit();
}

// Função para verificar o status do streaming e autodj
if($acao == "status_streaming") {

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "manutencao";
	exit();
	}
	
	$status_conexao = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	$status_conexao_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	$status_conexao_relay = status_relay($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_conexao_relay == "ligado") {
	echo "ligado-relay";
	exit();
	}
	
	if($status_conexao_autodj == "ligado") {
	echo "ligado-autodj";
	exit();
	}	
	
	if($status_conexao == "ligado") {
	echo "ligado";
	exit();
	}
	
	echo "desligado";
	
	exit();
	
}

// Função para desconectar o source do streaming(kick)
if($acao == "kick_streaming") {


	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	$kick = curl_init();
	curl_setopt($kick, CURLOPT_URL, "http://".$dados_servidor["ip"].":".$dados_stm["porta"]."/admin.cgi?sid=1&mode=kicksrc&pass=".$dados_stm["senha"]."");
	curl_setopt($kick, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($kick, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$resultado = curl_exec($kick);
	curl_close($kick);
	
	if(preg_match('/redirect/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>DJ(source) do streaming ".$dados_stm["porta"]." desconectado com sucesso.</span>";
	
	} else {	
	echo "<span class='texto_status_erro'>Não foi possível desconectar o DJ(source) do streaming ".$dados_stm["porta"]."<br>Log: ".$resultado."</span>";
	}
	
	} else {
	echo "<span class='texto_status_erro'>Não foi possível desconectar o DJ(source) do streaming ".$dados_stm["porta"]."<br>Log: O streaming não esta ligado.</span>";
	}
	
	exit();
	
}

// Função para atualizar o uso do espaço em disco do FTP
if($acao == "atualizar_espaco_usado") {

	$porta = query_string('3');
	$espaco_usado = round(query_string('4')/1024/1024);
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));

	mysql_query("Update streamings set espaco_usado = '".$espaco_usado."' where codigo = '".$dados_stm["codigo"]."'");
	
	exit();

}

// Função para checar a quantidade de ouvintes online e criar a barra de porcentagem de uso
if($acao == "estatistica_uso_plano") {

	$porta = query_string('3');
	$recurso = query_string('4');
	$texto = query_string('5');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

	if($recurso == "ouvintes") {
	
	if($dados_stm["aacplus"] == 'sim') {
	
	if($total_pontos > 0) {
	
	$sql_pontos = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
	while ($dados_ponto = mysql_fetch_array($sql_pontos)) {
	$stats_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],$dados_ponto["id"]);	
	$ouvintes_total_shoutcast += $stats_shoutcast["ouvintes_total"];
	}
	
	} else {
	$stats_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
	$ouvintes_total_shoutcast = $stats_shoutcast["ouvintes_total"];
	}
	
	$stats_aacplus = stats_ouvintes_aacplus($dados_stm["porta"],$dados_servidor_aacplus["ip"],$dados_servidor_aacplus["senha"]);
	$ouvintes_conectados = $ouvintes_total_shoutcast+$stats_aacplus["ouvintes"];
	
	if($ouvintes_conectados == 0) {
	$ouvintes_conectados = $ouvintes_conectados;
	} else {
	$ouvintes_conectados = $ouvintes_conectados-1;
	}

	} else {
	
	if($total_pontos > 0) {
	
	$sql_pontos = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
	while ($dados_ponto = mysql_fetch_array($sql_pontos)) {
	$stats_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],$dados_ponto["id"]);	
	$ouvintes_conectados += $stats_shoutcast["ouvintes_total"];
	}
	
	} else {
	$stats_shoutcast = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
	$ouvintes_conectados = $stats_shoutcast["ouvintes_total"];
	}

	}
	
	$porcentagem_uso_ouvintes = ($dados_stm["ouvintes"] == 0) ? "0" : $ouvintes_conectados*100/$dados_stm["ouvintes"];	
	$porcentagem_uso_ouvintes = ($porcentagem_uso_ouvintes < 1 && $ouvintes_conectados > 0) ? "1" : $porcentagem_uso_ouvintes;
	
	$modo_texto = ($texto == "sim") ? '<span class="texto_padrao_pequeno">('.$ouvintes_conectados.' de '.$dados_stm["ouvintes"].')</span>' : '';
	
	echo barra_uso_plano($porcentagem_uso_ouvintes,'('.$ouvintes_conectados.' de '.$dados_stm["ouvintes"].')').'&nbsp;'.$modo_texto;
		
	} else {
	
	$porcentagem_uso_espaco = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];
	
	$modo_texto = ($texto == "sim") ? '<span class="texto_padrao_pequeno">('.tamanho($dados_stm["espaco_usado"]).' de '.tamanho($dados_stm["espaco"]).')</span>' : '';
	
	echo barra_uso_plano($porcentagem_uso_espaco,'('.tamanho($dados_stm["espaco_usado"]).' de '.tamanho($dados_stm["espaco"]).')').'&nbsp;'.$modo_texto;
	
	}
	
	exit();
}

// Função para exibir a música atual tocando no streaming
if($acao == "musica_atual") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = query_string('3');
	$limite_caracteres = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$host_sources = ($dados_config["usar_cdn"] == "sim") ? $dados_config["dominio_cdn"] : $_SERVER['HTTP_HOST'];

	if($dados_config["usar_cdn"] == "sim") {
	
	$musica = file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&recurso=musica&ponto=1");
	
	} else {
	
	//$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
	
	$musica = $info["musica"];
	
	}
	
	if(strlen($musica) > $limite_caracteres) {
	echo "<img src='http://".$host_sources."/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;".substr($musica, 0, $limite_caracteres)."...";
	} else {
	echo "<img src='http://".$host_sources."/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;".$musica;
	}
	
	exit();
	
}

// Função para admin/revenda acessar painel de streaming
if($acao == "acessar_painel_streaming") {

	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}


	
	$porta = code_decode(query_string('3'),"D");

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));

	echo code_decode($dados_stm["porta"],"E")."@".code_decode($dados_stm["senha"],"E");
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Acesso administrativo ao painel deo streaming executado com sucesso.");
	
	exit();
	
}

// Função para ativar proteção contra ataques ao streaming
if($acao == "ativar_desativar_protecao") {


	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	if($dados_stm["protecao"] == "0") {	
	
	// Adiciona proteção ao iptables
	$ssh->executar("iptables -A INPUT -p tcp --syn --dport ".$dados_stm["porta"]." -m connlimit --connlimit-above 5 -j REJECT --reject-with tcp-reset;service iptables save;echo ok");
	
	mysql_query("Update streamings set protecao = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Proteção contra ataques ativada com sucesso.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Proteção contra ataques ativada com sucesso.");
	
	} else {
	
	$ssh->executar("iptables -D INPUT -p tcp --syn --dport ".$dados_stm["porta"]." -m connlimit --connlimit-above 5 -j REJECT --reject-with tcp-reset;service iptables save;echo ok");
	
	mysql_query("Update streamings set protecao = '0' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Proteção contra ataques desativada com sucesso.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Proteção contra ataques desativada com sucesso.");
	
	}
	
	}
	
	exit();
}

// Função para sincronizar streaming no servidor AAC+
if($acao == "sincronizar_aacplus") {


	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Ativa o relay no servidor RTMP	
	if($dados_stm["aacplus"] == 'sim') {
	
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
	
	$servidor_stm = strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/sincronizar-aacplus ".$dados_stm["porta"]." ".$servidor_stm." ".$dados_stm["ouvintes"]."");
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." sincronizado com sucesso.</span>";
	
	} else {
	echo "<span class='texto_status_erro'>O streaming ".$dados_stm["porta"]." não está configurado com AAC+ com RTMP.</span>";	
	}
	
	}
	
	exit();
	
}

// Função para sincronizar as playlists do streaming no servidor
if($acao == "sincronizar_playlists") {
	

	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$sql_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($sql_playlists)) {
		
	$sql_playlist_musicas = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
	while ($dados_musica = mysql_fetch_array($sql_playlist_musicas)) {
	
	// Adiciona a música na lista para adicionar ao arquivo da playlist
	$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$dados_musica["path_musica"]."\n";
	
	}
	
	// Cria o arquivo da playlist para enviar ao servidor do streaming
	$config_playlist = gerar_playlist($dados_playlist["arquivo"],$lista_musicas);
	
	// Envia o arquivo da playlist para o servidor do streaming
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_playlist."","/home/streaming/playlists/".$dados_playlist["arquivo"]."",0777);

	// Remove o arquivo temporário usado para criar a playlist
	unlink("/home/painel/public_html/temp/".$config_playlist."");
	
	}
	
	echo "<span class='texto_status_sucesso'>Playlists do streaming ".$dados_stm["porta"]." sincronizadas com sucesso.</span>";
	
	}
	
	exit();
}

// Função para configurar o encoder correto no streaming
if($acao == "configurar_encoder") {


	$porta = code_decode(query_string('3'),"D");

	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se o RTMP esta ativado
	if($dados_stm["aacplus"] == 'sim') {
	
	// RTMP ativado -> Encoder AAC+
	if($dados_stm["encoder"] == 'mp3') {
	
	mysql_query("Update streamings set encoder = 'aacp', codigo_servidor_aacplus = '".$dados_config["codigo_servidor_aacplus_atual"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	// Sincroniza AAC+
	if($dados_stm["codigo_servidor_aacplus"] == 0) {
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_config["codigo_servidor_aacplus_atual"]."'"));
	} else {
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$dados_stm["porta"]."");
	$ssh->executar("/usr/local/WowzaMediaServer/ativar-aacplus ".$dados_stm["porta"]." ".$dados_servidor["ip"]." ".$dados_stm["ouvintes"]."");
	
	echo "<span class='texto_status_sucesso'>".lang_acao_pagina_resolver_problemas_encoder_resultado_ok."</span>";
	
	exit();
	
	}
	
	} else {
	
	// RTMP desativado -> Encoder MP3
	if($dados_stm["encoder"] == 'aacp') {
	
	mysql_query("Update streamings set encoder = 'mp3' where codigo = '".$dados_stm["codigo"]."'");

	
	echo "<span class='texto_status_sucesso'>".lang_acao_pagina_resolver_problemas_encoder_resultado_ok."</span>";

	exit();
	
	}
	
	}	
	
	// tudo certo nada a fazer
	echo "<span class='texto_status_alerta'>".lang_acao_pagina_resolver_problemas_encoder_resultado_alerta."</span>";
	
	}

	exit();
	
}

// Função para atualizar cache player facebook
if($acao == "atualizar_cache_player_facebook") {


	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".lang_alerta_dados_faltando."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.lang_info_pagina_resolver_problemas_tab_titulo_facebook.'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
   <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="left" class="texto_padrao"><br />'.sprintf(lang_acao_pagina_resolver_problemas_player_facebook,$dados_config["dominio_padrao"],$dados_stm["porta"],$dados_config["dominio_padrao"],$dados_stm["porta"]).'<br /></td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	exit();
	
}

////////////////////////////////////////////////////////
///////////// Funções Gerenciamento AutoDJ /////////////
////////////////////////////////////////////////////////

// Função para carregar as playlists do streaming
if($acao == "carregar_playlists") {


	$porta = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se o relay esta ativado
	if($dados_stm["relay"] == "sim") {
	
	echo "<span class='texto_status_erro'>O AutoDJ não pode ser iniciado pois o relay esta ativado.</span><br />";
	echo "<span class='texto_status_erro_pequeno'>Para desativar clique em Configurar Relay no painel do streaming.</span>";
	
	exit();
	}
	
	// Verifica se o autodj já esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	echo "<span class='texto_status_erro'>O AutoDJ ".$dados_stm["porta"]." já esta em execussão.<br />Para iniciar outra playlist você deve desligar o AutoDJ.</span>";
	
	exit();
	}
	
	$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
	$dados_ultima_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
	$total_musicas_ultima_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_stm["ultima_playlist"]."'"));
		
	if($total_playlists > 0) {
	
	echo '<span style="color: #FFFFFF;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:20px;font-weight:bold;">Ligar AutoDJ '.$dados_stm["porta"].'</span><br />';
	echo '<span style="color: #FFFFFF;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;">(escolha as opções desejadas, as últimas configurações usadas já estão selecionadas)</span><br /><br />';
	echo '<select name="playlist" id="playlist" style="width:400px;">';
	echo '<optgroup label="Última Playlist Executada">';
	if($dados_stm["ultima_playlist"] > 0) {
	echo '<option value="'.$dados_stm["ultima_playlist"].'">'.$dados_ultima_playlist["nome"].' ('.$total_musicas_ultima_playlist.')</option>';
	}
	echo '</optgroup>';
	echo '<optgroup label="Playlists">';
	
	$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'"));

	echo '<option value="'.$dados_playlist["codigo"].'">'.$dados_playlist["nome"].' ('.$total_musicas.')</option>';
		
	}
	
	echo '</optgroup>';
	echo '</select>';
	echo '<br />';
	echo '<select name="shuffle" id="shuffle" style="width:135px;">';
	echo '<optgroup label="Ordem das Músicas">';
	echo '<option value="0" selected="selected">Seguir Orderm</option>';
	echo '<option value="1">Misturar</option>';
	echo '</optgroup>';
	echo '</select>';
	echo '<select name="xfade" id="xfade" style="width:105px;">';
	echo '<optgroup label="AutoMix/xFade">';
	
	foreach(array("0" => "(sem xfade)","2" => "2 segundos","4" => "4 segundos","6" => "6 segundos","8" => "8 segundos","10" => "10 segundos") as $xfade => $xfade_descricao){
	
		if($xfade == $dados_stm["xfade"]) {
			echo '<option value="'.$xfade.'" selected="selected">'.$xfade_descricao.'</option>';
		} else {
			echo '<option value="'.$xfade.'">'.$xfade_descricao.'</option>';
		}
	
	}
	
	echo '</optgroup>';
	echo '</select>';
	echo '<select name="bitrate" id="bitrate" style="width:160px;">';
	echo '<optgroup label="Bitrate">';
	
	foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
	
		if($bitrate <= $dados_stm["bitrate"]) {
			
			if($dados_stm["bitrate_autodj"]) {
		
			if($bitrate == $dados_stm["bitrate_autodj"]) {
				echo '<option value="'.$bitrate.'" selected="selected">'.$bitrate.' Kbps(último usado)</option>';
			} else {
				echo '<option value="'.$bitrate.'">'.$bitrate.' Kbps</option>';
			}
			
			} else {
			
			if($bitrate == $dados_stm["bitrate"]) {
				echo '<option value="'.$bitrate.'" selected="selected">'.$bitrate.' Kbps</option>';
			} else {
				echo '<option value="'.$bitrate.'">'.$bitrate.' Kbps</option>';
			}
			
			}
		
		}
		
	}	
		
	echo '</optgroup>';
	echo '</select>';
	echo '<br />';
	echo '<br />';	
	echo '<input type="button" class="botao" value="Ligar AutoDJ" onclick="ligar_autodj(\''.code_decode($dados_stm["porta"],"E").'\',document.getElementById(\'playlist\').value,document.getElementById(\'shuffle\').value,document.getElementById(\'bitrate\').value,document.getElementById(\'xfade\').value);" />';
	
	} else {
	echo "<span class='texto_status_erro'>O streaming ".$dados_stm["porta"]." não possui nenhuma playlist criada.<br />Você deve criar uma playlist para ligar o AutoDJ.</span>";
	}
	
	exit();

}

// Função para ligar autodj
if($acao == "ligar_autodj") {


	$porta = code_decode(query_string('3'),"D");
	$playlist = query_string('4');
	$shuffle = query_string('5');
	$bitrate = query_string('6');
	$xfade = query_string('7');
	$agendamento = query_string('8');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	// Verifica se o autodj já esta ligado
	//$status_autodj = $ssh->executar("nice --adjustment=-20 nmap -sT -p ".$dados_stm["porta_dj"]." localhost | grep open | wc -l");
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	echo "<span class='texto_status_erro'>O AutoDJ ".$dados_stm["porta"]." já esta em execussão.<br />Para iniciar outra playlist você deve desligar o AutoDJ.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível ligar o AutoDJ, ele já esta em execussão.");

	} else {
	
	// Carrega os DJs
	$qtd_djs_restricoes = 0;
	
	$sql_djs = mysql_query("SELECT * FROM djs where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_dj = mysql_fetch_array($sql_djs)) {
	
	$djs_config_autodj[] = $dados_dj["login"]."|".$dados_dj["senha"];
	
	$djs_config_autodj_calendar[$dados_dj["login"]]["login"] = $dados_dj["login"];
	$djs_config_autodj_calendar[$dados_dj["login"]]["senha"] = $dados_dj["senha"];
	
	$sql_djs_restricoes = mysql_query("SELECT * FROM djs_restricoes where codigo_dj = '".$dados_dj["codigo"]."' AND codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_dj_restricao = mysql_fetch_array($sql_djs_restricoes)) {
	
	$djs_restricoes .= $dados_dj_restricao["hora_inicio"]."|".$dados_dj_restricao["hora_fim"]."|".$dados_dj_restricao["dias_semana"].",";
	$qtd_djs_restricoes++;
	}
	
	$djs_config_autodj_calendar[$dados_dj["login"]]["restricoes"] = substr($djs_restricoes,0,-1);
	
	$djs_config_autodj_calendar[$dados_dj["login"]]["total_restricoes"] = $qtd_djs_restricoes;
	
	unset($djs_restricoes);
	unset($qtd_djs_restricoes);
	}
	
	// Carrega os agendamentos de relay
	$sql_agendamento_relay = mysql_query("SELECT * FROM relay_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
	while ($dados_agendamento_relay = mysql_fetch_array($sql_agendamento_relay)) {
	
	$relay_config_calendar[] = "".$dados_agendamento_relay["servidor"]."|".$dados_agendamento_relay["frequencia"]."|".$dados_agendamento_relay["data"]."|".$dados_agendamento_relay["hora"]."|".$dados_agendamento_relay["minuto"]."|".$dados_agendamento_relay["duracao_hora"]."|".$dados_agendamento_relay["duracao_minuto"]."|".$dados_agendamento_relay["dias"]."|".$dados_agendamento_relay["repetir"]."";
	
	}
	
	$total_multipoint = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($total_multipoint > 0) {
	
	$sql_multipoint = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
	while ($dados_ponto = mysql_fetch_array($sql_multipoint)) {
	
	$multipoint .= "uvoxstreamid_".$dados_ponto["id"]."=".$dados_ponto["id"]."\n";
	$multipoint .= "serverip_".$dados_ponto["id"]."=localhost\n";
	$multipoint .= "serverport_".$dados_ponto["id"]."=".$dados_stm["porta"]."\n";
	$multipoint .= "uvoxauth_".$dados_ponto["id"]."=".$dados_stm["senha"]."\n";
	$multipoint .= "encoder_".$dados_ponto["id"]."=".$dados_ponto["encoder"]."\n";
	$multipoint .= "bitrate_".$dados_ponto["id"]."=".$dados_ponto["bitrate"]."000\n";
	$multipoint .= "channels_".$dados_ponto["id"]."=".$dados_stm["autodj_channels"]."\n";
	$multipoint .= "samplerate_".$dados_ponto["id"]."=".$dados_stm["autodj_samplerate"]."\n";
	$multipoint .= "endpointname_".$dados_ponto["id"]."=".$dados_ponto["ponto"]."\n";
	$multipoint .= "outprotocol_".$dados_ponto["id"]."=3\n\n";
	
	}
	
	$config_autodj .= "playlistfile=/home/streaming/playlists/".$dados_stm["porta"].".pls\n";
	$config_autodj .= "logfile=/home/streaming/logs/autodj-".$dados_stm["porta"].".log\n\n";
	$config_autodj .= ";MULTI POINT\n";
	$config_autodj .= $multipoint;
	$config_autodj .= ";DADOS GERAIS\n";
	$config_autodj .= "streamtitle=".$dados_stm["streamtitle"]."\n";
	$config_autodj .= "streamurl=".$dados_stm["streamurl"]."\n";
	$config_autodj .= "genre=".$dados_stm["genre"]."\n";
	$config_autodj .= "shuffle=".$shuffle."\n";
	$config_autodj .= "xfade=".$xfade."\n";
	$config_autodj .= "xfadethreshold=20\n";
	$config_autodj .= "flashpolicyfile=/home/streaming/configs/crossdomain.xml\n\n";
	$config_autodj .= ";DJ PORT CONFIG\n";
	$config_autodj .= "calendarfile=/home/streaming/configs/calendar-".$dados_stm["porta"].".xml\n";
	$config_autodj .= "calendarrewrite=0\n";
	$config_autodj .= "djport=".$dados_stm["porta_dj"]."\n";
	
	$qtd_djs = 1;
	
	if($djs_config_autodj) {

	foreach($djs_config_autodj as $dj_config) {
	
	list($dj, $senha) = explode("|", $dj_config);
	
	$config_autodj .= "djlogin_".$qtd_djs."=".$dj."\n";
	$config_autodj .= "djpassword_".$qtd_djs."=".$senha."\n";
	
	$qtd_djs++;
	}
	
	}
	
	$config_autodj .= "\n;LICENCA MP3\n";
	$config_autodj .= "unlockkeyname=Advance Host\n";
	$config_autodj .= "unlockkeycode=F1ZA7-UXBW0-78TVK-Q02PB\n";
	
	} else {
	
	$config_autodj .= "playlistfile=/home/streaming/playlists/".$dados_stm["porta"].".pls\n";
	$config_autodj .= "logfile=/home/streaming/logs/autodj-".$dados_stm["porta"].".log\n\n";
	$config_autodj .= ";DADOS GERAIS\n";
	$config_autodj .= "uvoxstreamid=1\n";
	$config_autodj .= "serverip=localhost\n";
	$config_autodj .= "serverport=".$dados_stm["porta"]."\n";
	$config_autodj .= "uvoxauth=".$dados_stm["senha"]."\n";
	$config_autodj .= "streamtitle=".$dados_stm["streamtitle"]."\n";
	$config_autodj .= "streamurl=".$dados_stm["streamurl"]."\n";
	$config_autodj .= "genre=".$dados_stm["genre"]."\n";
	$config_autodj .= "bitrate=".$bitrate."000\n";
	$config_autodj .= "encoder=".$dados_stm["encoder"]."\n";
	$config_autodj .= "samplerate=".$dados_stm["autodj_samplerate"]."\n";
	$config_autodj .= "channels=".$dados_stm["autodj_channels"]."\n";
	$config_autodj .= "outprotocol=3\n";
	$config_autodj .= "shuffle=".$shuffle."\n";
	$config_autodj .= "xfade=".$xfade."\n";
	$config_autodj .= "xfadethreshold=20\n";
	$config_autodj .= "flashpolicyfile=/home/streaming/configs/crossdomain.xml\n\n";
	$config_autodj .= ";DJ PORT CONFIG\n";
	$config_autodj .= "calendarfile=/home/streaming/configs/calendar-".$dados_stm["porta"].".xml\n";
	$config_autodj .= "calendarrewrite=0\n";
	$config_autodj .= "djport=".$dados_stm["porta_dj"]."\n";
	
	$qtd_djs = 1;
	
	if($djs_config_autodj) {

	foreach($djs_config_autodj as $dj_config) {
	
	list($dj, $senha) = explode("|", $dj_config);
	
	$config_autodj .= "djlogin_".$qtd_djs."=".$dj."\n";
	$config_autodj .= "djpassword_".$qtd_djs."=".$senha."\n";
	
	$qtd_djs++;
	}
	
	}
	
	$config_autodj .= "\n;LICENCA MP3\n";
	$config_autodj .= "unlockkeyname=Advance Host\n";
	$config_autodj .= "unlockkeycode=F1ZA7-UXBW0-78TVK-Q02PB\n";
	
	}
	
	$config_autodj_calendar = array ("porta" => $dados_stm["porta"], "djs" => $djs_config_autodj_calendar, "relay" => $relay_config_calendar);

	$config_autodj = gerar_conf_autodj($dados_stm["porta"],$config_autodj);					  
	$config_calendar = gerar_calendar_autodj($config_autodj_calendar);
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_autodj."","/home/streaming/configs/".$config_autodj."",0777);
	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_calendar."","/home/streaming/configs/".$config_calendar."",0777);
	
	unlink("/home/painel/public_html/temp/".$config_autodj."");
	unlink("/home/painel/public_html/temp/".$config_calendar."");
	
	$resultado = $ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls");
	
	$resultado = $ssh->executar("/home/streaming/ligar_autodj /home/streaming/configs/".$config_autodj."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if(is_numeric($resultado) || $resultado == "ok") {
	
	// Atualiza a última playlist tocada e o bitrate do autodj
	mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."', bitrate_autodj = '".$bitrate."', xfade = '".$xfade."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>AutoDJ ".$dados_stm["porta"]." ligado com sucesso.</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] AutoDJ ligado com sucesso com a playlist ".$dados_playlist["nome"]."");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível ligar o AutoDJ ".$dados_stm["porta"]."<br>Tente novamente ou crie uma nova playlist.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível ligar o AutoDJ, possível erro na playlist.");
	
	}
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível ligar o AutoDJ ".$dados_stm["porta"]."<br>Log: O streaming ".$dados_stm["porta"]." esta desligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível ligar o AutoDJ, o streaming esta desligado.");
	
	}
	
	exit();
}

// Função para pular música no autodj
if($acao == "pular_musica") {


	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica se o autodj esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	$resultado = $ssh->executar("/home/streaming/gerenciar_autodj pular_musica ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	echo "<span class='texto_status_sucesso'>Música atual do AutoDJ ".$dados_stm["porta"]." pulada com sucesso.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Música atual do AutoDJ pulada com sucesso.");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$resultado."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível pular a música atual do AutoDJ, erro: ".$resultado."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível pular a música atual do AutoDJ ".$dados_stm["porta"]."<br>Log: O AutoDJ esta desligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível pular a música atual, o AutoDJ esta desligado");
	
	}
	
	exit();
}

// Função para recarregar playlist no autodj
if($acao == "recarregar_playlist") {


	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica se o autodj esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	$resultado = $ssh->executar("/home/streaming/gerenciar_autodj recarregar_playlist ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	echo "<span class='texto_status_sucesso'>Playlist do AutoDJ ".$dados_stm["porta"]." recarregada com sucesso.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Playlist do AutoDJ recarregada com sucesso.");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$resultado."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível recarregar a playlist, erro: ".$resultado."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível recarregar a playlist do AutoDJ ".$dados_stm["porta"]."<br>Log: O AutoDJ esta desligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível recarregar a playlist, o AutoDJ esta desligado.");
	
	}
	
	exit();
}

// Função para desligar autodj
if($acao == "desligar_autodj") {


	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica se o autodj esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	$resultado = $ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	echo "<span class='texto_status_sucesso'>AutoDJ ".$dados_stm["porta"]." desligado com sucesso.</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] AutoDJ desligado com sucesso.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível desligar o AutoDJ ".$dados_stm["porta"]."<br>Log: O AutoDJ já esta desligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível desligar o AutoDJ, erro: ".$resultado."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível desligar o AutoDJ ".$dados_stm["porta"]."<br>Log: O AutoDJ já esta desligado.</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível desligar o AutoDJ, ele já esta desligado.");
	
	}
	
	exit();
}

// Função para remover um DJ do AutoDJ
if($acao == "remover_dj") {


	$codigo = code_decode(query_string('3'),"D");
	
	$dados_dj = mysql_fetch_array(mysql_query("SELECT * FROM djs where codigo = '".$codigo."'"));

	mysql_query("Delete From djs where codigo = '".$dados_dj["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>DJ <strong>".$dados_dj["login"]."</strong> removido com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover o DJ <strong>".$dados_dj["login"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para diagnosticar erros no AutoDJ
if($acao == "diagnosticar_autodj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica os logs do AutoDJ a procura de erros	
	$checagem_erro1 = $ssh->executar("grep 'Error opening port' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro2 = $ssh->executar("grep 'Playlist has run dry' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro3 = $ssh->executar("grep 'Playlist is empty' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro4 = $ssh->executar("grep 'deactivating playlist' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro5 = $ssh->executar("grep 'Bind error' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	if($checagem_erro1 > 0 || $checagem_erro5 > 0) {
	
	$porta_dj = $dados_stm["porta_dj"];
	$porta_dj_listen = $dados_stm["porta_dj"]+1;
	
	$ssh->executar("lsof -i :".$porta_dj." | grep ':".$porta_dj."->' | awk {'print $2'} | xargs kill -9;echo ok");
	$ssh->executar("netstat -anp | grep :".$porta_dj_listen." | awk {'print $7'} | cut -d / -f 1 | xargs kill -9;echo ok");
	
	die("".lang_acao_diagnosticar_autodj_resultado_erro_porta."<br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>");

	} elseif($checagem_erro2 > 0 || $checagem_erro3 > 0 || $checagem_erro4 > 0) {
	
	die("".lang_acao_diagnosticar_autodj_resultado_erro_playlist."<br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>");
	
	} else {
	
	die("<span class='texto_status_sucesso'>".lang_acao_diagnosticar_autodj_resultado_sem_erros."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".lang_botao_titulo_fechar."]</a>");
	
	}
	
}

////////////////////////////////////////////////////////
//////////// Funções Gerenciamento Revenda /////////////
////////////////////////////////////////////////////////

// Função para bloquear revenda
if($acao == "bloquear_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");


	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {

	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$codigo."'"));
	
	// Desliga os streamings da revenda
	$query_stms = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."'");
	while ($dados_stm = mysql_fetch_array($query_stms)) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_stm["codigo"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	
	mysql_query("Update streamings set status = '2' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." bloqueado com sucesso.</span><br />";
	
	}
	
	mysql_query("Update revendas set status = '2' where codigo = '".$dados_revenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'><br />Revenda <strong>".$dados_revenda["nome"]."</strong> bloqueada com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_revenda["nome"]." - ".$dados_revenda["email"]."] Revenda bloqueada com sucesso");
	
	}
	
	exit();
}

// Função para desbloquear revenda
if($acao == "desbloquear_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");


	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$codigo."'"));
	
	// Desbloqueia os streamings da revenda
	$query_stms = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."'");
	while ($dados_stm = mysql_fetch_array($query_stms)) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_stm["codigo"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$porta = $dados_stm["porta"];
	$porta_ouvinte = $dados_stm["porta"]+1;
	$porta_dj = $dados_stm["porta_dj"];
	
	// Bloqueia o streaming no servidor
	$ssh->executar("iptables-save > /tmp/iptables.rules;sed -i '/".$porta."/d' /tmp/iptables.rules;sed -i '/".$porta_ouvinte."/d' /tmp/iptables.rules;iptables-restore < /tmp/iptables.rules;service iptables save;rm -fv /tmp/iptables.rules");
	
	mysql_query("Update streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." desbloqueado com sucesso.</span><br />";
	}
	
	mysql_query("Update revendas set status = '1' where codigo = '".$dados_revenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'><br />Revenda <strong>".$dados_revenda["nome"]."</strong> desbloqueada com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_revenda["nome"]." - ".$dados_revenda["email"]."] Revenda desbloqueada com sucesso");
	
	}
	
	exit();
}

// Função para remover revenda
if($acao == "remover_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");



	$codigo = code_decode(query_string('3'),"D");
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando.</span>";
	
	} else {
	
	$checar_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas where codigo = '".$codigo."'"));
	
	if($checar_revenda == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Revenda não encontrada.</span>";
	
	exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$codigo."'"));
	
	// Remove os streamings da revenda
	$query_stms = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."'");
	while ($dados_stm = mysql_fetch_array($query_stms)) {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_stm["codigo"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>Não foi possível executar ação, servidor em manutenção.</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	// Desliga o autodj caso esteja ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	}

	$resultado = $ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
		
	$resultado = str_replace("\n","",$resultado);
	
	} else {
	$resultado = "ok";
	}
	
	if($resultado == "ok") {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "desligado") {
	
	// Desbloqueia o streaming no servidor
	$porta = $dados_stm["porta"];
	$porta_ouvinte = $dados_stm["porta"]+1;
	$porta_dj = $dados_stm["porta_dj"];
	
	$ssh->executar("iptables-save > /tmp/iptables.rules;sed -i '/".$porta."/d' /tmp/iptables.rules;sed -i '/".$porta_ouvinte."/d' /tmp/iptables.rules;iptables-restore < /tmp/iptables.rules;service iptables save;rm -f /tmp/iptables.rules");
	
	$ssh->executar("nohup rm -rf /home/streaming/".$dados_stm["porta"]." /home/streaming/configs/*".$dados_stm["porta"]."* /home/streaming/logs/*".$dados_stm["porta"]."* /home/streaming/playlists/".$dados_stm["porta"]."-*.pls; echo ok");
	
	mysql_query("Delete From streamings where codigo = '".$dados_stm["codigo"]."'");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	mysql_query("Delete From playlists where codigo = '".$dados_playlist["codigo"]."'");
	
	$query_playlists_musicas = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
	while ($dados_playlist_musicas = mysql_fetch_array($query_playlists_musicas)) {
	mysql_query("Delete From playlists_musicas where codigo = '".$dados_playlist_musicas["codigo"]."'");
	}
	
	}
	
	// Remove os DJs
	mysql_query("Delete From djs where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove os Agendamentos
	mysql_query("Delete From playlists_agendamentos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove solicitações de app
	mysql_query("Delete From apps where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove logs
	mysql_query("Delete From logs_streamings where codigo_stm = '".$dados_stm["codigo"]."'");
	mysql_query("Delete From logs_migracoes where codigo_stm = '".$dados_stm["codigo"]."'");
	mysql_query("Delete From dicas_rapidas_acessos where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Remove migração de musicas
	mysql_query("Delete FROM migracoes where codigo_stm = '".$dados_stm["codigo"]."'");
	
	// Desativa o relay no servidor RTMP	
	if($dados_stm["aacplus"] == 'sim') {
	
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

	$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$dados_stm["porta"]."");
	
	}
	
	echo "<span class='texto_status_sucesso'>Streaming ".$dados_stm["porta"]." removido com sucesso.</span><br />";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Streaming removido com sucesso na remoção da revenda ".$dados_revenda["nome"]." - ".$dados_revenda["email"]."");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover o streaming ".$dados_stm["porta"]." Log: Erro desconhecido.</span><br />";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível remover o streaming na remoção da revenda ".$dados_revenda["nome"]." - ".$dados_revenda["email"].", erro desconhecido.");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover o streaming ".$dados_stm["porta"]." Log: ".$resultado."</span><br />";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Não foi possível remover o streaming na remoção da revenda ".$dados_revenda["nome"]." - ".$dados_revenda["email"].", erro: ".$resultado.".");
	
	}
	
	}
	
	mysql_query("Delete From revendas where codigo = '".$dados_revenda["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>Revenda <strong>".$dados_revenda["nome"]."</strong> removida com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_revenda["nome"]." - ".$dados_revenda["email"]."] Revenda removida com sucesso.");
	
	}
	
	exit();
}

// Função para alterar senha de uma revenda
if($acao == "alterar_senha_revenda") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");



	$codigo = code_decode(query_string('3'),"D");
	$nova_senha = query_string('4');
	
	if($codigo == "" || $codigo == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando.</span>";
	
	} else {
	
	$checar_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas where codigo = '".$codigo."'"));
	
	if($checar_revenda == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Revenda não encontrada.</span>";
	
	exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$codigo."'"));
	
	mysql_query("Update revendas set senha = PASSWORD('".$nova_senha."') where codigo = '".$dados_revenda["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Senha da revenda <strong>".$dados_revenda["nome"]."</strong> alterada com sucesso para ".$nova_senha."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
		// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_revenda["nome"]." - ".$dados_revenda["email"]."] Senha da revenda alterada com sucesso.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível alterar a senha da revenda <strong>".$dados_revenda["nome"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	
	}
	
	exit();

}

// Função para carregar a lista de players
if($acao == "carregar_players") {
	

	
	$porta = query_string('3');

	echo '<div id="quadro">
<div id="quadro-topo"><strong>Gerenciamento de Players</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,\'conteudo\');">
		    <option value="/admin/revenda-gerenciar-player-flash/'.$porta.'">Escolha um</option>
            <option value="/admin/revenda-gerenciar-player-flash/'.$porta.'">Flash Simples</option>
            <option value="/admin/revenda-gerenciar-player-topo/'.$porta.'">Topo/Barra</option>
            <option value="/admin/revenda-gerenciar-player-computador/'.$porta.'">Windows & Linux</option>
            <option value="/admin/revenda-gerenciar-player-celulares/'.$porta.'">Android & iOS</option>
            <option value="/admin/revenda-gerenciar-player-facebook/'.$porta.'">FaceBook</option>
			<option value="/admin/revenda-gerenciar-player-twitter/'.$porta.'">Twitter</option>
			<option value="/admin/revenda-app-android">App Android</option>
            <option value="/admin/revenda-gerenciar-player-popup/'.$porta.'">Pop-up</option>
         </select>
         </td>
      </tr>
    </table>
  </div>
</div>';

	exit();
	
}

// Função para alterar senha de um streaming
if($acao == "alterar_senha_streaming") {

	// Proteção Administrador
	require_once("inc/protecao-admin.php");



	$porta = code_decode(query_string('3'),"D");
	$tipo = query_string('4');
	$nova_senha = query_string('5');
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	if($tipo == "dj") {
	mysql_query("Update streamings set senha = '".$nova_senha."' where codigo = '".$dados_stm["codigo"]."'");
	} else {
	mysql_query("Update streamings set senha_admin = '".$nova_senha."' where codigo = '".$dados_stm["codigo"]."'");
	}
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Senha do streaming <strong>".$dados_stm["porta"]."</strong> alterada com sucesso para: ".$nova_senha."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Alterada senha do streaming.");
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível alterar a senha do streaming <strong>".$dados_stm["porta"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	
	}
	
	exit();

}

// Função para buscar streaming no painel de revenda
if($acao == "buscar_streaming_revenda") {
	
	// Proteção Administrador/Revenda
	if(empty($_SESSION["type_logged_user"])) {
	die("<span class='texto_status_erro'>0x004 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");	
	}


	
	echo code_decode(query_string('3'),"E");
	
	exit();

}

// Função para mover um streaming para a revenda principal
if($acao == "mover_streaming_subrevenda_revenda") {



	$porta = code_decode(query_string('3'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
	
	$verifica_subrevenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND codigo = '".$dados_stm["codigo_cliente"]."') AND tipo = '2'"));
	
	// Verifica se o streaming é de uma sub revenda que pertence a revenda logada
	if($verifica_subrevenda > 0) {
	
	mysql_query("Update streamings set codigo_cliente = '".$dados_revenda["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_subrevenda_streamings_mover_streaming_resultado_ok."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao("[".$dados_stm["porta"]."] Movido para revenda principal ".$dados_revenda["id"]."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".lang_info_subrevenda_streamings_mover_streaming_resultado_alerta."</span>";
	
	}
	
	
	}
	
	exit();

}

////////////////////////////////////////////////////////
//////////// Funções Gerenciamento Servidor ////////////
////////////////////////////////////////////////////////

// Função para listar os streamings do servidor
if($acao == "listar_streamings_servidor") {

	
	$codigo_servidor = code_decode(query_string('3'),"D");
	
	$sql = mysql_query("SELECT * FROM streamings where codigo_servidor = '".$codigo_servidor."' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$streamings .= "".code_decode($dados_stm["porta"],"E")."|";
	
	}
	
	echo substr($streamings,0,-1);

	exit();
	
}

// Função para ligar todos os streamings em todos os servidores
if($acao == "listar_streamings_geral") {

	
	$sql = mysql_query("SELECT * FROM streamings where status = '1' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "on") {
	$streamings .= "".code_decode($dados_stm["porta"],"E")."|";
	}
	
	}
	
	echo substr($streamings,0,-1);

	exit();
	
}

// Função para listar os streamings do servidor
if($acao == "listar_streamings_autodj_servidor") {

	
	$codigo_servidor = code_decode(query_string('3'),"D");
	
	$sql = mysql_query("SELECT * FROM streamings where codigo_servidor = '".$codigo_servidor."' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$bitrate = ($dados_stm["bitrate_autodj"] != "") ? $dados_stm["bitrate_autodj"] : $dados_stm["bitrate"];
	
	$streamings .= "".code_decode($dados_stm["porta"],"E").",".$dados_stm["ultima_playlist"].",".$dados_stm["shuffle"].",".$bitrate.",".$dados_stm["xfade"]."|";
	
	}
	
	echo substr($streamings,0,-1);

	exit();
	
}

// Função para ligar todos os autodjs em todos os servidores
if($acao == "listar_streamings_autodj_geral") {

	
	$sql = mysql_query("SELECT * FROM streamings where status = '1' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "on") {
	
	$bitrate = ($dados_stm["bitrate_autodj"] != "") ? $dados_stm["bitrate_autodj"] : $dados_stm["bitrate"];
	
	$streamings .= "".code_decode($dados_stm["porta"],"E").",".$dados_stm["ultima_playlist"].",".$dados_stm["shuffle"].",".$bitrate.",".$dados_stm["xfade"]."|";
	
	}
	
	}
	
	echo substr($streamings,0,-1);

	exit();
	
}

// Função para sincronizar streaming no servidor AAC+
if($acao == "sincronizar_aacplus_servidor") {


	$codigo_servidor = code_decode(query_string('3'),"D");
	
	if($codigo_servidor == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$sql = mysql_query("SELECT * FROM streamings where (codigo_servidor = '".$codigo_servidor."' || codigo_servidor_aacplus = '".$codigo_servidor."') AND aacplus = 'sim' ORDER by porta ASC");
	
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$streamings .= "".code_decode($dados_stm["porta"],"E")."|";
	
	}
	
	echo substr($streamings,0,-1);
	
	}
	
	exit();
}

// Função para sincronizar as playlists dos streamings no servidor
if($acao == "sincronizar_playlists_servidor") {
	

	$codigo_servidor = code_decode(query_string('3'),"D");
	
	if($codigo_servidor == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$codigo_servidor."'"));
	
	$sql = mysql_query("SELECT * FROM streamings where codigo_servidor = '".$codigo_servidor."' ORDER by porta ASC");
	while ($dados_stm = mysql_fetch_array($sql)) {
	
	$sql_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($sql_playlists)) {
		
	$sql_playlist_musicas = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
	while ($dados_musica = mysql_fetch_array($sql_playlist_musicas)) {
	
	// Adiciona a música na lista para adicionar ao arquivo da playlist
	$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$dados_musica["path_musica"]."\n";
	
	}
	
	// Cria o arquivo da playlist para enviar ao servidor do streaming
	$config_playlist = gerar_playlist($dados_playlist["arquivo"],$lista_musicas);
	
	unset($lista_musicas);
	
	// Envia o arquivo da playlist para o servidor do streaming
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_playlist."","/home/streaming/playlists/".$dados_playlist["arquivo"]."",0777);

	// Remove o arquivo temporário usado para criar a playlist
	unlink("/home/painel/public_html/temp/".$config_playlist."");
	
	}
	
	}	
	
	echo "<span class='texto_status_sucesso'>Playlists do servidor ".$dados_servidor["nome"]." sincronizadas com sucesso.</span>";
	
	}
	
	exit();
}

// Função para ativar/desativar manutenção em um servidor
if($acao == "manutencao_servidor") {


	$codigo_servidor = code_decode(query_string('3'),"D");
	$acao = query_string('4');
	$mensagem = query_string('5');
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$codigo_servidor."'"));

	if($acao == "ativar") {
	mysql_query("Update servidores set status = 'off', mensagem_manutencao = '".$mensagem."' where codigo = '".$dados_servidor["codigo"]."'");
	} else {
	mysql_query("Update servidores set status = 'on', mensagem_manutencao = '' where codigo = '".$dados_servidor["codigo"]."'");
	}
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Manutenção ativada/desativada no servidor <strong>".$dados_servidor["nome"]."</strong> com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível ativar/desativar a manutenção no servidor <strong>".$dados_servidor["nome"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

////////////////////////////////////////////////////////
//////////////////// Funções Gerais ////////////////////
////////////////////////////////////////////////////////

// Função para gerar qr code do link do app no google play
if($acao == "gerar_qr_code_app") {


	$package = query_string('3');

	if($package == "") {
	
	echo "<span class='texto_status_erro'>Atenção! Erro ao executar ação, dados faltando</span>";
	
	} else {
	
	echo '<img src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=market://details?id='.$package.'" width="150" height="150" />';
	echo '<br><br>';
	echo '<textarea name="textarea" readonly="readonly" style="width:99%; height:40px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=market://details?id='.$package.'" width="200" height="200" /></textarea>';
	
	}
	
	exit();
	
}

// Função para remover uma dica rápida
if($acao == "remover_dica_rapida") {


	$codigo = code_decode(query_string('3'),"D");
	
	$dados_dica_rapida = mysql_fetch_array(mysql_query("SELECT * FROM dicas_rapidas where codigo = '".$codigo."'"));

	mysql_query("Delete From dicas_rapidas where codigo = '".$dados_dica_rapida["codigo"]."'");
	mysql_query("Delete From dicas_rapidas_acessos where codigo_dica = '".$dados_dica_rapida["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Dica rápida <strong>".$dados_dica_rapida["titulo"]."</strong> removida com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover a dica rápida <strong>".$dados_dica_rapida["titulo"]."</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para exibir avisos
if($acao == "exibir_aviso") {


	$codigo_aviso = query_string('3');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM avisos where codigo = '".$codigo_aviso."'"));
	
	$area = ($_SESSION["porta_logada"]) ? 'streaming' : 'revenda';
	$codigo_usuario = ($area == "streaming") ? $_SESSION["porta_logada"] : $_SESSION["code_user_logged"];

	if(!mysql_error()) {
	
	list($ano,$mes,$dia) = explode("-",$dados_aviso["data"]);
	
	echo "<div id=\"quadro\">
			<div id=\"quadro-topo\"><strong>Atenção!</strong></div>
				<div class=\"texto_padrao\" id=\"quadro-conteudo\">
				".$dados_aviso["mensagem"]."<br><br>
				<span class=\"texto_padrao_vermelho\">Aviso adicionado em ".$dia."/".$mes."/".$ano."</span><br>
				<span class=\"texto_padrao_pequeno\"><input type=\"checkbox\" onclick=\"desativar_exibicao_aviso('".$codigo_aviso."', '".$area."', '".$codigo_usuario."');\" style=\"vertical-align:middle;\" />&nbsp;Marque esta caixa para não exibir novamente este aviso em seu painel de controle.</span>
				</div>
		  </div>";
	
	}
	
	exit();

}

// Função para marcar um aviso como vizualizado
if($acao == "desativar_exibicao_aviso") {

	$codigo_aviso = query_string('3');
	$area = query_string('4');
	$codigo_usuario = query_string('5');
	
	mysql_query("INSERT INTO avisos_desativados (codigo_aviso,codigo_usuario,area,data) VALUES ('".$codigo_aviso."','".$codigo_usuario."','".$area."',NOW())");
	
	exit();

}


// Função para mudar o status de exibição de um aviso
if($acao == "alterar_status_aviso") {


	$codigo_aviso = code_decode(query_string('3'),"D");
	
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM avisos where codigo = '".$codigo_aviso."'"));
	
	$status = ($dados_aviso["status"] == "sim") ? 'nao' : 'sim';
	
	mysql_query("Update avisos set status = '".$status."' where codigo = '".$dados_aviso["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Status do aviso <strong>".$dados_aviso["titulo"]."</strong> alterado com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível alterar o status do aviso <strong>".$dados_aviso["titulo"]."</strong>.</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover um aviso
if($acao == "remover_aviso") {


	$codigo_aviso = code_decode(query_string('3'),"D");
	
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM avisos where codigo = '".$codigo_aviso."'"));
	
	mysql_query("Delete From avisos where codigo = '".$dados_aviso["codigo"]."'");
	mysql_query("Delete From avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>Aviso <strong>".$dados_aviso["titulo"]."</strong> removido com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover o aviso <strong>".$dados_aviso["titulo"]."</strong>.</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover uma requisição de app android
if($acao == "remover_app_android") {


	$codigo_app = code_decode(query_string('3'),"D");
	
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".$codigo_app."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));
	
	mysql_query("Delete From apps where codigo = '".$dados_app["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Remove o apk e imagens
	@unlink("../app_android/apps/".$dados_app["zip"]."");
	@unlink("../".$dados_app["print"]."");
	
	echo "<span class='texto_status_sucesso'>Requisição do streaming <strong>".$dados_stm["porta"]."</strong> removido com sucesso.</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[Atualizar]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>Não foi possível remover a requisição do streaming <strong>".$dados_stm["porta"]."</strong>.</strong><br>Log: ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para desbloquear IP bloqueado no login
if($acao == "desbloquear_ip_login") {


	$codigo = code_decode(query_string('3'),"D");
	
	mysql_query("Delete From bloqueios_login where codigo = '".$codigo."'");
	
	echo "<span class='texto_status_sucesso'>".lang_info_ips_bloqueados_resultado_ok."</span><br /><br/><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[Fechar]</a>";
	
	exit();

}
?>