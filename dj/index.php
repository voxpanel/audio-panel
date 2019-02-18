<?php
session_start();

require_once("../admin/inc/conecta.php");
require_once("../admin/inc/funcoes.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

// Verifica se painel esta com manutenчуo ativada e entуo exibe a pсgina de manutenчуo
if($dados_config["manutencao"] == "sim") {

require("manutencao.php");

exit();

}

//////////////////////////////////////////////////////////////////
//////////////////////// Idioma do Painel ////////////////////////
//////////////////////////////////////////////////////////////////

if($_SESSION["dj_logado"]) {

list($codigo_stm, $dj_login, $dj_senha) = explode("|",$_SESSION["dj_logado"]);

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$codigo_stm."'"));

if(file_exists("../inc/lang-".$dados_stm["idioma_painel"].".php")) {
require_once("../inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("../inc/lang-pt-br.php");
}

}

//////////////////////////////////////////////////////////////////
//////////////////////////// Navegaчуo ///////////////////////////
//////////////////////////////////////////////////////////////////

// Navegaчуo
$pagina = query_string('1');

if($pagina == "sair") {

$pagina = "login";

unset($_SESSION["dj_logado"]);
}

if ($pagina == "") {
require("login.php");
} elseif (!file_exists($pagina.".php")) {
require("login.php");
} else {
require("".$pagina.".php");
}
?>