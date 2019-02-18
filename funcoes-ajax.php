<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "1024M");
ini_set("max_execution_time", 600);

// Inclusão de classes
require_once("admin/inc/classe.ssh.php");
require_once("admin/inc/classe.ftp.php");
require_once("admin/inc/classe.mail.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

// Funções gerais para uso com Ajax

$acao = query_string('1');

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar streaming
if($acao == "ligar_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
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
	
	$max_ouvintes = ($dados_stm["ouvintes"] == 999999) ? '0' : $dados_stm["ouvintes"];
	
	$config_streaming .= "portbase=".$dados_stm["porta"]."\n";
	$config_streaming .= "maxuser=".$max_ouvintes."\n";
	$config_streaming .= "adminpassword=".$senha_admin."\n";
	$config_streaming .= "password=".$dados_stm["senha"]."\n";
	$config_streaming .= "srcip=any\n";
	$config_streaming .= "destip=any\n";
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
	$config_streaming .= "logclients=1\n";
	$config_streaming .= "showlastsongs=".$dados_stm["showlastsongs"]."\n";
	$config_streaming .= ";YPSERVER\n";
	$config_streaming .= "ypaddr=46.105.114.166\n";
	$config_streaming .= "ypport=80\n";
	$config_streaming .= "ypPath=/yp2\n";
	$config_streaming .= "ypTimeout=10\n";
	$config_streaming .= "ypmaxretries=10\n";
	$config_streaming .= "ypreportinterval=3600\n";
	$config_streaming .= "ypminreportinterval=1800\n";
	$config_streaming .= "configrewrite=1\n";
	$config_streaming .= ";UPDATE\n";
	$config_streaming .= "cpucount=24\n";
	//$config_streaming .= "portlegacy=0\n";
	$config_streaming .= "maxbitrate=".$dados_stm["bitrate"]."000\n";
	
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
	
	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_streaming."","/home/streaming/configs/".$config_streaming."",0777);
	
	unlink("/home/painel/public_html/temp/".$config_streaming."");
	
	$resultado = $ssh->executar("/home/streaming/ligar_streaming /home/streaming/configs/".$config_streaming."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if(is_numeric($resultado)) {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	if($dados_stm["autodj"] == "sim") {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a><br /><a href='javascript:void(0);' onClick='carregar_playlists(\"".code_decode($dados_stm["porta"],"E")."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_ligar_autodj']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	}
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_erro']."");
	
	}
	
	} else {
	
	// Força desligamento do streaming por ter ocorrido erro ao ligar
	$ssh->executar("echo OK;/bin/ps aux | /bin/grep ".$dados_stm["porta_dj"]." | /bin/grep sc_trans | /bin/awk '{ print $2;}' | /usr/bin/head -50 | /usr/bin/xargs /bin/kill -9");
	$ssh->executar("echo OK;/bin/ps aux | /bin/grep ".$dados_stm["porta"]." | /bin/grep sc_serv | /bin/awk '{ print $2;}' | /usr/bin/head -50 | /usr/bin/xargs /bin/kill -9");
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_stm_resultado_erro']."");
	
	}
	
	} else {
	
	// Força desligamento do streaming por ter ocorrido erro ao ligar
	$ssh->executar("echo OK;/bin/ps aux | /bin/grep ".$dados_stm["porta_dj"]." | /bin/grep sc_trans | /bin/awk '{ print $2;}' | /usr/bin/head -50 | /usr/bin/xargs /bin/kill -9");
	$ssh->executar("echo OK;/bin/ps aux | /bin/grep ".$dados_stm["porta"]." | /bin/grep sc_serv | /bin/awk '{ print $2;}' | /usr/bin/head -50 | /usr/bin/xargs /bin/kill -9");
	
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
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
		
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
	
	// Desliga
	$porta_listen = $dados_stm["porta"]+1;
	
	$ssh->executar("lsof -i :".$dados_stm["porta"]." | grep -v PID | awk {'print $2'} | xargs kill -9;echo ok");
	$ssh->executar("netstat -anp | grep :".$dados_stm["porta"]." | awk {'print $7'} | cut -d / -f 1 | xargs kill -9;echo ok");
	$ssh->executar("lsof -i :".$porta_listen." | grep -v PID | awk {'print $2'} | xargs kill -9;echo ok");
	$ssh->executar("netstat -anp | grep :".$porta_listen." | awk {'print $7'} | cut -d / -f 1 | xargs kill -9;echo ok");
	
	$ssh->executar("echo OK;/bin/ps aux | /bin/grep ".$dados_stm["porta_dj"]." | /bin/grep sc_trans | /bin/awk '{ print $2;}' | /usr/bin/head -50 | /usr/bin/xargs /bin/kill -9");
	$resultado = $ssh->executar("echo OK;/bin/ps aux | /bin/grep ".$dados_stm["porta"]." | /bin/grep sc_serv | /bin/awk '{ print $2;}' | /usr/bin/head -50 | /usr/bin/xargs /bin/kill -9");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "OK") {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_desligar_stm_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_desligar_stm_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_desligar_stm_resultado_erro']."");
	
	}
	
	exit();
}

