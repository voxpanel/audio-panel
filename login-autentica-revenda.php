<?php
$dados_acesso = explode("@",query_string('1'));
$porta_X = code_decode($dados_acesso[0],"D");
$senha_Y = code_decode($dados_acesso[1],"D");

if($porta_X == '' || $senha_Y == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Dados incorretos ou inválidos.</strong>
  </td>
</tr>
</table>';

unset($_SESSION["porta_logada"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;
}

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE (porta = '".anti_sql_injection($porta_X)."' AND senha = '".anti_sql_injection($senha_Y)."') AND codigo_cliente = '".$_SESSION["code_user_logged"]."'"));

if($valida_usuario == 1) {

$_SESSION["porta_logada"] = $porta_X;

header("Location: http://".$_SERVER['HTTP_HOST']."/streaming");
exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
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