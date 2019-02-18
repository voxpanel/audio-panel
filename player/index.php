<?php
require_once("inc/conecta-remoto.php");
require_once("inc/funcoes.php");

//////////////////////////////////////////////////////////////////
////////////////////// Domнnio Prуprio Site //////////////////////
//////////////////////////////////////////////////////////////////
/*
if(!preg_match('/player./i',$_SERVER['HTTP_HOST']) && query_string('0') != "site") {

$resultado_busca = shell_exec("grep -H ".$_SERVER['HTTP_HOST']." cache/site-*.txt | cut -d : -f 1");

$porta = str_replace("cache/","",$resultado_busca);
$porta = str_replace("site-","",$porta);
$porta = str_replace(".txt","",$porta);

echo file_get_contents("http://localhost/site/".$porta."");
exit();
}
*/
//////////////////////////////////////////////////////////////////
//////////////////////////// Navegaзгo ///////////////////////////
//////////////////////////////////////////////////////////////////

$pagina = query_string('0');

if ($pagina == "") {
die("Acesso Negado! Access Denied!");
}

if (!file_exists($pagina.".php")) {
die("Acesso Negado! Access Denied!");
}

require("".$pagina.".php");
?>