// Função para verificar o status do streaming e autodj
if($acao == "status_streaming") {
	
	// Proteção contra sessão expirada
	if(empty($_SESSION["porta_logada"])) {
	
	echo "<font color=\"#FF0000\" size=\"4\"><strong>Sessão Expirada!</strong></font><br><font color=\"#FF0000\" size=\"2\"><strong>Faça login novamente.</strong></font>";
	exit();	
	}

	$porta = code_decode(query_string('2'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {
	echo "<font color=\"#999999\" size=\"5\"><strong>".$lang['lang_info_status_manutencao']."</strong></font>";
	exit();
	}
	
	$status_conexao = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	$status_conexao_transmissao = status_streaming_transmissao($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"]);
	
	if($status_conexao_transmissao == "relay") {
	echo "<font color=\"#009900\" size=\"5\"><strong>".$lang['lang_info_status_transmitindo']."</strong></font><br><font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_relay']."</strong></font>";
	exit();
	}
	
	if($status_conexao_transmissao == "autodj") {
	echo "<font color=\"#009900\" size=\"5\"><strong>".$lang['lang_info_status_transmitindo']."</strong></font><br><font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_autodj']."</strong></font>";
	exit();
	}
	
	if($status_conexao_transmissao == "aovivo") {
	echo "<font color=\"#009900\" size=\"5\"><strong>".$lang['lang_info_status_transmitindo']."</strong></font><br><font color=\"#009900\" size=\"2\"><strong>".$lang['lang_info_status_aovivo']."</strong></font>";
	exit();
	}
	
	if($status_conexao == "ligado") {
	echo "<font color=\"#009900\" size=\"6\"><strong>".$lang['lang_info_status_ligado']."</strong></font>";
	exit();
	}
	
	echo "<font color=\"#999999\" size=\"6\"><strong>".$lang['lang_info_status_desligado']."</strong></font>";
	
	exit();
	
}

// Função para verificar o status do streaming e autodj
if($acao == "status_streaming_movel") {

	$porta = code_decode(query_string('2'),"D");
		
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

// Função para verificar o status do streaming e autodj
if($acao == "status_streaming_interno") {

	$porta = code_decode(query_string('2'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$status_conexao = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	$status_conexao_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	$status_conexao_relay = status_relay($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($dados_servidor["status"] == "off") {
	echo "manutencao";
	exit();
	}
	
	if($status_conexao_relay == "ligado") {
	echo "ligado-relay";
	exit();
	}
	
	if($status_conexao_autodj == "ligado") {
	echo "ligado-autodj";
	exit();
	}
	
	if($status_conexao == "ligado" && $dados_stm["autodj"] == "nao") {
	echo "ligado-autodj-desativado";
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

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
	if(empty($dados_stm["senha_admin"])) {	
	echo "<span class='texto_status_alerta'>".$lang['lang_kick_streaming_resultado_erro_senha_admin']."</span>";
	exit();	
	}
	
	$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($total_pontos > 0) {
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_kick_streaming_tab_titulo'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
 <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="140" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_kick_streaming_multipoint'].'</td>
        <td width="435" align="left">
        <select name="ponto" class="input" id="ponto" style="width:255px;">
          <option value="0" selected="selected">'.$lang['lang_kick_streaming_multipoint_todos'].'</option>';
		  
          $sql = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
		  while ($dados_ponto = mysql_fetch_array($sql)) {
			echo '<option value="' . $dados_ponto["id"] . '">ID: ' . $dados_ponto["id"] . ' - '.$lang['lang_kick_streaming_multipoint_ponto'].': ' . $dados_ponto["ponto"] . '</option>';
		  }
		  
        echo '</select>
        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="button" class="botao" value="'.$lang['lang_botao_titulo_executar'].'" onclick="kick_streaming_multipoint(\''.query_string('2').'\',document.getElementById(\'ponto\').value);" />
		  </td>
      </tr>
    </table>
	</div>
</div>';
	
	exit();
	
	} else {
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	$kick = curl_init();
	curl_setopt($kick, CURLOPT_URL, "http://".$dados_servidor["ip"].":".$dados_stm["porta"]."/admin.cgi?sid=1&mode=kicksrc&pass=".$dados_stm["senha"]."");
	curl_setopt($kick, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($kick, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	
	if(curl_exec($kick) === false) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_kick_streaming_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_kick_streaming_resultado_erro']."");
	
	} else {
	
	sleep(10);	
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_kick_streaming_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_kick_streaming_resultado_ok']."");
	
	}
	
	curl_close($kick);
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_kick_streaming_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_kick_streaming_resultado_alerta']."");
	
	}
	
	}
	
	exit();
	
}

// Função para desconectar o source do streaming(kick)
if($acao == "kick_streaming_multipoint") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$ponto = query_string('3');
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
	if(empty($dados_stm["senha_admin"])) {	
	echo "<span class='texto_status_alerta'>".$lang['lang_kick_streaming_resultado_erro_senha_admin']."</span>";
	exit();	
	}
	
	$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
	if($status_streaming == "ligado") {
	
	
	if($ponto == 0) {
	
	$sql = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
	while ($dados_ponto = mysql_fetch_array($sql)) {
	
	$kick = curl_init();
	curl_setopt($kick, CURLOPT_URL, "http://".$dados_servidor["ip"].":".$dados_stm["porta"]."/admin.cgi?sid=".$dados_ponto["id"]."&mode=kicksrc&pass=".$dados_stm["senha_admin"]."");
	curl_setopt($kick, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($kick, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$resultado = curl_exec($kick);
	curl_close($kick);	
	
	}
		
	} else {	
	
	$kick = curl_init();
	curl_setopt($kick, CURLOPT_URL, "http://".$dados_servidor["ip"].":".$dados_stm["porta"]."/admin.cgi?sid=".$ponto."&mode=kicksrc&pass=".$dados_stm["senha_admin"]."");
	curl_setopt($kick, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($kick, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$resultado = curl_exec($kick);
	curl_close($kick);
	
	}
	
	if(preg_match('/redirect/i',$resultado)) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_kick_streaming_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_kick_streaming_resultado_ok']."");
	
	} else {	
	
	echo "<span class='texto_status_erro'>".$lang['lang_kick_streaming_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_kick_streaming_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_kick_streaming_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_kick_streaming_resultado_alerta']."");
	
	}
	
	exit();
	
}

// Função para checar a quantidade de ouvintes online e criar a barra de porcentagem de uso
if($acao == "estatistica_uso_plano") {

	$porta = query_string('2');
	$recurso = query_string('3');
	$texto = query_string('4');
	
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
	
	if($ouvintes_total_aacplus > 0) {
	$ouvintes_total_shoutcast = $ouvintes_total_shoutcast-1;	
	}	
	
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

// Função para carregar o formulário para geração das estatísticas do streaming
if($acao == "carregar_estatisticas_streaming") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	// Proteção contra usuario não logados
	if(empty($_SESSION["porta_logada"])) {
	die("<span class='texto_status_erro'>0x002 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_estatisticas_tab_titulo'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
 <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="140" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_estatisticas_estatistica'].'</td>
        <td width="435" align="left">
        <select name="estatistica" class="input" id="estatistica" style="width:255px;" onchange="tipo_estatistica(this.value);">
          <option value="1">'.$lang['lang_info_estatisticas_estatistica_ouvintes'].'</option>
		  <option value="2">'.$lang['lang_info_estatisticas_estatistica_ouvintes_meses_ano'].'</option>
          <option value="3">'.$lang['lang_info_estatisticas_estatistica_tempo_conectado'].'</option>
          <option value="4">'.$lang['lang_info_estatisticas_estatistica_paises'].'</option>
		  <option value="5">'.$lang['lang_info_estatisticas_estatistica_players'].'</option>
		  <option value="6">'.$lang['lang_info_estatisticas_estatistica_ouvintes_hora'].'</option>
        </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="left">
        <table width="545" border="0" cellspacing="0" cellpadding="0" id="tabela_data">
          <tr>
            <td width="140" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_estatisticas_periodo'].'</td>
        <td width="405" align="left">
        <select name="mes" class="input" id="mes" style="width:162px;">';
						
						foreach(array("01" => "".$lang['lang_info_estatisticas_periodo_01']."","02" => "".$lang['lang_info_estatisticas_periodo_02']."","03" => "".$lang['lang_info_estatisticas_periodo_03']."","04" => "".$lang['lang_info_estatisticas_periodo_04']."","05" => "".$lang['lang_info_estatisticas_periodo_05']."","06" => "".$lang['lang_info_estatisticas_periodo_06']."","07" => "".$lang['lang_info_estatisticas_periodo_07']."","08" => "".$lang['lang_info_estatisticas_periodo_08']."","09" => "".$lang['lang_info_estatisticas_periodo_09']."","10" => "".$lang['lang_info_estatisticas_periodo_10']."","11" => "".$lang['lang_info_estatisticas_periodo_11']."","12" => "".$lang['lang_info_estatisticas_periodo_12']."") as $mes => $mes_nome){
							if($mes == date("m")) {
								echo "<option value=\"".$mes."\" selected=\"selected\">".$mes_nome."</option>\n";
							} else {
								echo "<option value=\"".$mes."\">".$mes_nome."</option>\n";
							}
						}

        echo '</select>&nbsp;';
        echo '<select name="ano" class="input" id="ano" style="width:90px;">';

				$ano_inicial = date("Y")-1;
				$ano_final = date("Y")+1;
				$qtd = $ano_final-$ano_inicial;
					for($i=0; $i <= $qtd; $i++) {
							if(sprintf("%02s",$ano_inicial+$i) == date("Y")) {
								echo "<option value=\"".sprintf("%02s",$ano_inicial+$i)."\" selected=\"selected\">".sprintf("%02s",$ano_inicial+$i)."</option>\n";
							} else {
								echo "<option value=\"".sprintf("%02s",$ano_inicial+$i)."\">".sprintf("%02s",$ano_inicial+$i)."</option>\n";
							}
					}
					
        echo '</select></td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="button" class="botao" value="'.$lang['lang_botao_titulo_visualizar'].'" onclick="window.open(\'/estatisticas/\'+document.getElementById(\'estatistica\').value+\'/\'+document.getElementById(\'mes\').value+\'/\'+document.getElementById(\'ano\').value+\'\',\'conteudo\');this.disabled" />
		  </td>
      </tr>
    </table>
	</div>
</div>';
	
	exit();

}

// Função para exibir a música atual tocando no streaming
if($acao == "musica_atual") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = query_string('2');
	$limite_caracteres = query_string('3');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$musica = @file_get_contents("http://".$dados_servidor["ip"].":".$dados_stm["porta"]."/currentsong?sid=1");
	
	$musica = ucwords(strtolower($musica));
	
	if(strlen($musica) > $limite_caracteres) {
	echo "<img src='http://".$_SERVER['HTTP_HOST']."/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;".substr($musica, 0, $limite_caracteres)."...";
	} else {
	echo "<img src='http://".$_SERVER['HTTP_HOST']."/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;".$musica;
	}
	
	exit();
	
}

// Função para exibir a música atual tocando no streaming
if($acao == "capa_musica_atual") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = query_string('2');
	
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$host_sources = ($dados_config["usar_cdn"] == "sim") ? $dados_config["dominio_cdn"] : $_SERVER['HTTP_HOST'];
	
	if($dados_config["usar_cdn"] == "sim") {
	
	$musica = file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&recurso=musica&ponto=1");
	$musica_partes = explode("-",$musica);
	
	} else {
	
	$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
	$musica_partes = explode("-",$info["musica"]);
	
	}

	$artista = str_replace("_"," ",$musica_partes[0]);
	$artista = rtrim($artista, " \t.");
	
	$musica = $musica_partes[1];

	$resultado_api_lastfm_vagalume = lastfm('artist',$artista);
	
	if($resultado_api_lastfm_vagalume["status"] == "ok") {
	$imagem = $resultado_api_lastfm_vagalume["imagem"];
	} else {
	$resultado_api_lastfm_vagalume = vagalumeapi('capa2',$artista,$musica);
	}
	
	if($resultado_api_lastfm_vagalume["status"] == "ok") {
	$imagem = $resultado_api_lastfm_vagalume["imagem"];
	} else {
	$resultado_api_lastfm_vagalume = vagalumeapi('capa1',$artista,'');
	}

	if($resultado_api_lastfm_vagalume["status"] == "ok") {
	echo $imagem = $resultado_api_lastfm_vagalume["imagem"];
	} else {
	echo "http://".$host_sources."/img/img-capa-artista-padrao.png";
	}
	
	exit();
	
}


// Função para sincronizar streaming no servidor AAC+
if($acao == "sincronizar_aacplus") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Ativa o relay no servidor RTMP	
	if($dados_stm["aacplus"] == 'sim') {
	
	$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor_aacplus["senha"],"D"));
	
	$ssh->executar("/usr/local/WowzaMediaServer/sincronizar-aacplus ".$dados_stm["porta"]." ".$dados_servidor["ip"]." ".$dados_stm["ouvintes"]."");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_sinc_aacplus_stm_resultado_ok']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_sinc_aacplus_stm_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_alerta'>0x001 ".$lang['lang_acao_sinc_aacplus_stm_resultado_alerta']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";	
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_sinc_aacplus_stm_resultado_alerta']."");
	
	}
	
	}
	
	exit();
	
}

// Função para atualizar cache player facebook
if($acao == "atualizar_cache_player_facebook") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_pagina_resolver_problemas_tab_titulo_facebook'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
   <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="left" class="texto_padrao"><br />'.sprintf($lang['lang_acao_pagina_resolver_problemas_player_facebook'],$dados_config["dominio_padrao"],$dados_stm["porta"],$dados_config["dominio_padrao"],$dados_stm["porta"]).'<br /></td>
      </tr>
    </table>
  </div>
</div>';
	
	}
	
	exit();
	
}

// Função para atualizar cache dos players
if($acao == "atualizar_cache_players") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
	@file_get_contents("http://player.audiocast.ml/atualizar-cache-player/".$porta."");
	@file_get_contents("http://player.audiocast.ml/atualizar-cache-player/".$porta."");
	@file_get_contents("http://player.audiocast.ml/atualizar-cache-player/".$porta."");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_pagina_resolver_problemas_player_cache_resultado_ok']."</span>";
	
	}
	
	exit();
	
}

// Função para configurar o encoder correto no streaming
if($acao == "configurar_encoder") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");

	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se o RTMP esta ativado
	if($dados_stm["aacplus"] == 'sim' && $dados_stm["encoder_aacplus"] == "sim") {
	
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_pagina_resolver_problemas_encoder_resultado_ok']."</span>";
	
	exit();
	
	}
	
	} else {
	
	// RTMP desativado -> Encoder MP3
	if($dados_stm["encoder"] == 'aacp') {
	
	mysql_query("Update streamings set encoder = 'mp3' where codigo = '".$dados_stm["codigo"]."'");

	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_pagina_resolver_problemas_encoder_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_pagina_resolver_problemas_encoder_resultado_ok']."");
	
	exit();
	
	}
	
	}	
	
	// tudo certo nada a fazer
	echo "<span class='texto_status_alerta'>".$lang['lang_acao_pagina_resolver_problemas_encoder_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_pagina_resolver_problemas_encoder_resultado_alerta']."");
	
	}

	exit();
	
}

