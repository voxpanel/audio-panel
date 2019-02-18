<?php
session_start();

require("../admin/inc/conecta.php");

if($_SESSION["dj_logado"]) {

list($codigo_stm, $login_dj, $senha_dj) = explode("|",$_SESSION["dj_logado"]);

$valida_dj = mysql_num_rows(mysql_query("SELECT * FROM djs WHERE codigo_stm = '".$codigo_stm."' AND login = '".$login_dj."' AND senha = '".$senha_dj."'"));

if($valida_dj != 1) {

unset($_SESSION["dj_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/dj/login");
exit;

}

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#FFFF66" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Sessão expirada faça logon novamente</strong>
  </td>
</tr>
</table>';

header("Location: http://".$_SERVER['HTTP_HOST']."/dj/login");
exit;

}
?>