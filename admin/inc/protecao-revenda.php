<?php
session_start();
require("conecta.php");

if($_SESSION["code_user_logged"] == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="/admin/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Sessão expirada faça logon novamente</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;
}

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

if($valida_usuario != 1) {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="/admin/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Sessão expirada faça logon novamente</strong>
  </td>
</tr>
</table>';

unset($_SESSION["type_logged_user"]);
unset($_SESSION["code_user_logged"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/admin/login");
exit;

}
?>