// Função para gerar qr code do link do app no google play
if($acao == "gerar_qr_code_app") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$package = query_string('2');

	if($package == "") {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
	echo '<img src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=market://details?id='.$package.'" width="150" height="150" />';
	echo '<br><br>';
	echo '<textarea name="textarea" readonly="readonly" style="width:99%; height:40px;font-size:11px" onmouseover="this.select()"><img src="http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=market://details?id='.$package.'" width="200" height="200" /></textarea>';
	
	}
	
	exit();
	
}

// Função para carregar a lista de players
if($acao == "carregar_players") {
	
	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	// Proteção contra usuario não logados
	if(empty($_SESSION["porta_logada"])) {
	die("<span class='texto_status_erro'>0x002 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
	$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_players_tab_players'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;">
          <select name="players" class="input" id="players" style="width:98%;" onchange="window.open(this.value,\'conteudo\');">
		    <option value="/gerenciar-player-html5-simples">'.$lang['lang_info_players_player_selecione'].'</option>
            <option value="/gerenciar-player-html5-simples">'.$lang['lang_info_players_player_html5_simples'].'</option>			
            <option value="/gerenciar-player-muses">'.$lang['lang_info_players_player_muses'].'</option>
            <option value="/gerenciar-player-topo">'.$lang['lang_info_players_player_flash_topo'].'</option>
            <option value="/gerenciar-player-computador">'.$lang['lang_info_players_player_computador'].'</option>
            <option value="/gerenciar-player-celulares">'.$lang['lang_info_players_player_celulares'].'</option>
            <option value="/gerenciar-player-facebook">'.$lang['lang_info_players_player_facebook'].'</option>
			<option value="/gerenciar-player-twitter">'.$lang['lang_info_players_player_twitter'].'</option>';
			if($dados_stm["stm_exibir_app_android"] == 'sim') {
			echo '<option value="/app-android">'.$lang['lang_info_players_player_app_android'].'</option>';
			}
            echo '<option value="/gerenciar-player-popup">'.$lang['lang_info_players_player_popup'].'</option>
			<option value="/gerenciar-player-popup-responsivo">'.$lang['lang_info_players_player_popup_responsivo'].'</option>
         </select>
         </td>
      </tr>
    </table>
  </div>
</div>';

	exit();
	
}

// Função para remover um DJ do AutoDJ
if($acao == "remover_multipoint") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");

	mysql_query("Delete From multipoint where codigo = '".$codigo."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_multipoint_resultado_remover_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_multipoint_resultado_remover_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover um pedido de musica
if($acao == "remover_pedido_musical") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");

	mysql_query("Delete FROM pedidos_musicais where codigo = '".$codigo."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_pedidos_musicais_resultado_remover_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_pedidos_musicais_resultado_remover_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para mudar a música atual no shoutcast para um texto qualquer
if($acao == "definir_nome_musica") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$musica = query_string('3');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	if($total_pontos > 0) {
	
	$sql_pontos = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
	while ($dados_ponto = mysql_fetch_array($sql_pontos)) {
	definir_nome_musica_shoutcast($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"],$dados_ponto["id"],$musica);
	}
	
	} else {
	definir_nome_musica_shoutcast($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"],1,$musica);
	}
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_pagina_informacoes_tab_menu_definir_nome_musica_ok']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	exit();

}

////////////////////////////////////////////////////////
///////////// Funções Gerenciamento AutoDJ /////////////
////////////////////////////////////////////////////////

// Função para carregar as playlists do streaming para ligar o autodj
if($acao == "carregar_playlists") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Verifica se o relay esta ativado
	if($dados_stm["relay"] == "sim") {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_autodj_resultado_relay']."</span>";
	
	exit();
	}
	
	// Verifica se tem alguém ao vivo
	$status_conexao_transmissao = status_streaming_transmissao($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"]);
	
	if($status_conexao_transmissao == "aovivo") {
	
	echo "<span class='texto_status_alerta'>0x002 ".$lang['lang_acao_ligar_autodj_resultado_alerta_aovivo']."</span>";
	
	exit();
	}
	// Verifica se o autodj já esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"]);
	
	if($status_autodj == "ligado") {
	
	echo "<span class='texto_status_alerta'>0x003 ".$lang['lang_acao_ligar_autodj_resultado_alerta']."</span>";
	
	exit();
	}
	
	$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
	$dados_ultima_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
	$total_musicas_ultima_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_stm["ultima_playlist"]."'"));
	
	if($total_playlists > 0) {
	
	echo '</script><div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_autodj_ligar'].'</strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao">
    	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; margin-left:0 auto; margin-right:0 auto; background-color: #C1E0FF; border: #006699 1px solid">
			<tr>
			<td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
			<td align="left" class="texto_padrao_destaque" scope="col">'.$lang['lang_info_autodj_ligar_opcoes'].'</td>
		   </tr>
        </table>
     </td>
  </tr>
  <tr>
    <td height="25">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_autodj_playlists'].'</td>
            <td align="left">
            <select name="playlist" id="playlist" style="width:255px;">
			<optgroup label="'.$lang['lang_info_autodj_ultima_playlist'].'">';
			if($dados_stm["ultima_playlist"] > 0) {
			echo '<option value="'.$dados_stm["ultima_playlist"].'">'.$dados_ultima_playlist["nome"].' ('.$total_musicas_ultima_playlist.')</option>';
			}
			echo '</optgroup>
			<optgroup label="'.$lang['lang_info_autodj_playlists'].'">';
			$query_playlists = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
			while ($dados_playlist = mysql_fetch_array($query_playlists)) {
	
			$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'"));

			echo '<option value="'.$dados_playlist["codigo"].'">'.$dados_playlist["nome"].' ('.$total_musicas.')</option>';
		
			}
			
			echo '</select>
            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">'.$lang['lang_info_autodj_bitrate'].'</td>
            <td align="left">
            <select name="bitrate" id="bitrate" style="width:255px;">';
			
			foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
	
				if($bitrate <= $dados_stm["bitrate"]) {
					
					if($dados_stm["bitrate_autodj"]) {
				
					if($bitrate == $dados_stm["bitrate_autodj"]) {
						echo '<option value="'.$bitrate.'" selected="selected">'.$bitrate.' Kbps</option>';
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
            echo '</select>
			</td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">'.$lang['lang_info_autodj_shuffle'].'</td>
            <td align="left">
            <select name="shuffle" id="shuffle" style="width:255px;">
			<option value="0" selected="selected">'.$lang['lang_info_autodj_shuffle_opcao_seguir_ordem'].'</option>
			<option value="1">'.$lang['lang_info_autodj_shuffle_opcao_misturar'].'</option>
			</select>
         	</td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_autodj_xfade'].'</td>
            <td align="left" class="texto_padrao">
			<select name="xfade" id="xfade" style="width:255px;">';
			
			foreach(array("0" => "(".$lang['lang_info_autodj_xfade_0'].")","2" => "".$lang['lang_info_autodj_xfade_2']."","4" => "".$lang['lang_info_autodj_xfade_4']."","6" => "".$lang['lang_info_autodj_xfade_6']."","8" => "".$lang['lang_info_autodj_xfade_8']."","10" => "".$lang['lang_info_autodj_xfade_10']."") as $xfade => $xfade_descricao){
	
				if($xfade == $dados_stm["xfade"]) {
					echo '<option value="'.$xfade.'" selected="selected">'.$xfade_descricao.'</option>';
				} else {
					echo '<option value="'.$xfade.'">'.$xfade_descricao.'</option>';
				}
			
			}
			
            echo '</select>
			</td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">'.$lang['lang_info_autodj_samplerate'].'</td>
            <td align="left" class="texto_padrao">
            <select name="samplerate" class="input" id="samplerate" style="width:255px;">';
			
			foreach(array("22050", "32000", "44100", "48000", "88200", "96000") as $samplerate) {
	
				if($samplerate == $dados_stm["autodj_samplerate"]) {
				echo '<option value="'.$samplerate.'" selected="selected">'.$samplerate.' Hz</option>';
				} else {
				echo '<option value="'.$samplerate.'">'.$samplerate.' Hz</option>';
				}
			}
			
			echo '</select>
            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">'.$lang['lang_info_autodj_canal'].'</td>
            <td align="left">
			<select name="canal" class="input" id="canal" style="width:255px;">';
            
			foreach(array("1" => $lang['lang_info_autodj_canal_mono'], "2" => $lang['lang_info_autodj_canal_stereo']) as $canal => $canal_nome) {
	
				if($canal == $dados_stm["autodj_channels"]) {
				echo '<option value="'.$canal.'" selected="selected">'.$canal_nome.'</option>';
				} else {
				echo '<option value="'.$canal.'">'.$canal_nome.'</option>';
				}
			}
			
            echo '</select>
			</td>
          </tr>
		  <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">'.$lang['lang_info_autodj_encoder'].'</td>
            <td align="left">
			<select name="encoder" class="input" id="encoder" style="width:255px;">';
			
			if($dados_stm["encoder_mp3"] == "sim") {
			if($dados_stm["encoder"] == "mp3") {
			echo '<option value="mp3" selected="selected">MP3</option>';
			} else {
			echo '<option value="mp3">MP3</option>';
			}
			}
			
			if($dados_stm["encoder_aacplus"] == "sim") {
			if($dados_stm["encoder"] == "aacp") {
			echo '<option value="aacp" selected="selected">AACPlus</option>';
			} else {
			echo '<option value="aacp">AACPlus</option>';
			}
			}
			
            echo '</select>
			</td>
          </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td height="30" align="center"><input type="button" class="botao" value="'.$lang['lang_botao_titulo_ligar_autodj'].'" onclick="ligar_autodj(\''.code_decode($dados_stm["porta"],"E").'\',document.getElementById(\'playlist\').value,document.getElementById(\'shuffle\').value,document.getElementById(\'bitrate\').value,document.getElementById(\'xfade\').value,document.getElementById(\'samplerate\').value,document.getElementById(\'canal\').value,document.getElementById(\'encoder\').value);" /></td>
  </tr>
</table>
</div>
</div>';
	
	} else {
	echo "<span class='texto_status_erro'>0x003 ".$lang['lang_info_autodj_sem_playlist']."</span>";
	}
	
	exit();

}

// Função para ligar autodj
if($acao == "ligar_autodj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$playlist = query_string('3');
	$shuffle = query_string('4');
	$bitrate = query_string('5');
	$xfade = query_string('6');
	$samplerate = query_string('7');
	$canal = query_string('8');
	$encoder = query_string('9');
	$agendamento = query_string('10');
	
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
	$config_autodj .= "encoder=".$encoder."\n";
	$config_autodj .= "samplerate=".$samplerate."\n";
	$config_autodj .= "channels=".$canal."\n";
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
	
	// Atualiza os dados selecionados ao ligar o autodj
	mysql_query("Update streamings set ultima_playlist = '".$dados_playlist["codigo"]."', bitrate_autodj = '".$bitrate."', xfade = '".$xfade."', autodj_samplerate = '".$samplerate."', autodj_channels = '".$canal."', autodj_shuffle = '".$shuffle."', encoder = '".$encoder."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_ligar_autodj_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_autodj_resultado_erro']."</span><br /><br/><a href='javascript:void(0);' onClick='diagnosticar_autodj(\"".code_decode($dados_stm["porta"],"E")."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_diagnosticar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_erro']."");
	
	}
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_ligar_autodj_resultado_erro_stm_desligado']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_ligar_autodj_resultado_erro_stm_desligado']."");
	
	}
	
	exit();
}

