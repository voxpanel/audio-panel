<?php
require_once("inc/classe.mail.php");

if($_POST["portas"]) {

$total = 0;

foreach($_POST["portas"] as $email) {

if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

$resultado = envia_Email($dados_revenda["smtp_servidor"],$dados_revenda["smtp_porta"],$dados_revenda["smtp_email"],$dados_revenda["smtp_senha"],$dados_revenda["email"],$_POST["assunto"],$_POST["mensagem"],true);

// Loga a aчуo
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('envio_mensagem',NOW(),'".$_SERVER['REMOTE_ADDR']."','Envio de mensagem para ".$email." ')");

if(!$resultado) {

$total++;

}

}

}

// Cria o sessуo do status das aчѕes executadas e redireciona.
$_SESSION["status_acao"] = status_acao(lang_info_pagina_enviar_email_resultado_ok_mass,"ok");

header("Location: /admin/revenda-informacoes");

} else {

if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST["email"])) {

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

$resultado = envia_Email($dados_revenda["smtp_servidor"],$dados_revenda["smtp_porta"],$dados_revenda["smtp_email"],$dados_revenda["smtp_senha"],$dados_revenda["email"],$_POST["assunto"],$_POST["mensagem"],true);

// Loga a aчуo
mysql_query("INSERT INTO logs (acao,data,ip,log) VALUES ('envio_mensagem',NOW(),'".$_SERVER['REMOTE_ADDR']."','Envio de mensagem para ".$_POST["email"]." ')");

if(!$resultado) {

// Cria o sessуo do status das aчѕes executadas e redireciona.
$_SESSION["status_acao"] = status_acao(lang_info_pagina_enviar_email_resultado_ok,"ok");

} else {

// Cria o sessуo do status das aчѕes executadas e redireciona.
$_SESSION["status_acao"] = status_acao(lang_info_pagina_enviar_email_resultado_erro,"erro");

}

header("Location: /admin/revenda-enviar-email/".$_POST["porta"]."");

}

}
?>