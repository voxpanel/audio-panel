<?php
ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclus&atilde;o de classes
require_once('inc/classe.ssh.php');

/*
chave -> query_string('2');
acao -> query_string('3');
porta -> query_string('4');
*/

$chave_api = query_string('2');
$acao = query_string('3');

// Verifica se a chave da api foi informada
if($chave_api == "") {
echo "0||Chave da API vazia.";
exit();
}

// Verifica se a chave da api esta configurada
$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));

if($valida_revenda == 0) {
echo "0||Chave da API inv&aacute;lida.";
exit();
}

// Função para cadastrar streaming
if($acao == "cadastrar") {
	
	$ouvintes = query_string('4');
	$bitrate = query_string('5');
	$espaco = query_string('6');
	$senha = query_string('7');
	$aacplus = query_string('8');
	$idioma = (query_string('9')) ? query_string('9') : 'pt-br';
	$app_android = (query_string('10')) ? query_string('10') : 'sim';
	$encoder_mp3 = (query_string('11') != "on" && query_string('11') != "sim") ? 'nao' : 'sim';
	$encoder_aacplus = (query_string('12') != "on" && query_string('12') != "sim") ? 'nao' : 'sim';
	$ativar_autodj = ($espaco > 0) ? 'sim' : 'nao';
	$identificacao = urldecode(query_string('13'));
	
	// Portas inválidas/bloqueadas
	$portas_invalidas = array("6000","6665","6666","6667","6668","6669","6984","6985");

	// Verifica a última gerada e gera a próxima
	$porta_livre_stm = false;
	$porta_livre_dj = false;

	$nova_porta_stm = 6670;
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
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));
	
	$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
	$total_streamings_subrevenda = mysql_fetch_array(mysql_query("SELECT SUM(streamings) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
	$total_streamings_revenda = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
	$ouvintes_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
	$ouvintes_stm_revenda = mysql_fetch_array(mysql_query("SELECT SUM(ouvintes) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));
	$espaco_subrevenda_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));
	$espaco_revenda = mysql_fetch_array(mysql_query("SELECT SUM(espaco) as total FROM streamings WHERE codigo_cliente = '".$dados_revenda["codigo"]."'"));


	// Verifica se excedeu o limite de streamings do cliente
	$total_streamings_revenda = $total_streamings_revenda+1;

	if($total_streamings_revenda > $dados_revenda["streamings"]) {
		echo "0||Limite de streamings atingido.";
		exit();
	}

	// Verifica se excedeu o limite de ouvintes do cliente
	$total_ouvintes_revenda = $ouvintes_revenda["total"]+$ouvintes_subrevenda_revenda["total"]+$ouvintes;

	if($total_ouvintes_revenda > $dados_revenda["ouvintes"] && $dados_revenda["ouvintes"] != 999999) {
		echo "0||Limite de ouvintes atingido.";
		exit();
	}

	// Verifica se excedeu o limite de ouvintes do cliente
	$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$espaco;

	if($total_espaco_revenda > $dados_revenda["espaco"]) {
		echo "0||Limite de espa&ccedil;o para autodj atingido.";
		exit();
	}

	// Verifica se excedeu o limite de bitrate do cliente
	if($bitrate > $dados_revenda["bitrate"]) {
		echo "0||Limite de bitrate atingido.";
		exit();
	}
	
	// Carrega as configura&ccedil;ões do sistema
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_config["codigo_servidor_atual"]."'"));
	
	if($dados_revenda["aacplus"] == 'sim' && $aacplus == 'sim') {
	
	$servidor_aacplus = $dados_config["codigo_servidor_aacplus_atual"];
	$encoder = "aacp";
	$aacplus = "sim";
	
	// Ativa o relay no servidor aacplus
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_config["codigo_servidor_aacplus_atual"]."'"));

	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

	$ssh->executar("/usr/local/WowzaMediaServer/ativar-aacplus ".$porta." ".$dados_servidor["ip"]." ".$ouvintes."");
	
	} else {
	
	$servidor_aacplus = 0;
	$encoder = "mp3";
	$aacplus = "nao";
	
	}
	
	$senha_admin = gera_id(12);
	
	mysql_query("INSERT INTO streamings (codigo_cliente,codigo_servidor,codigo_servidor_aacplus,porta,porta_dj,ouvintes,bitrate,bitrate_autodj,encoder_mp3,encoder_aacplus,encoder,espaco,senha,senha_admin,ftp_dir,aacplus,idioma_painel,exibir_app_android,autodj,data_cadastro,hora_cadastro,local_cadastro,ip_cadastro,identificacao,publicserver) VALUES ('".$dados_revenda["codigo"]."','".$dados_config["codigo_servidor_atual"]."','".$servidor_aacplus."','".$porta."','".$porta_dj."','".$ouvintes."','".$bitrate."','".$bitrate."','".$encoder_mp3."','".$encoder_aacplus."','".$encoder."','".$espaco."','".$senha."','".$senha_admin."','/home/streaming/".$porta."','".$aacplus."','".$idioma."','".$app_android."','".$ativar_autodj."',NOW(),NOW(),'api-revenda','".$_SERVER['REMOTE_ADDR']."','".$identificacao."','never')");
	
	if(!mysql_error()) {
	
		$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_config["codigo_servidor_atual"]."'"));
		
		$dominio_servidor = ($dados_revenda["dominio_padrao"]) ? strtolower($dados_servidor["nome"]).".".$dados_revenda["dominio_padrao"] : strtolower($dados_servidor["nome"]).".".$dados_config["dominio_padrao"];
		
		// Loga a ação executada
		mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('cadastro_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado streaming ".$porta." no servidor ".$dados_servidor["ip"]." pela revenda ".$dados_revenda["nome"]."')");
	
		echo "1|".$dominio_servidor.":".$porta."|Streaming cadastrado com sucesso.";	
	
	} else {
		echo "0||Erro ao executar query no mysql: ".mysql_error()."";
	}
	
	exit();
}