// Função para desligar autodj
if($acao == "desligar_autodj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
		
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
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_desligar_autodj_resultado_ok']."</span><br /><br/><a href='javascript:void(0);' onClick='status_streaming(\"".code_decode($dados_stm["porta"],"E")."\");document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
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
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
		
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
	
	$resultado = $ssh->executar("/home/streaming/gerenciar_autodj pular_musica ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
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
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
		
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
	
	$resultado = $ssh->executar("/home/streaming/gerenciar_autodj recarregar_playlist ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");
	
	$resultado = str_replace("\n","",$resultado);
	
	if($resultado == "ok") {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_recarregar_playlist_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_recarregar_playlist_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_recarregar_playlist_autodj_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_recarregar_playlist_autodj_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_recarregar_playlist_autodj_resultado_alerta']."");
	
	}
	
	exit();
}

// Função para trocar playlist no autodj sem reiniciar
if($acao == "trocar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$acao = query_string('3');
	$playlist = query_string('4');
		
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	if($dados_servidor["status"] == "off") {	
	echo "<span class='texto_status_alerta'>".$lang['lang_alerta_manutencao_servidor']."</span>";
	exit();	
	}
	
	if($acao == "carregar_playlists") {
	
	$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($total_playlists > 0) {
	
	$dados_ultima_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$dados_stm["ultima_playlist"]."'"));
	$total_musicas_ultima_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_stm["ultima_playlist"]."'"));
	
	echo '<div id="quadro">
<div id="quadro-topo"><strong>'.$lang['lang_info_autodj_trocar_playlist_tab_titulo'].'</strong></div>
 <div class="texto_medio" id="quadro-conteudo">
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
  	  <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        <td width="430" align="left" class="texto_pequeno_erro" scope="col">'.$lang['lang_info_autodj_trocar_playlist'].'</td>
     </tr>
    </table>
    <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding-left:5px;padding-right:5px;">';
	echo '<select name="playlist" id="playlist" style="width:100%;">';
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
	echo '</td>
      </tr>
      <tr>
        <td height="40" align="center">
          <input type="button" class="botao" value="'.$lang['lang_botao_titulo_trocar'].'" onClick="trocar_playlist(\''.query_string('2').'\',\'trocar\',document.getElementById(\'playlist\').value);" /></td>
      </tr>
	</table>
  </div>
</div>';
	
	} else {
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_info_autodj_sem_playlist']."</span>";
	}
	
	} else {
	
	$dados_playlist_selecionada = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));
	
	// Verifica se o autodj esta ligado
	$status_autodj = status_autodj($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
	
	if($status_autodj == "ligado") {
	
	$resultado = $ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist_selecionada["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls;/home/streaming/gerenciar_autodj recarregar_playlist ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");

	if(preg_match('/'.$dados_stm["porta"].'/i',$resultado)) {
	
	// Atualiza a última playlist tocada
	mysql_query("Update streamings set ultima_playlist = '".$dados_playlist_selecionada["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_trocar_playlist_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_trocar_playlist_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_trocar_playlist_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_trocar_playlist_autodj_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_trocar_playlist_autodj_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_trocar_playlist_autodj_resultado_alerta']."");
	
	}
	
	}
	
	exit();
}

// Função para remover um DJ do AutoDJ
if($acao == "remover_dj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");
	
	// Remove DJ
	mysql_query("Delete From djs where codigo = '".$codigo."'");
	
	// Remove Restricções
	mysql_query("Delete From djs_restricoes where codigo_dj = '".$codigo."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_remover_dj_autodj_resultado_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_remover_dj_autodj_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover uma restrição de um DJ
if($acao == "remover_dj_restricao") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");
	
	mysql_query("Delete From djs_restricoes where codigo = '".$codigo."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_remover_dj_autodj_resultado_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_acao_remover_dj_autodj_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para diagnosticar erros no AutoDJ
if($acao == "diagnosticar_autodj") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
		
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
	
	// Verifica os logs do AutoDJ a procura de erros	
	$checagem_erro1 = $ssh->executar("grep 'Error opening port' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro2 = $ssh->executar("grep 'Playlist has run dry' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro3 = $ssh->executar("grep 'Playlist is empty' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro4 = $ssh->executar("grep 'deactivating playlist' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	$checagem_erro5 = $ssh->executar("grep 'Bind error' /home/streaming/logs/autodj-".$dados_stm["porta"].".log | wc -l");
	
	if($checagem_erro1 > 0 || $checagem_erro5 > 0) {
	
	$porta_dj = $dados_stm["porta_dj"];
	$porta_dj_listen = $dados_stm["porta_dj"]+1;
	
	$ssh->executar("lsof -i :".$porta_dj." | grep -v PID | awk {'print $2'} | xargs kill -9;echo ok");
	$ssh->executar("netstat -anp | grep :".$porta_dj." | awk {'print $7'} | cut -d / -f 1 | xargs kill -9;echo ok");
	$ssh->executar("lsof -i :".$porta_dj_listen." | grep -v PID | awk {'print $2'} | xargs kill -9;echo ok");
	$ssh->executar("netstat -anp | grep :".$porta_dj_listen." | awk {'print $7'} | cut -d / -f 1 | xargs kill -9;echo ok");
	
	die("".$lang['lang_acao_diagnosticar_autodj_resultado_erro_porta']."<br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>");

	} elseif($checagem_erro2 > 0 || $checagem_erro3 > 0 || $checagem_erro4 > 0) {
	
	die("".$lang['lang_acao_diagnosticar_autodj_resultado_erro_playlist']."<br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>");
	
	} else {
	
	die("<span class='texto_status_sucesso'>".$lang['lang_acao_diagnosticar_autodj_resultado_sem_erros']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>");
	
	}
	
}

// Função para remover uma requisição de app android
if($acao == "remover_app_android") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo_app = code_decode(query_string('2'),"D");
	
	$dados_app = mysql_fetch_array(mysql_query("SELECT * FROM apps where codigo = '".$codigo_app."'"));
	
	mysql_query("Delete From apps where codigo = '".$dados_app["codigo"]."'");
	
	// Remove o apk e imagens
	@unlink("app_android/apps/".$dados_app["zip"]."");
	@unlink("app_android/apps/tmp/".$dados_app["hash"]."-img-play-app.png");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_streaming_app_android_resultado_remover_app_ok']."</span><br /><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	exit();

}

////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Playlists /////////////
////////////////////////////////////////////////////////

// Função para carregar as playlists
if($acao == "carregar_lista_playlists") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$total_playlists = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."'"));
	
	if($total_playlists > 0) {

	$query = mysql_query("SELECT * FROM playlists where codigo_stm = '".$dados_stm["codigo"]."' ORDER by nome ASC");
	while ($dados_playlist = mysql_fetch_array($query)) {
	
	$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' AND tipo != 'hc'"));
	
	echo "".$dados_playlist["codigo"]."|".$dados_playlist["nome"]."|".$total_musicas.";";
	
	}
	
	}
	
	exit();

}

// Função para carregar as pastas
if($acao == "carregar_lista_pastas") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	// Conexão FTP
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);

	$array_pastas = $ftp->listar_pastas("/");
	
	$lista_pastas .= "/|".$ftp->total_arquivos("/","mp3").";";

	if(count($array_pastas) > 0){
	
	foreach ($array_pastas as $pasta) {

	if($pasta != "." && $pasta != "..") {
	
	$lista_pastas .= "".$pasta."|".$ftp->total_arquivos($pasta,"mp3").";";
	
	}
	
	}
	
	}
	
	echo $lista_pastas;
		
	exit();

}


