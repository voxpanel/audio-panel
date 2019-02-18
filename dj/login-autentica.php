<?php
if($_POST["porta"] == '' || $_POST["login_dj"] == '' || $_POST["senha_dj"] == '') {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_alerta" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Por favor informe a porta/senha DJ de acesso</strong>
  </td>
</tr>
</table>';

unset($_SESSION["dj_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/dj/login");
exit;
}

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".anti_sql_injection($_POST["porta"])."'"));

$valida_dj = mysql_num_rows(mysql_query("SELECT * FROM djs WHERE codigo_stm = '".$dados_stm["codigo"]."' AND login = '".anti_sql_injection($_POST["login_dj"])."' AND senha = '".anti_sql_injection($_POST["senha_dj"])."'"));

if($valida_dj == 1) {

$_SESSION["dj_logado"] = $dados_stm["codigo"]."|".anti_sql_injection($_POST["login_dj"])."|".anti_sql_injection($_POST["senha_dj"]);

header("Location: http://".$_SERVER['HTTP_HOST']."/dj/streaming");
exit;

} else {

$_SESSION[status_login] = '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td width="100%" height="25" bgcolor="#F2BBA5" class="texto_log_sistema_erro" style="padding-left: 5px;" scope="col" align="left">
<img src="img/icones/atencao.png" align="absmiddle">&nbsp;<strong>Porta ou login/senha DJ inválidos, tente novamente</strong>
  </td>
</tr>
</table>';

unset($_SESSION["dj_logado"]);
header("Location: http://".$_SERVER['HTTP_HOST']."/dj/login");
exit;
}
?>