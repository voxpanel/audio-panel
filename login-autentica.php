<?php
//////////////////////////////////////////////////////////////////
//////// Verifica Bloqueio por Tentativas de Acesso do IP ////////
//////////////////////////////////////////////////////////////////

$checar_bloqueio_ip = @mysql_num_rows(@mysql_query("SELECT * FROM bloqueios_login where ip = '".$_SERVER['REMOTE_ADDR']."' AND tentativas >= 2"));

if($checar_bloqueio_ip > 0) {

$_SESSION["status_login"] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_padrao_pequeno" style="padding-left: 5px; color: #923614;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>IP '.$_SERVER['REMOTE_ADDR'].' bloqueado, contate nosso atendimento!</strong>
  </td>
</tr>
</table>';

unset($_SESSION["porta_logada"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}

//////////////////////////////////////////////////////////////////

$_POST["porta"] = str_replace("'='","",$_POST["porta"]);
$_POST["senha"] = str_replace("'='","",$_POST["senha"]);

if($_POST["porta"] == '' || $_POST["senha"] == '') {

$_SESSION["status_login"] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Por favor informe a porta/senha de acesso</strong>
  </td>
</tr>
</table>';

unset($_SESSION["porta_logada"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta = '".anti_sql_injection($_POST["porta"])."' AND senha = '".anti_sql_injection($_POST["senha"])."'"));

if($valida_usuario == 1) {

$_SESSION["porta_logada"] = $_POST["porta"];

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings WHERE porta = '".$_SESSION["porta_logada"]."'"));

// Limpa as tentativas de logins frustradas anteriormente
@mysql_query("Delete From bloqueios_login where codigo = '".$dados_stm["codigo"]."'");

if(file_exists("inc/lang-".$dados_stm["idioma_painel"].".php")) {
require_once("inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

// Insere a ação executada no registro de logs.
logar_acao_streaming("".$dados_stm["codigo"]."","".$lang['lang_info_log_login_painel']."");

// Loga ultimo acesso da revenda
@mysql_query("Update streamings set ultimo_acesso_data = NOW(), ultimo_acesso_ip = '".$_SERVER['REMOTE_ADDR']."' WHERE porta = '".$_SESSION["porta_logada"]."'");

header("Location: http://".$_SERVER['HTTP_HOST']."/streaming");
exit;

} else { // Dados inválidos

//////////////////////////////////////////////////////////////////
//////////////// Grava a Tentativa de Acesso do IP ///////////////
//////////////////////////////////////////////////////////////////

$checar_stm_bloqueio = @mysql_num_rows(@mysql_query("SELECT * FROM streamings WHERE porta = '".anti_sql_injection($_POST["porta"])."'"));

if($checar_stm_bloqueio > 0) {

$checar_ip = @mysql_num_rows(@mysql_query("SELECT * FROM bloqueios_login where ip = '".$_SERVER['REMOTE_ADDR']."'"));

if($checar_ip == 0) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings WHERE porta = '".anti_sql_injection($_POST["porta"])."'"));

@mysql_query("INSERT INTO bloqueios_login (codigo_cliente,codigo_stm,data,ip,navegador,tentativas) VALUES ('".$dados_stm["codigo_cliente"]."','".$dados_stm["codigo"]."',NOW(),'".$_SERVER['REMOTE_ADDR']."','".formatar_navegador($_SERVER['HTTP_USER_AGENT'])."','1')");

} else {
@mysql_query("Update bloqueios_login set tentativas = tentativas+1 where ip = '".$_SERVER['REMOTE_ADDR']."'");
}

}

//////////////////////////////////////////////////////////////////

$_SESSION["status_login"] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Porta ou senha inválidos, tente novamente</strong>
  </td>
</tr>
</table>';

unset($_SESSION["porta_logada"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}
?>