// Função para carregar as pastas
if($acao == "carregar_pasta_programetes") {

	ini_set("max_execution_time", 1800);

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$xml_pastas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-pastas-programetes.php?porta=".$dados_servidor["portapro"]."");
	
	$total_pastas = count($xml_pastas->pasta);

	if($total_pastas > 0) {

	for($i=0;$i<$total_pastas;$i++){
	
	$lista_pastas .= $xml_pastas->pasta[$i]->nome."|".$xml_pastas->pasta[$i]->total.";";
	
	}
	
	}
	
	echo $lista_pastas;
		
	exit();

}



// Função para carregar musicas do streaming(programetes)
if($acao == "carregar_musicas_pasta_programetes") {

	ini_set("max_execution_time", 1800);

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$pasta = query_string('3');
	$ordenar = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
		
	$xml_musicas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas-programetes.php?porta=".$dados_servidor["portapro"]."&pasta=".$pasta."&ordenar=".$ordenar."");
	
	$total_musicas = count($xml_musicas->musica);

	if($total_musicas > 0) {

	for($i=0;$i<$total_musicas;$i++){
	
	$lista_musicas .= utf8_decode($xml_musicas->musica[$i]->nome)."|".utf8_decode($xml_musicas->musica[$i]->duracao)."|".utf8_decode($xml_musicas->musica[$i]->duracao_segundos).";";
	
	}
	
	}
	
	echo $lista_musicas;
		
	exit();

}