// Função para bloquear streaming
if($acao == "bloquear") {

	$porta = query_string('4');
	
	if(empty($porta)) {
		echo "0|".$dados_stm["porta"]."|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}

	// Conex&atilde;o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	//$porta = $dados_stm["porta"];
	//$porta_ouvinte = $dados_stm["porta"]+1;
	
	// Bloqueia o streaming no servidor
	//$ssh->executar("iptables -A INPUT -p tcp --dport ".$porta.":".$porta_ouvinte." -j DROP;service iptables save;echo ok");
	
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "desligado") {
	
		mysql_query("Update streamings set status = '3' where codigo = '".$dados_stm["codigo"]."'");
		
		// Loga a ação executada
		mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('bloquear_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Bloqueio do streaming ".$dados_stm["porta"]."')");
		
		// Loga data do bloqueio
		@mysql_query("Update streamings set data_bloqueio = NOW() WHERE codigo = '".$dados_stm["codigo"]."'");
	
		echo "1|".$dados_stm["porta"]."|Streaming bloqueado com sucesso.";
	
	} else {
	
		mysql_query("Update streamings status = '3' where codigo = '".$dados_stm["codigo"]."'");
	
		echo "0|".$dados_stm["porta"]."|Erro desconhecido.";
	}
	
	exit();
}

// Função para desbloquear streaming
if($acao == "desbloquear") {
	
	$porta = query_string('4');
	
	if(empty($porta)) {
		echo "0|".$dados_stm["porta"]."|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}
	
	if($dados_stm["status"] == 2) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}	
	
	// Conex&atilde;o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$porta = $dados_stm["porta"];
	$porta_ouvinte = $dados_stm["porta"]+1;
	$porta_dj = $dados_stm["porta_dj"];
	
	// Desbloqueia o streaming no servidor
	$ssh->executar("iptables -D INPUT -p tcp --dport ".$porta.":".$porta_ouvinte." -j DROP;service iptables save;echo ok");
	$ssh->executar("iptables -D INPUT -p tcp --dport ".$porta.":".$porta_ouvinte." -j DROP;service iptables save;echo ok");
	$ssh->executar("iptables -D INPUT -p tcp --dport ".$porta.":".$porta_ouvinte." -j DROP;service iptables save;echo ok");
	$ssh->executar("iptables -D INPUT -p tcp --dport ".$porta.":".$porta_ouvinte." -j DROP;service iptables save;echo ok");
	
	mysql_query("Update streamings set status = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('desbloquear_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Desbloqueio do streaming ".$dados_stm["porta"]."')");
	
	echo "1|".$dados_stm["porta"]."|Streaming desbloqueado com sucesso.";
	
	exit();
}

// Função para desbloquear streaming
if($acao == "alterar_senha") {
	
	$porta = query_string('4');
	$senha = query_string('5');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}
	
	if($dados_stm["status"] == 2) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}	
	
	mysql_query("Update streamings set senha = '".$senha."' where codigo = '".$dados_stm["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('alterar_senha_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração de senha do streaming ".$dados_stm["porta"]."')");
	
	echo "1|".$dados_stm["porta"]."|Senha alterada com sucesso.";
	
	} else {
	
	echo "0||Erro ao executar query no mysql: ".mysql_error()."";
	
	}
	
	exit();
}

