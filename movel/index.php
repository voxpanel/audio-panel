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

if($_SESSION["porta_logada"]) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));

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

unset($_SESSION["porta_logada"]);
}

if ($pagina == "") {
require("login.php");
} elseif (!file_exists($pagina.".php")) {
require("login.php");
} else {
require("".$pagina.".php");
}
?>