// Função para carregar as pastas(avançado)
if($acao == "carregar_pastas") {

	ini_set("max_execution_time", 1800);

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$xml_pastas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-pastas.php?porta=".$dados_stm["porta"]."");
	
	$total_pastas = count($xml_pastas->pasta);

	if($total_pastas > 0) {

	for($i=0;$i<$total_pastas;$i++){
	
	$lista_pastas .= $xml_pastas->pasta[$i]->nome."|".$xml_pastas->pasta[$i]->total.";";
	
	}
	
	}
	
	echo $lista_pastas;
		
	exit();

}

// Função para carregar musicas do streaming(avançado)
if($acao == "carregar_musicas_pasta") {

	ini_set("max_execution_time", 1800);

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$pasta = query_string('3');
	$ordenar = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
		
	$xml_musicas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$pasta."&ordenar=".$ordenar."");
	
	$total_musicas = count($xml_musicas->musica);

	if($total_musicas > 0) {

	for($i=0;$i<$total_musicas;$i++){
	
	$lista_musicas .= $pasta."/".utf8_decode($xml_musicas->musica[$i]->nome)."|".utf8_decode($xml_musicas->musica[$i]->nome)."|".$xml_musicas->musica[$i]->duracao."|".$xml_musicas->musica[$i]->duracao_segundos.";";
	
	}
	
	}
	
	echo $lista_musicas;
		
	exit();

}

// Função para carregar musicas do streaming(avançado)
if($acao == "carregar_musicas_pasta_playlists") {
	
	ini_set("max_execution_time", 1800);

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$pasta = query_string('3');
	$ordenar = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
		
	$xml_musicas = @simplexml_load_file("http://".$dados_servidor["ip"].":555/listar-musicas.php?porta=".$dados_stm["porta"]."&pasta=".$pasta."&ordenar=".$ordenar."");
	
	$total_musicas = count($xml_musicas->musica);

	if($total_musicas > 0) {

	for($i=0;$i<$total_musicas;$i++){
	
	$lista_musicas .= utf8_decode($xml_musicas->musica[$i]->nome)."|".$xml_musicas->musica[$i]->duracao."|".$xml_musicas->musica[$i]->duracao_segundos.";";
	
	}
	
	}
	
	echo $lista_musicas;
		
	exit();

}

// Função para carregar musicas do playlist
if($acao == "carregar_musicas_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = query_string('2');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
	$total_musicas = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$playlist."'"));

	if($total_musicas > 0) {

	$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$playlist."' ORDER by ordem+0,codigo ASC");
	while ($dados_playlist_musica = mysql_fetch_array($query)) {
	
	echo "".$dados_playlist_musica["path_musica"]."|".$dados_playlist_musica["musica"]."|".$dados_playlist_musica["duracao"]."|".$dados_playlist_musica["duracao_segundos"]."|".$dados_playlist_musica["tipo"]."|".code_decode($dados_stm["porta"],"E").";";
	
	}
	
	}
	
	exit();

}

// Função para criar nova playlist
if($acao == "criar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$playlist = query_string('3');

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));

	$playlist_arquivo = "".$dados_stm["porta"]."-".formatar_nome_playlist($playlist).".pls";

	mysql_query("INSERT INTO playlists (codigo_stm,nome,arquivo,data) VALUES ('".$dados_stm["codigo"]."','".$playlist."','".$playlist_arquivo."',NOW())");
	$codigo_playlist = mysql_insert_id();
	
	if(!mysql_error()) {
	
	echo "ok|".code_decode($codigo_playlist,"E")."";
	
	} else {
	
	echo $lang['lang_acao_gerenciador_playlists_resultado_erro']." ".mysql_error()."";
	
	}
	
	exit();

}

// Função para remover música da playlist
if($acao == "remover_musica") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$musica = query_string('2')."/".query_string('3');

	$verifica_musica = mysql_num_rows(mysql_query("SELECT * FROM playlists_musicas where path_musica = '".$musica."'"));
	
	if($verifica_musica == 1) {
	
	mysql_query("Delete From playlists_musicas where path_musica = '".$musica."'") or die(mysql_error());
	
	}
	
	exit();

}

// Função para remover uma playlist
if($acao == "remover_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	//$playlist = query_string('2'); // REMOVER
	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_playlist["codigo_stm"]."'"));
	
	$verifica_playlist = mysql_num_rows(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	
	if($verifica_playlist == 1) {
	
	mysql_query("Delete From playlists where codigo = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From playlists_agendamentos where codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_playlist_resultado_ok']."</span><br /><a href='javascript:window.location.reload();' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_playlist_resultado_erro']."</span>";
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_playlist_resultado_alerta']."</span>";
	
	}
	
	exit();

}


// Função para iniciar transmissão de uma playlist pelo gerenciador de playlists
if($acao == "iniciar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist_selecionada = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_playlist_selecionada["codigo_stm"]."'"));
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
	
	$resultado = $ssh->executar("cp -fv /home/streaming/playlists/".$dados_playlist_selecionada["arquivo"]." /home/streaming/playlists/".$dados_stm["porta"].".pls;/home/streaming/gerenciar_autodj recarregar_playlist ".$dados_stm["porta"]." ".$dados_stm["porta_dj"]."");

	if(preg_match('/'.$dados_stm["porta"].'/i',$resultado)) {
	
	// Atualiza a última playlist tocada e o bitrate do autodj
	mysql_query("Update streamings set ultima_playlist = '".$dados_playlist_selecionada["codigo"]."' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_acao_trocar_playlist_autodj_resultado_ok']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_trocar_playlist_autodj_resultado_ok']."");
	
	} else {
	
	echo "<span class='texto_status_erro'>0x001 ".$lang['lang_acao_trocar_playlist_autodj_resultado_erro']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_trocar_playlist_autodj_resultado_erro']."");
	
	}
	
	} else {
	
	echo "<span class='texto_status_erro'>0x002 ".$lang['lang_acao_trocar_playlist_autodj_resultado_alerta']."</span>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_acao_trocar_playlist_autodj_resultado_alerta']."");
	
	}
	
	exit();
}

// Função para exibir a música atual e próxima música
if($acao == "musica_atual_proxima_musica") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = query_string('2');
	$limite_caracteres = query_string('3');
	
	$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	$host_sources = ($dados_config["usar_cdn"] == "sim") ? $dados_config["dominio_cdn"] : $_SERVER['HTTP_HOST'];
	
	if($dados_config["usar_cdn"] == "sim") {
	
	$musica_atual = file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&recurso=musica&ponto=1");
	
	$proxima_musica = file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&recurso=proxima_musica&ponto=1");
	
	} else {
	
	$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
	
	$musica_atual = $info["musica"];
	$proxima_musica = $info["proxima_musica"];
	
	}
	
	$musica_atual = (strlen($musica_atual) > $limite_caracteres) ? "".substr($musica_atual, 0, $limite_caracteres)."..." : $musica_atual;
	
	$proxima_musica = (strlen($proxima_musica) > $limite_caracteres) ? "".substr($proxima_musica, 0, $limite_caracteres)."..." : $proxima_musica;
	
	echo $musica_atual."|".$proxima_musica;
	
	exit();
	
}