// Função para remover streaming
if($acao == "remover") {
	
	$porta = query_string('4');
	
	if(empty($porta)) {
		echo "0|".$dados_stm["porta"]."|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}
	
	// Conex&atilde;o SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	// Desliga o autodj caso esteja ligado
	$status_autodj = $ssh->executar("nice --adjustment=-20 nmap -sT -p ".$dados_stm["porta_dj"]." localhost | grep open | wc -l");
	
	if($status_autodj == 1) {
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
	
	$ssh->executar("iptables -D INPUT -p tcp --dport ".$porta." -j DROP;iptables -D INPUT -p tcp --dport ".$porta_ouvinte." -j DROP;iptables -D INPUT -p tcp --dport ".$porta_dj." -j DROP;service iptables save;echo ok");
	
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
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('remover_streaming_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Remoção do streaming ".$dados_stm["porta"]." pela revenda ".$dados_revenda["nome"]."')");
	
	echo "1|".$dados_stm["porta"]."|Streaming removido com sucesso.";
	
	} else {
	
	echo "0|".$dados_stm["porta"]."|Erro desconhecido.";
	}
	
	} else {
	echo "0|".$dados_stm["porta"]."|".$resultado."";
	}
	
	exit();
}

// Função para cadastrar streaming
if($acao == "limite_bitrate") {
	
   	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));

	foreach(array("24","32","48","64","96","128") as $bitrate){
		   
		if($bitrate <= $dados_revenda["bitrate"]) {
		   
			$array_bitrate .= $bitrate.",";

		}
		    
	}
	
	echo substr($array_bitrate,0,-1);

   exit();
}

// Função para cadastrar streaming
if($acao == "status_streaming") {

	$porta = query_string('4');
	
	if(empty($porta)) {
		echo "0|Dados faltando.";
		exit();
	}
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}
	
	$status = ($dados_stm["status"] == '1') ? "ativo" : "bloqueado";
	
	echo "1|".$status."";

}

// Função para cadastrar sub revenda
if($acao == "cadastrar_subrevenda") {
	
	$streamings = query_string('4');
	$ouvintes = query_string('5');
	$bitrate = query_string('6');
	$espaco = query_string('7');
	$aacplus = query_string('8');
	$idioma_painel = query_string('9');
	$email_subrevenda = query_string('10');
	$senha = query_string('11');
	$subrevendas = query_string('12');

	if(empty($streamings) or empty($ouvintes) or empty($bitrate) or empty($espaco) or empty($senha) or empty($aacplus) or empty($idioma_painel) or empty($email_subrevenda)) {
		echo "0||Dados faltando.";
		exit();
	}
	
	// Verifica os limites da revenda
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));
	
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
		echo "0||Limite de sub revendas atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de streamings
	$total_streamings_revenda = $total_streamings_revenda+$total_streamings_subrevenda["total"]+$streamings;

	if($total_streamings_revenda > $dados_revenda["streamings"] && $dados_revenda["streamings"] != 999999) {
		echo "0||Limite de streamings atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de ouvintes
	$total_ouvintes_revenda = $ouvintes_revenda["total"]+$ouvintes_subrevenda_revenda["total"]+$ouvintes;
	
	if($total_ouvintes_revenda > $dados_revenda["ouvintes"] && $dados_revenda["ouvintes"] != 999999) {
		echo "0||Limite de ouvintes atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de espaco FTP
	$total_espaco_revenda = $espaco_revenda["total"]+$espaco_subrevenda_revenda["total"]+$espaco;
	
	if($total_espaco_revenda > $dados_revenda["espaco"]) {
		echo "0||Limite de espaco FTP atingido.";
		exit();
	}
	
	// Verifica se excedeu o limite de bitrate
	if($bitrate > $dados_revenda["bitrate"]) {
		echo "0||Limite de bitrate atingido.";
		exit();
	}
	
	$id = gera_id();
	
	// 1 - revenda | 2 - subrevenda da revenda | 3 - subrevenda da subrevenda
	$tipo = (empty($dados_revenda["codigo_revenda"])) ? 2 : 3;
	
	mysql_query("INSERT INTO revendas (codigo_revenda,id,nome,email,senha,subrevendas,streamings,ouvintes,bitrate,espaco,chave_api,aacplus,idioma_painel,tipo,data_cadastro) VALUES ('".$dados_revenda["codigo"]."','".$id."','".$dados_revenda["nome"]."','".$email_subrevenda."',PASSWORD('".$senha."'),'".$subrevendas."','".$streamings."','".$ouvintes."','".$bitrate."','".$espaco."','".code_decode($email_subrevenda,"E")."','".$aacplus."','".$idioma_painel."','".$tipo."',NOW())");
	
	if(!mysql_error()) {

		// Loga a ação executada
		mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('cadastro_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Cadastrado sub revenda ".$id." pela revenda ".$dados_revenda["nome"]."')");
	
		echo "1|".$id."|Sub revenda cadastrada com sucesso.";	
	
	} else {
		echo "0||Erro ao executar query no mysql: ".mysql_error()."";
	}
	
	exit();
	
}

// Função para bloquear sub revenda
if($acao == "bloquear_subrevenda") {
	
	$id = query_string('4');
	
	if(empty($id)) {
		echo "0||Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."')"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}
	
	// Bloqueia os streamings da revenda
	$query_stms = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm = mysql_fetch_array($query_stms)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "on") {	
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	
	}
	
	// Bloqueia o streaming da subreenda no painel
	mysql_query("Update streamings set status = '2' where codigo = '".$dados_stm["codigo"]."'");
	
	}
	
	// Bloqueia as subrevendas da subrevenda
	$query_subrevendas_sub = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND status = '1' AND tipo = '3' ORDER by codigo ASC");
	while ($dados_subrevenda_sub = mysql_fetch_array($query_subrevendas_sub)) {
	
	// Bloqueia os streamings da subrevenda
	$query_stms_subrevenda = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_subrevenda_sub["codigo"]."'");
	while ($dados_stm_subrevenda_sub = mysql_fetch_array($query_stms_subrevenda)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm_subrevenda_sub["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "on") {	
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	
	}
	
	// Bloqueia o streaming da subreenda no painel
	mysql_query("Update streamings set status = '2' where codigo = '".$dados_stm_subrevenda_sub["codigo"]."'");
	
	}

	// Bloqueia a subrevenda da subrevenda no painel
	mysql_query("Update revendas set status = '3' where codigo = '".$dados_subrevenda_sub["codigo"]."'");
	
	}	
	
	// Bloqueia a subrevenda no painel
	mysql_query("Update revendas set status = '3' where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "1|".$id."|Sub revenda bloqueada com sucesso.";
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('bloquear_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Bloqueio da sub revenda ".$id."')");
	
	exit();
	
}

