<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

ini_set("memory_limit", "128M");
ini_set("max_execution_time", 600);

// Funções gerais para uso com Ajax

$acao = query_string('1');

////////////////////////////////////////
/////////// Funções Players ////////////
////////////////////////////////////////


// Função para exibir a música atual tocando no streaming
if($acao == "musica_atual") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$servidor = query_string('2');
	$porta = query_string('3');
	$limite_caracteres = query_string('4');
	
	$musica = @file_get_contents("http://".$servidor.":".$porta."/currentsong?sid=1");
	
	$musica = (preg_match('/feminino_/i',$musica)) ? "Hora Certa" : $musica;
	$musica = (preg_match('/masculino_/i',$musica)) ? "Hora Certa" : $musica;
	
	$musica = str_replace("_"," ",$musica);
	$musica = ucwords(strtolower($musica));
	$musica = utf8_decode($musica);
	
	if(!$musica) {
	
	$dados_servidor_shoutcast = shoutcast_info($servidor,$porta,1);
	
	die($dados_servidor_shoutcast["titulo"]);
	}
		
	if(strlen($musica) > $limite_caracteres) {
	echo substr($musica, 0, $limite_caracteres)."...";
	} else {
	echo $musica;
	}
	
	exit();
	
}

// Função para exibir a música atual tocando no streaming
if($acao == "capa_musica_atual") {

	// Proteção contra acesso direto
	if(!preg_match("/".str_replace("http://","",str_replace("www.","",$_SERVER['HTTP_HOST']))."/i",$_SERVER['HTTP_REFERER'])) {
	die("<span class='texto_status_erro'>0x001 - Atenção! Acesso não autorizado, favor entrar em contato com nosso atendimento para maiores informações!</span>");
	}
	
	$servidor = query_string('2');
	$porta = query_string('3');
	
	$musica_completa = @file_get_contents("http://".$servidor.":".$porta."/currentsong?sid=1");
	
	$musica_completa = str_replace("_"," ",$musica_completa);
	$musica_completa = ucwords(strtolower($musica_completa));
	$musica_completa = utf8_decode($musica_completa);
	
	$musica_completa = str_replace(" e "," & ",$musica_completa);
	$musica_completa = str_replace(" E "," & ",$musica_completa);
	
	if(!$musica_completa) {
	die("https://player.srvstm.com/img/img-capa-artista-padrao.png");
	}
	
	if(preg_match('/feminino_/i',$musica_completa)) {
	die("https://player.srvstm.com/img/img-capa-artista-relogio.png");
	}
	
	if(preg_match('/masculino_/i',$musica_completa)) {
	die("https://player.srvstm.com/img/img-capa-artista-relogio.png");
	}
	
	$musica_partes = explode("-",$musica_completa);

	$artista = str_replace("_"," ",$musica_partes[0]);
	$artista = rtrim($artista, " \t.");
	
	$musica = $musica_partes[1];

	$resultado_api_lastfm_vagalume = lastfm('artist',$artista);
	
	if($resultado_api_lastfm_vagalume["status"] == "ok") {
	$imagem = $resultado_api_lastfm_vagalume["imagem"];
	} else {
	$resultado_api_lastfm_vagalume = vagalumeapi('capa2',$artista,$musica);
	}
	
	if($resultado_api_lastfm_vagalume["status"] == "ok") {
	$imagem = $resultado_api_lastfm_vagalume["imagem"];
	} else {
	$resultado_api_lastfm_vagalume = vagalumeapi('capa1',$artista,'');
	}

	if($resultado_api_lastfm_vagalume["status"] == "ok") {
	
	if(strlen($resultado_api_lastfm_vagalume["imagem"]) > 10) {
	echo $imagem = $resultado_api_lastfm_vagalume["imagem"];
	} else {
	echo "https://player.srvstm.com/img/img-capa-artista-padrao.png";
	}
	
	} else {
	echo "https://player.srvstm.com/img/img-capa-artista-padrao.png";
	}
	
	exit();
	
}
?>