// Função para remover um agendamento de playlist
if($acao == "remover_agendamento") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo = code_decode(query_string('2'),"D");
	
	$dados_agendamento = mysql_fetch_array(mysql_query("SELECT * FROM playlists_agendamentos where codigo = '".$codigo."'"));

	mysql_query("Delete From playlists_agendamentos where codigo = '".$dados_agendamento["codigo"]."'");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_agendamento_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_agendamento_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover a configuração do Hora Certa
if($acao == "remover_hora_certa_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_playlist["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	mysql_query("Delete From playlists_musicas where tipo = 'hc' AND codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Cria o arquivo da playlist para enviar para o servidor
	$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' ORDER by ordem+0 ASC");
	while ($dados_musica = mysql_fetch_array($query)) {

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
	
	// Marca como desativado na playlist
	mysql_query("Update playlists set hora_certa = 'nao' where codigo = '".$dados_playlist["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_playlists_remover_hora_certa_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."] </a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_playlists_remover_hora_certa_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para remover a configuração de Vinhetas & Comerciais
if($acao == "remover_vinhetas_comerciais_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$playlist = code_decode(query_string('2'),"D");
	
	$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist."'"));
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_playlist["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
	
	mysql_query("Delete From playlists_musicas where tipo = 'vinheta' AND codigo_playlist = '".$dados_playlist["codigo"]."'");
	mysql_query("Delete From playlists_musicas where tipo = 'comercial' AND codigo_playlist = '".$dados_playlist["codigo"]."'");
	
	if(!mysql_error()) {
	
	// Cria o arquivo da playlist para enviar para o servidor
	$query = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."' ORDER by ordem+0 ASC");
	while ($dados_musica = mysql_fetch_array($query)) {

	// Adiciona a música na lista para adicionar ao arquivo da playlist
	if($dados_musica["tipo"] == "hc") {
	$lista_musicas .= $dados_musica["path_musica"]."\n";
	} else {
	$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$dados_musica["path_musica"]."\n";
	}

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
	
	// Marca como desativado na playlist
	mysql_query("Update playlists set vinhetas_comerciais = 'nao' where codigo = '".$dados_playlist["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_playlists_remover_vinhetas_comerciais_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_playlists_remover_vinhetas_comerciais_resultado_erro']." ".mysql_error()."</span>";
	
	}
	
	exit();

}

// Função para criar player da musica(previa) Programentes
if($acao == "play_musicaM") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = code_decode(query_string('2'),"D");
	
	//$musica = (query_string('4')) ? query_string('3')."/".query_string('4') : query_string('3');
	$musica = query_string('4')."/".query_string('5');

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	echo '<div style="width:300px; height:62px; background-color:#000000; margin:0 auto; padding:0"">';
	echo '<img src="http://player.audiocast.ml/img/img-player-vu-meter-grande.gif" width="245" height="30" /><br />';
	echo '<audio controls autoplay><source src="http://'.$dados_servidor["ip"].':555/playP.php?porta='.$dados_servidor["portapro"].'&programete='.$musica.'" type="audio/mp3"></audio>';
	echo '</div>';
	
	exit();

}


// Função para criar player da musica(previa) Programentes
if($acao == "play_musicaP") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = code_decode(query_string('2'),"D");
	
	$musica = (query_string('4')) ? query_string('3')."/".query_string('4') : query_string('3');
	//$musica = query_string('4')."/".query_string('5');

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '8991'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	echo '<div style="width:300px; height:62px; background-color:#000000; margin:0 auto; padding:0"";>';
	echo '<img src="http://player.audiocast.ml/img/img-player-vu-meter-grande.gif" width="245" height="30" /><br />';
echo '<audio style="width:150px; height:40px; background-color:#f90; padding:15px 75px 1px 75px;" preload="none" controls autoplay ><source src="http://'.$dados_servidor["ip"].':555/playP.php?porta='.$dados_servidor["portapro"].'&musica='.$musica.'" type="audio/mp3"></audio>';

	echo '</div>';
	
	exit();

}


// Função para criar player da musica(previa)
if($acao == "play_musica") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$porta = code_decode(query_string('2'),"D");
	
	$musica = (query_string('4')) ? query_string('3')."/".query_string('4') : query_string('3');

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	echo '<div style="width:300px; height:62px; background-color:#000000; margin:0 auto; padding:0"">';
	echo '<img src="http://player.audiocast.ml/img/img-player-vu-meter-grande.gif" width="245" height="30" /><br />';
	//echo '<audio controls autoplay><source src="http://'.$dados_servidor["ip"].':555/play.php?porta='.$dados_stm["porta"].'&musica='.$musica.'" type="audio/mp3"></audio>';

echo '<audio style="width:150px; height:40px; background-color:#f90; padding:15px 75px 1px 75px;" preload="none" controls autoplay src="http://'.$dados_servidor["ip"].':555/play.php?porta='.$dados_stm["porta"].'&musica='.$musica.'">Seu navegador não tem suporte a HTML5</audio>';


	echo '</div>';
	
	exit();

}

// Função para duplicar(copiar) uma playlist
if($acao == "duplicar_playlist") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$playlist_atual = code_decode(query_string('2'),"D");
	$playlist_nova = query_string('3');

	$dados_playlist_atual = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$playlist_atual."'"));	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_playlist_atual["codigo_stm"]."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	$playlist_arquivo = "".$dados_stm["porta"]."-".formatar_nome_playlist($playlist_nova).".pls";

	mysql_query("INSERT INTO playlists (codigo_stm,nome,arquivo,data,hora_certa,vinhetas_comerciais) VALUES ('".$dados_stm["codigo"]."','".$playlist_nova."','".$playlist_arquivo."',NOW(),'".$dados_playlist_atual["hora_certa"]."','".$dados_playlist_atual["vinhetas_comerciais"]."')");
	$codigo_playlist = mysql_insert_id();
		
	$sql_playlist_atual_musicas = mysql_query("SELECT * FROM playlists_musicas where codigo_playlist = '".$dados_playlist_atual["codigo"]."'");
	while ($dados_musica_playlist_atual = mysql_fetch_array($sql_playlist_atual_musicas)) {
	
	// Adiciona música na playlist
	mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,duracao,duracao_segundos,tipo,ordem) VALUES ('".$codigo_playlist."','".addslashes($dados_musica_playlist_atual["path_musica"])."','".addslashes($dados_musica_playlist_atual["musica"])."','".$dados_musica_playlist_atual["duracao"]."','".$dados_musica_playlist_atual["duracao_segundos"]."','".$dados_musica_playlist_atual["tipo"]."','".$dados_musica_playlist_atual["ordem"]."')");

	// Adiciona a música na lista para adicionar ao arquivo da playlist
	$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$dados_musica_playlist_atual["path_musica"]."\n";

	}
	
	// Cria o arquivo da playlist para enviar ao servidor do streaming
	$config_playlist = gerar_playlist($playlist_arquivo,$lista_musicas);
	
	// Envia o arquivo da playlist para o servidor do streaming
	// Conexão SSH
	$ssh = new SSH();
	$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
	$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

	$ssh->enviar_arquivo("/home/painel/public_html/temp/".$config_playlist."","/home/streaming/playlists/".$playlist_arquivo."",0777);

	// Remove o arquivo temporário usado para criar a playlist
	unlink("/home/painel/public_html/temp/".$config_playlist."");
	
	if(!mysql_error()) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_gerenciador_playlists_duplicar_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_gerenciador_playlists_duplicar_resultado_erro']." ".mysql_error()."</span>";	
	
	}
	
	exit();

}
	
