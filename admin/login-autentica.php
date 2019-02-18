<?php
require_once("inc/classe.mail.php");

$x_email = anti_sql_injection($_POST["email"]);
$x_senha = anti_sql_injection($_POST["senha"]);

if(is_numeric($_POST["email"])) {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Por favor tente novamente.</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;

}

if($_POST["email"] == '' || $_POST["senha"] == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Por favor informe seu e-mail/senha de acesso</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;
}

if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST["email"])) {

$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE email = '".$x_email."' AND senha = PASSWORD('".$x_senha."')"));

if($valida_revenda == 1) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE email = '".$x_email."' AND senha = PASSWORD('".$x_senha."')"));

$_SESSION["type_logged_user"] = "cliente";
$_SESSION["code_user_logged"] = $dados_revenda["codigo"];

// Loga o acesso do usuario
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('login',NOW(),'".$_SERVER['REMOTE_ADDR']."','Revenda ".$dados_revenda["id"]." acessou sistema.')");

// Loga ultimo acesso da revenda
@mysql_query("Update revendas set ultimo_acesso_data = NOW(), ultimo_acesso_ip = '".$_SERVER['REMOTE_ADDR']."'  WHERE codigo = '".$dados_revenda["codigo"]."'");

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/revenda");
exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>E-mail ou senha inválidos, tente novamente.</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}

} else { // se nao for cliente é operador
/*
// Valida IP
$ip_liberado = shell_exec("cat /home/painel/public_html/acessos.txt");

if(!preg_match('/'.$_SERVER["REMOTE_ADDR"].'/i',$ip_liberado)) {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Permissão negada!</strong>
  </td>
</tr>
</table>';

envia_Email("site.com.br","587","contato@site.com.br","1q2w3e4r","contato@site.com.br","[Alerta] Tentativa de acesso admin painel streaming","Tentativa de acesso admin painel streaming\n\n".gethostbyaddr($_SERVER["REMOTE_ADDR"])."\n".date("d/m/Y H:i:s")."\n".$_SESSION["code_user_logged"]."\n".$_SESSION["type_logged_user"]."\n".$_POST["email"]."\n".$_POST["senha"]."",true);

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}
*/
$valida_operador = mysql_num_rows(mysql_query("SELECT * FROM administradores WHERE usuario = '".$x_email."' AND senha = PASSWORD('".$x_senha."')"));

if($valida_operador == 1) {

$dados_operador = mysql_fetch_array(mysql_query("SELECT * FROM administradores WHERE usuario = '".$x_email."'"));

$_SESSION["type_logged_user"] = "operador";
$_SESSION["code_user_logged"] = $dados_operador["codigo"];

// Loga o acesso do usuario
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('login',NOW(),'".$_SERVER['REMOTE_ADDR']."','Administrador ".$_POST["email"]." acessou sistema.')");


if($_SERVER['HTTP_REFERER']) {

$pagina_inicial = str_replace("login","admin-configuracoes",$_SERVER['HTTP_REFERER']);
$pagina_inicial = str_replace("sair","admin-configuracoes",$pagina_inicial);

header("Location: ".$pagina_inicial."");
} else {
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/admin-configuracoes");
}

exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Usuário ou senha inválidos, tente novamente</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}

}
?>