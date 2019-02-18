#!/usr/bin/php
<?php
ini_set("memory_limit", "1024M");
ini_set("max_execution_time", 3600);

require_once("getid3/getid3.php");

// Função para calcular tempo de exceussão
function tempo_execucao() {
    $sec = explode(" ",microtime());
    $tempo = $sec[1] + $sec[0];
    return $tempo;
}

$inicio_execucao = tempo_execucao();

$getID3 = new getID3;

$streamings = new DirectoryIterator('/home/streaming/');

foreach($streamings as $streaming) {
if(!$streaming->isDot() && $streaming->isDir()) {

$pasta_streaming = $streaming->getFilename();

if(is_numeric($pasta_streaming)) {

echo "Porta: ".$pasta_streaming."\n";

$pastas_stm = new DirectoryIterator('/home/streaming/'.$pasta_streaming);

foreach($pastas_stm as $dir_stm) {
if(!$dir_stm->isDot() && $dir_stm->isDir()) {

$pasta = $dir_stm->getFilename();

$path_pasta_stm = realpath('/home/streaming/'.$pasta_streaming.'/'.$pasta.'');

$dir_stm_musicas = new DirectoryIterator($path_pasta_stm);

foreach($dir_stm_musicas as $dir_stm_musica) {
	if($dir_stm_musica->isFile()) {
		if(pathinfo($dir_stm_musica->getFilename(), PATHINFO_EXTENSION) == "mp3" || pathinfo($dir_stm_musica->getFilename(), PATHINFO_EXTENSION) == "MP3") {
			$array_musicas[] = $path_pasta_stm."/".$dir_stm_musica->getFilename();
		}
	}

}
}
}

if(count($array_musicas) > 0) {

foreach($array_musicas as $musica) {

// Hash musica
$hash_musica_atual = md5_file($musica);

// Verifica se a musica esta no cache
if (!file_exists('/home/streaming/'.$pasta_streaming.'/.cache_musicas')) {
@shell_exec('/bin/touch /home/streaming/'.$pasta_streaming.'/.cache_musicas');
@shell_exec('/bin/chown streaming.streaming /home/streaming/'.$pasta_streaming.'/.cache_musicas');
}

$lines = file('/home/streaming/'.$pasta_streaming.'/.cache_musicas');
foreach($lines as $line)
{
  if(strpos($line, $hash_musica_atual) !== false)
    list($hash_musica_cache, $duracao_segundos) = explode("|",$line);
}

if(empty($duracao_segundos)) {

$musica_info = $getID3->analyze($musica);
$duracao_segundos = round($musica_info['playtime_seconds']);

// Inclui a musica no cache
$cache_file = fopen('/home/streaming/'.$pasta_streaming.'/.cache_musicas', "a");
fwrite($cache_file, "\n". "".$hash_musica_atual."|".$duracao_segundos."");
fclose($cache_file);
}

unset($musica_info);
unset($duracao_segundos);

}

}

}

}

}

$fim_execucao = tempo_execucao();

$tempo_execucao = number_format(($fim_execucao-$inicio_execucao),2);

echo "\n\n--------------------------------------------------------------------\n\n";
echo "Tempo: ".$tempo_execucao." segundo(s);\n\n";
?>