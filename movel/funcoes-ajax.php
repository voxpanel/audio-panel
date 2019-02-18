<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("../admin/inc/classe.ssh.php");
require_once("../admin/inc/classe.ftp.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('2');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar streaming
if($acao == "ligar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
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
	$config_streaming .= "flashpolicyfile=/home/streaming/configs/crossdomain.xml\n";
	$config_streaming .= "w3cenable=0\n";
	$config_streaming .= "logclients=0\n";
	$config_streaming .= "showlastsongs=".$dados_stm["showlastsongs"]."\n";
	
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
	
	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_streaming."","/home/streaming/configs/".$config_streaming."",0777);
	
	unlink("/home/painel/public_html/temp/".$config_streaming."");
	
	$resultado = $ssh->executar("/home/streaming/ligar_streaming /home/streaming/configs/".$config_streaming."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if(is_numeric($resultado)) {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='javascript:window.location.reload();document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_alerta'>".$lang['lang_acao_ligar_stm_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_alerta']."");
	
	}
	
	exit();
}

// Função para desligar streaming
if($acao == "desligar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_desligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='javascript:window.location.reload();document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_desligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_desligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_alerta'>".$lang['lang_acao_desligar_stm_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_alerta']."");
	
	}
	
	exit();
}

// Função para verificar o status do streaming e autodj
if($acao == "status_streaming") {

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$status_conexao = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	$status_conexao_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	$status_conexao_relay = status_relay($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"2\"><strong>".$lang['lang_info_status_manutencao']."</strong></font>";
	exit();
	}
	
	if($status_conexao_relay == "ligado") {
	echo "<font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_transmitindo']." (".$lang['lang_info_status_relay'].")</strong></font>";
	exit();
	}
	
	if($status_conexao_autodj == "ligado") {
	echo "<font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_transmitindo']." (".$lang['lang_info_status_autodj'].")</strong></font>";
	exit();
	}	
	
	if($status_conexao == "ligado") {
	echo "<font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_ligado']."</strong></font>";
	exit();
	}
	
	echo "<font color=\"#999999\" size=\"2\"><strong>".$lang['lang_info_status_desligado']."</strong></font>";
	
	exit();
	
}

// Função para desconectar o source do streaming(kick)
if($acao == "kick_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_kick_stm_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_kick_stm_resultado_ok']."");
	
	} else {	
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_kick_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_kick_stm_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_kick_stm_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_kick_stm_resultado_alerta']."");
	
	}
	
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
	$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($recurso == "ouvintes") {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
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
	
	if($dados_stm["aacplus"] == 'sim') {	
	$stats_aacplus = stats_ouvintes_aacplus($dados_stm["porta"],$dados_servidor_aacplus["ip"],$dados_servidor_aacplus["senha"]);
	$ouvintes_total_aacplus = $stats_aacplus["ouvintes"];
	
	$ouvintes_total_shoutcast = $ouvintes_total_shoutcast-1;
	}	

	$ouvintes_conectados = $ouvintes_total_shoutcast+$ouvintes_total_aacplus;
	
	$porcentagem_uso_ouvintes = ($dados_stm["ouvintes"] == 0) ? "0" : $ouvintes_conectados*100/$dados_stm["ouvintes"];	
	$porcentagem_uso_ouvintes = ($porcentagem_uso_ouvintes < 1 && $ouvintes_conectados > 0) ? "1" : $porcentagem_uso_ouvintes;
	
	$modo_texto = ($texto == "sim") ? '<span class="texto_padrao_pequeno">('.str_replace("-","",$ouvintes_conectados).' de '.$dados_stm["ouvintes"].')</span>' : '';
	
	echo barra_uso_plano(str_replace("-","",$porcentagem_uso_ouvintes),'('.str_replace("-","",$ouvintes_conectados).' de '.$dados_stm["ouvintes"].')').'&nbsp;'.$modo_texto;
	
	} else { // Streaming desligado	
	echo barra_uso_plano(0,'(0 de '.$dados_stm["ouvintes"].')').'&nbsp;'.$modo_texto;
	}
		
	} else { // -> Recurso FTP
	
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
	
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$host_sources = ($dados_config["usar_cdn"] == "sim") ? $dados_config["dominio_cdn"] : $_SERVER['HTTP_HOST'];
	
	if($dados_config["usar_cdn"] == "sim") {
	
	$musica = file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&recurso=musica");
	
	} else {
	
	$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
	
	$musica = $info["musica"];
	
	}
	
	if(strlen($musica) > $limite_caracteres) {
	echo substr($musica, 0, $limite_caracteres)."...";
	} else {
	echo $musica;
	}
	
	exit();
	
}

////////////////////////////////////////////////////////
///////////// Funções Gerenciamento AutoDJ /////////////
////////////////////////////////////////////////////////