////////////////////////////////////////////////////////
//////////// Funções Gerenciamento Músicas /////////////
////////////////////////////////////////////////////////

// Função para criar nova pasta
if($acao == "criar_pasta") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$pasta = remover_acentos(query_string('3'));

	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);
	
	$resultado = $ftp->criar_pasta($pasta);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_criar_pasta_resultado_ok']."</span><br /><a href='javascript:carregar_pastas(\"".query_string('2')."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_criar_pasta_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para renomear uma pasta no FTP
if($acao == "renomear_pasta") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$antigo = query_string('3');
	$novo = query_string('4');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);
	
	$resultado = $ftp->renomear($antigo,$novo);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_renomear_pasta_resultado_ok']."</span><br /><a href='javascript:window.location.reload()' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_renomear_pasta_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para remover uma pasta
if($acao == "remover_pasta") {
	
	ini_set("max_execution_time", 1800);

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	$pasta = query_string('3');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);
	
	$resultado = $ftp->remover_pasta($pasta);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_pasta_resultado_ok']."</span><br /><a href='javascript:carregar_pastas(\"".query_string('2')."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_pasta_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para renomear uma musica no FTP
if($acao == "renomear_musica_ftp") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	list($pasta, $musica) = explode("|",query_string('3'));
	$novo = query_string('4');
	
	$pasta = ($pasta == "") ? '/' : $pasta;
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);
	
	$resultado = $ftp->renomear($pasta."/".$musica,$pasta."/".$novo.".mp3");
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_renomear_musica_resultado_ok']."</span><br /><a href='javascript:carregar_musicas_pasta(\"".query_string('2')."\",\"".$pasta."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_renomear_musica_resultado_erro']."</span>";
	
	}
	
	exit();

}

// Função para remover uma música no FTP
if($acao == "remover_musica_ftp") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	list($pasta, $musica) = explode("|",query_string('3'));
	
	$pasta = ($pasta == "") ? '/' : $pasta;
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
	$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

	// Conexão SSH
	$ftp = new FTP();
	$ftp->conectar($dados_servidor["ip"]);
	$ftp->autenticar($dados_stm["porta"],$dados_stm["senha"]);
	
	$resultado = $ftp->remover_arquivo($pasta."/".$musica);
	
	if($resultado) {
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_remover_musica_resultado_ok']."</span><br /><a href='javascript:carregar_musicas_pasta(\"".query_string('2')."\",\"".$pasta."\");' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_atualizar']."]</a>";
	
	} else {
	
	echo "<span class='texto_status_erro'>".$lang['lang_info_remover_musica_resultado_erro']."</span>";
	
	}
	
	exit();

}

//////////////////////////////////////////////////////
/////////// Funções Gerenciamento Painel /////////////
//////////////////////////////////////////////////////

// Função para exibir avisos
if($acao == "exibir_aviso") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$codigo_aviso = query_string('2');
	
	$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
	$dados_aviso = mysql_fetch_array(mysql_query("SELECT * FROM avisos where codigo = '".$codigo_aviso."'"));
	
	$area = ($_SESSION["porta_logada"]) ? 'streaming' : 'revenda';
	$codigo_usuario = ($area == "streaming") ? $_SESSION["porta_logada"] : $_SESSION["code_user_logged"];

	if(!mysql_error()) {
	
	echo "<div id=\"quadro\">
			<div id=\"quadro-topo\"><strong>".$lang['lang_info_aviso_titulo']."</strong></div>
				<div class=\"texto_padrao\" id=\"quadro-conteudo\">
				".$dados_aviso["mensagem"]."<br><br>
				<span class=\"texto_padrao_vermelho\">".$lang['lang_info_aviso_data']." ".formatar_data($dados_stm["formato_data"], $dados_aviso["data"], $dados_stm["timezone"])."</span><br>
				<span class=\"texto_padrao_pequeno\"><input type=\"checkbox\" onclick=\"desativar_exibicao_aviso('".$codigo_aviso."', '".$area."', '".$codigo_usuario."');\" style=\"vertical-align:middle;\" />&nbsp;".$lang['lang_info_aviso_desativar']."</span>
				</div>
		  </div>";
	
	}
	
	exit();

}

// Função para marcar um aviso como vizualizado
if($acao == "desativar_exibicao_aviso") {

	$codigo_aviso = query_string('2');
	$area = query_string('3');
	$codigo_usuario = query_string('4');
	
	mysql_query("INSERT INTO avisos_desativados (codigo_aviso,codigo_usuario,area,data) VALUES ('".$codigo_aviso."','".$codigo_usuario."','".$area."',NOW())");
	
	exit();

}

// Função para obter o domínio dos servidores CDN
if($acao == "get_host_cdn") {
	
	echo $dados_config["dominio_cdn"];

	exit();
	
}

// Função para ativar proteção contra ataques ao streaming
if($acao == "ativar_desativar_protecao") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	monitoramento_ataques();
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}

	$porta = code_decode(query_string('2'),"D");
	
	if($porta == "" || $porta == 0) {
	
	echo "<span class='texto_status_erro'>".$lang['lang_alerta_dados_faltando']."</span>";
	
	} else {
	
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
	
	if($dados_stm["protecao"] == "0") {	
	
	// Bloqueia o streaming no servidor
	$ssh->executar("iptables -A INPUT -p tcp --syn --dport ".$dados_stm["porta"]." -m connlimit --connlimit-above 5 -j REJECT --reject-with tcp-reset;service iptables save;echo ok");
	
	mysql_query("Update streamings set protecao = '1' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_ativar_desativar_protecao_ativado']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_info_ativar_desativar_protecao_ativado']."");
	
	} else {
	
	$ssh->executar("iptables -D INPUT -p tcp --syn --dport ".$dados_stm["porta"]." -m connlimit --connlimit-above 5 -j REJECT --reject-with tcp-reset;service iptables save;echo ok");
	
	mysql_query("Update streamings set protecao = '0' where codigo = '".$dados_stm["codigo"]."'");
	
	echo "<span class='texto_status_sucesso'>".$lang['lang_info_ativar_desativar_protecao_desativado']."</span><br /><br /><a href='javascript:void(0);' onClick='document.getElementById(\"log-sistema-fundo\").style.display = \"none\";document.getElementById(\"log-sistema\").style.display = \"none\";' class='texto_status_atualizar'>[".$lang['lang_botao_titulo_fechar']."]</a>";
	
	// Insere a ação executada no registro de logs.
	logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_info_ativar_desativar_protecao_desativado']."");
	
	}
	
	}
	
	exit();
}
?>
