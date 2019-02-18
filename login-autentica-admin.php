<?php
$dados_acesso = explode("@",query_string('1'));
$porta = code_decode($dados_acesso[0],"D");
$senha = code_decode($dados_acesso[1],"D");

if($porta == '' || $senha == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
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

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta = '".anti_sql_injection($porta)."' AND senha = '".anti_sql_injection($senha)."'"));

if($valida_usuario == 1) {

$_SESSION["porta_logada"] = $porta;

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