// Função para carregar as playlists do streaming
if($acao == "carregar_playlists") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se o relay esta ativado
	if($dados_stm["relay"] == "sim") {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_autodj_resultado_relay']."</span>";
	
	exit();
	}
	
	// Verifica se o autodj já esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	echo "<span class='texto_status_alerta'>0x001 ".$lang['lang_acao_ligar_autodj_resultado_alerta']."</span>";
	
	exit();
	}
	
	$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
	$dados_ultima_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
	$total_musicas_ultima_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_stm["ultima_playlist"]."'"));
	
	if($total_playlists > 0) {
	
	echo '<span style="color: #FFFFFF;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:20px;font-weight:bold;">'.$lang['lang_info_autodj_ligar'].'</span><br />';
	echo '<span style="color: #FFFFFF;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;">('.$lang['lang_info_autodj_ligar_opcoes'].')</span><br /><br />';
	echo '<select name="playlist" id="playlist" style="width:200px;">';
	echo '<optgroup label="'.$lang['lang_info_autodj_ultima_playlist'].'">';
	if($dados_stm["ultima_playlist"] > 0) {
	echo '<option value="'.$dados_stm["ultima_playlist"].'">'.$dados_ultima_playlist["nome"].' ('.$total_musicas_ultima_playlist.')</option>';
	}
	echo '</optgroup>';
	echo '<optgroup label="'.$lang['lang_info_autodj_playlists'].'">';
	
	$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
	while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
	$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'"));

	echo '<option value="'.$dados_playlist["codigo"].'">'.$dados_playlist["nome"].' ('.$total_musicas.')</option>';
		
	}
	
	echo '</optgroup>';
	echo '</select>';
	echo '<br />';
	echo '<select name="shuffle" id="shuffle" style="width:130px;">';
	echo '<optgroup label="'.$lang['lang_info_autodj_shuffle'].'">';
	echo '<option value="0" selected="selected">'.$lang['lang_info_autodj_shuffle_opcao_seguir_ordem'].'</option>';
	echo '<option value="1">'.$lang['lang_info_autodj_shuffle_opcao_misturar'].'</option>';
	echo '</optgroup>';
	echo '</select>';
	echo '<select name="xfade" id="xfade" style="width:85px;">';
	echo '<optgroup label="'.$lang['lang_info_autodj_xfade'].'">';
	
	foreach(array("0" => "(".$lang['lang_info_autodj_xfade_0'].")","2" => "".$lang['lang_info_autodj_xfade_2']."","4" => "".$lang['lang_info_autodj_xfade_4']."","6" => "".$lang['lang_info_autodj_xfade_6']."","8" => "".$lang['lang_info_autodj_xfade_8']."","10" => "".$lang['lang_info_autodj_xfade_10']."") as $xfade => $xfade_descricao){
	
		if($xfade == $dados_stm["xfade"]) {
			echo '<option value="'.$xfade.'" selected="selected">'.$xfade_descricao.'</option>';
		} else {
			echo '<option value="'.$xfade.'">'.$xfade_descricao.'</option>';
		}
	
	}
	
	echo '</optgroup>';
	echo '</select>';
	echo '<select name="bitrate" id="bitrate" style="width:85px;">';
	echo '<optgroup label="'.$lang['lang_info_autodj_bitrate'].'">';
	
	foreach(array("24","32","48","64","96","128") as $bitrate){
	
		if($bitrate <= $dados_stm["bitrate"]) {
			
			if($dados_stm["bitrate_autodj"]) {
		
			if($bitrate == $dados_stm["bitrate_autodj"]) {
				echo '<option value="'.$bitrate.'" selected="selected">'.$bitrate.' Kbps('.$lang['lang_info_autodj_bitrate_ultimo_usado'].')</option>';
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
	echo '<input type="button" class="botao" value="'.$lang['lang_botao_titulo_ligar'].'" onclick="ligar_autodj(\''.code_decode($dados_stm["porta"],"E").'\',document.getElementById(\'playlist\').value,document.getElementById(\'shuffle\').value,document.getElementById(\'bitrate\').value,document.getElementById(\'xfade\').value);" />';
	
	} else {
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_info_autodj_sem_playlist']."</span>";
	}
	
	exit();

}

// Função para ligar autodj
if($acao == "ligar_autodj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
	$playlist = query_string('4');
	$shuffle = query_string('5');
	$bitrate = query_string('6');
	$xfade = query_string('7');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	// Verifica se o autodj já esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	echo "<span class='texto_status_alerta'>0x002 ".$lang['lang_acao_ligar_autodj_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_alerta']."");

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
	$multipoint .= "channels_".$dados_ponto["id"]."=".$canal."\n";
	$multipoint .= "samplerate_".$dados_ponto["id"]."=".$samplerate."\n";
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
	
	$ssh->executar("rm -fv /home/streaming/playlists/".$dados_stm["porta"].".pls");
	
	$ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls");
	
	$resultado = $ssh->executar("/home/streaming/ligar_autodj /home/streaming/configs/".$config_autodj."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if(is_numeric($resultado) || $resultado == "ok") {
	
	// Atualiza a última playlist tocada e o bitrate do autodj
	mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."', bitrate_autodj = '".$bitrate."', xfade = '".$xfade."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_autodj_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='javascript:window.location.reload();document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_ligar_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_erro']."");
	
	}
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x003 ".$lang['lang_acao_ligar_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_erro']."");
	
	}
	
	exit();
}

// Função para desligar autodj
if($acao == "desligar_autodj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_desligar_autodj_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='javascript:window.location.reload();document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_desligar_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_autodj_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_desligar_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_autodj_resultado_erro']."");
	
	}
	
	exit();
}

// Função para recarregar playlist no autodj
if($acao == "pular_musica") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica se o autodj esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	$resultado = $ssh->executar("/home/streaming/pular_musica_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_pular_musica_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_pular_musica_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_pular_musica_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_pular_musica_autodj_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_pular_musica_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_pular_musica_autodj_resultado_erro']."");
	
	}
	
	exit();
}

// Função para recarregar playlist no autodj
if($acao == "recarregar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('3'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	
	exit();	
	}
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica se o autodj esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	$resultado = $ssh->executar("/home/streaming/recarregar_playlist_autodj ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."");
	
	}
	
	exit();
}

?>