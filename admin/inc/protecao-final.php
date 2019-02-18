<?php
session_start();

require("admin/inc/conecta.php");

if($_SESSION["porta_logada"]) {

$valida_usuario = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta = '".$_SESSION["porta_logada"]."'"));

if($valida_usuario != 1) {

unset($_SESSION["porta_logada"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;

}

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="/admin/img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Sessão expirada faça logon novamente</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/login");
exit;

}
?>