// Função para desbloquear sub revenda
if($acao == "desbloquear_subrevenda") {
	
	$id = query_string('4');
	
	if(empty($id)) {
		echo "0||Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."')"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}

	// Desbloqueia os streamings da revenda
	mysql_query("Update streamings set status = '1' where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	
	// Desbloqueia as subrevendas da subrevenda no painel
	mysql_query("Update revendas set status = '3' where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND (status = '1' AND tipo = '3')");

	// Desbloqueia as subrevendas da subrevenda
	$query_subrevendas_sub = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND (status = '1' AND tipo = '3') ORDER by codigo ASC");
	while ($dados_subrevenda_sub = mysql_fetch_array($query_subrevendas_sub)) {
	
	// Desbloqueia o streaming da subreenda no painel
	mysql_query("Update streamings set status = '1' where codigo_cliente = '".$dados_stm_subrevenda_sub["codigo"]."'");
	}
	
	// Desbloqueia a subrevenda
	mysql_query("Update revendas set status = '1' where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "1|".$id."|Sub revenda desbloqueada com sucesso.";
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('desbloquear_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Desbloqueio da sub revenda ".$id."')");
	
	exit();

}

// Função para remover sub revenda
if($acao == "remover_subrevenda") {
	
	$id = query_string('4');
	
	if(empty($id)) {
		echo "0||Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."')"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}
	
	// Remove os streamings da revenda
	$query_stms = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'");
	while ($dados_stm = mysql_fetch_array($query_stms)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
		echo "0||Servidor em manutencao, tente mais tarde.";
		exit();
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm["porta"]."");
	$ssh->executar("nohup rm -rf /home/streaming/".$dados_stm["porta"]." /home/streaming/configs/*".$dados_stm["porta"]."* /home/streaming/logs/*".$dados_stm["porta"]."* /home/streaming/playlists/".$dados_stm["porta"]."-*.pls; echo ok");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	mysql_query("Delete From playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
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
	
	// Remove o streaming do painel
	mysql_query("Delete From streamings where codigo = '".$dados_stm["codigo"]."'");
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('remover_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Streaming ".$dados_stm["porta"]." removido com sucesso na remoção da sub revenda ".$id."')");
	
	}
	
	// Remove as subrevendas da subrevenda
	$query_subrevendas_sub = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' AND (status = '1' AND tipo = '3') ORDER by codigo ASC");
	while ($dados_subrevenda_sub = mysql_fetch_array($query_subrevendas_sub)) {
	
	// Remove os streamings da subrevenda da subrevenda
	$query_stms_sub = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_subrevenda_sub["codigo"]."'");
	while ($dados_stm_sub = mysql_fetch_array($query_stms_sub)) {
	
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm_sub["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".lang_alerta_manutencao_servidor."</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

	$ssh->executar("/home/streaming/desligar_autodj ".$dados_stm_sub["porta"]." ".$dados_stm_sub["porta_dj"]."");
	$ssh->executar("/home/streaming/desligar_streaming ".$dados_stm_sub["porta"]."");
	$ssh->executar("nohup rm -rf /home/streaming/".$dados_stm_sub["porta"]." /home/streaming/configs/*".$dados_stm_sub["porta"]."* /home/streaming/logs/*".$dados_stm_sub["porta"]."* /home/streaming/playlists/".$dados_stm_sub["porta"]."-*.pls; echo ok");
	
	// Remove as playlists
	$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	mysql_query("Delete From playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
	}
	
	// Remove os DJs
	mysql_query("Delete From djs where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	
	// Remove os Agendamentos
	mysql_query("Delete From playlists_agendamentos where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	
	// Remove solicitações de app
	mysql_query("Delete From apps where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	
	// Remove logs
	mysql_query("Delete From logs_streamings where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	mysql_query("Delete From logs_migracoes where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	mysql_query("Delete From dicas_rapidas_acessos where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	
	// Remove migração de musicas
	mysql_query("Delete FROM migracoes where codigo_stm = '".$dados_stm_sub["codigo"]."'");
	
	// Desativa o relay no servidor RTMP	
	if($dados_stm_sub["aacplus"] == 'sim') {
	
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm_sub["codigo_servidor_aacplus"]."'"));
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));

	$ssh->executar("/usr/local/WowzaMediaServer/desativar-aacplus ".$dados_stm_sub["porta"]."");
	
	}
	
	// Remove o streaming do painel
	mysql_query("Delete From streamings where codigo = '".$dados_stm_sub["codigo"]."'");
	
	// Insere a ação executada no registro de logs.
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('remover_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','[".$dados_stm_sub["porta"]."] Streaming removido com sucesso na remoção da sub revenda ".$dados_subrevenda_sub["id"]." - ".$dados_subrevenda_sub["email"]."')");
	
	}	
	
	// Remove a subrevenda da subrevenda no painel
	mysql_query("Delete From revendas where codigo = '".$dados_subrevenda_sub["codigo"]."'");
	}
	
	mysql_query("Delete From revendas where codigo = '".$dados_subrevenda["codigo"]."'");
		
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('remover_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Remoção da sub revenda ".$id."')");

	echo "1|".$id."|Sub revenda removida com sucesso.";
	
	exit();	
	
}

// Função para desbloquear sub revenda
if($acao == "alterar_senha_subrevenda") {
	
	$id = query_string('4');
	$senha = query_string('5');
	
	if(empty($id) or empty($senha)) {
		echo "0||Dados faltando.";
		exit();
	}
	
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."'"));
	$dados_subrevenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE (codigo_revenda = '".$dados_revenda["codigo"]."' AND id = '".$id."')"));	
	
	// Verifica se a chave da api informada é do cliente proprietario da sub revenda
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_subrevenda["codigo_revenda"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$id."|Permissao negada.";
		exit();
	}

	// Altera a senha da sub revenda
	mysql_query("Update revendas set senha = PASSWORD('".$senha."') where codigo = '".$dados_subrevenda["codigo"]."'");
	
	echo "1|".$id."|Senha alterada com sucesso.";
	
	// Loga a ação executada
	mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('alterar_senha_subrevenda_api',NOW(),'".$_SERVER['REMOTE_ADDR']."','Alteração de senha da sub revenda ".$id."')");
	
	exit();

}

// Função para bloquear streaming
if($acao == "chave_streaming") {

	$porta = query_string('4');
	
	if(empty($porta)) {
		echo "0|".$dados_stm["porta"]."|Dados faltando.";
		exit();
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	// Verifica se a chave da api informada é do cliente propriet&aacute;rio do streaming
	$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE chave_api = '".$chave_api."' AND codigo = '".$dados_stm["codigo_cliente"]."'"));
	
	if($valida_revenda == 0) {
		echo "0|".$dados_stm["porta"]."|Permissao negada.";
		exit();
	}
	
	echo code_decode($dados_stm["porta"],"E");	
	
}
?>