<?php
require_once("inc/classe.mail.php");

if($_POST["passo1"]) {

$x_email = anti_sql_injection($_POST["email"]);

if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST["email"])) {

$valida_revenda = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE email = '".$x_email."'"));

if($valida_revenda == 1) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE email = '".$x_email."'"));

// Verifica se a revenda tem SMTP configurado
if($dados_revenda["smtp_servidor"] && $dados_revenda["smtp_porta"] && $dados_revenda["smtp_email"] && $dados_revenda["smtp_senha"]) {

$codigo_validacao = code_decode($dados_revenda["email"],"E");

$mensagem = "=========================================\n";
$mensagem .= "========= Recuperação de Senha ==========\n";
$mensagem .= "=========================================\n";
$mensagem .= "ID: ".$dados_revenda["id"]."\n";
$mensagem .= "Nome: ".$dados_revenda["nome"]."\n";
$mensagem .= "E-mail: ".$dados_revenda["email"]."\n";
$mensagem .= "Data da Solicitação: ".date("d/m/Y H:i:s")."\n\n";
$mensagem .= "Para recuperar sua senha, acesse o link abaixo em seu navegador:\n\n";
$mensagem .= "http://".$_SERVER['HTTP_HOST']."/admin/login-recuperar-senha-validacao/".$codigo_validacao."\n\n";
$mensagem .= "IP: ".$_SERVER['REMOTE_ADDR']."\n";
$mensagem .= "=========================================";

envia_Email($dados_revenda["smtp_servidor"],$dados_revenda["smtp_porta"],$dados_revenda["smtp_email"],$dados_revenda["smtp_senha"],$dados_revenda["email"],"Recuperação de Senha",$mensagem,false);

// Loga o acesso do usuario
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('login',NOW(),'".$_SERVER['REMOTE_ADDR']."','Recuperação de senha da revenda ".$_POST["email"]." ')");

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Instruções de recuperação enviadas para o e-mail.</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Esta revenda não possui um SMTP configurado.</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login-recuperar-senha");
exit;

}

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>E-mail não cadastrado, tente novamente.</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login-recuperar-senha");
exit;

}

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>E-mail inválido, digite corretamente.</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login-recuperar-senha");
exit;

}

}

if($_POST["passo2"]) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where email = '".code_decode($_POST["validacao"],"D")."'")) or die(mysql_error());

mysql_query("Update revendas set senha = PASSWORD('".$_POST["nova_senha"]."'), alterar_senha = '1' where codigo = '".$dados_revenda["codigo"]."'") or die(mysql_error());

// Loga o acesso do usuario
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('login',NOW(),'".$_SERVER['REMOTE_ADDR']."','Recuperação de senha da revenda ".$_POST["email"]." concluída.')");

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Senha alterada com sucesso.</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}
?>