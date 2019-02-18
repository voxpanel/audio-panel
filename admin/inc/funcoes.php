<?php
// Fun��es de uso no painel

////////////////////////////////////////
//////////// Fun��es Gerais ////////////
////////////////////////////////////////

// Fun��o para gerenciar query string
function query_string($posicao='0') {

$gets = explode("/",str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
array_shift($gets);

return utf8_decode(urldecode($gets[$posicao]));

}

// Fun��o para codificar e decodificar strings
function code_decode($texto, $tipo = "E") {

  if($tipo == "E") {
  
  $sesencoded = $texto;
  $num = mt_rand(0,3);
  for($i=1;$i<=$num;$i++)
  {
     $sesencoded = base64_encode($sesencoded);
  }
  $alpha_array = array('1','Z','3','R','1','Y','2','N','A','T','Z','X','A','E','Y','6','9','4','F','S','X');
  $sesencoded =
  $sesencoded."+".$alpha_array[$num];
  $sesencoded = base64_encode($sesencoded);
  return $sesencoded;
  
  } else {
  
   $alpha_array = array('1','Z','3','R','1','Y','2','N','A','T','Z','X','A','E','Y','6','9','4','F','S','X');
   $decoded = base64_decode($texto);
   list($decoded,$letter) = explode("+",$decoded);
   for($i=0;$i<count($alpha_array);$i++)
   {
   if($alpha_array[$i] == $letter)
   break;
   }
   for($j=1;$j<=$i;$j++)
   {
      $decoded = base64_decode($decoded);
   }
   return $decoded;
  }
}

// Fun��o para gerar ID da revenda
function gera_id($max = 6) {

$aux = microtime();
$id = substr(md5($aux),0,$max);

return $id;
}

// Fun��o para criar c�lulas de logs do sistema
function status_acao($status,$tipo) {

if($tipo == 'ok') {
$celula_status = '<tr style="background-color:#A6EF7B;">
      <td width="790" height="35" class="texto_log_sistema" scope="col">
	  <div align="center">'.$status.'</div>
	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
} elseif($tipo == 'ok2') {
$celula_status = '<tr style="background-color:#A6EF7B;">
      <td width="790" height="35" class="texto_log_sistema" scope="col">
	  <div align="center">'.$status.'</div>
	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
} elseif($tipo == 'alerta') {
$celula_status = '<tr style="background-color:#FFFF66;">
      <td width="790" height="35" class="texto_log_sistema_alerta" scope="col">
	  <div align="center">'.$status.'</div>

	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
} else {
$celula_status = '<tr style="background-color:#F2BBA5;">
      <td width="790" height="35" class="texto_log_sistema_erro" scope="col">
	  <div align="center">'.$status.'</div>
	  </td>
</tr>
<tr><td scope="col" height="2" width="770"></td></tr>
';
}  

return $celula_status;
}

// Fun��o para remover acentos e espa�os
function formatar_nome_playlist($playlist) {

$array_caracteres = array("/[�����]/"=>"a","/[�����]/"=>"a","/[����]/"=>"e","/[����]/"=>"e","/[����]/"=>"i","/[����]/"=>"i","/[�����]/"=>"o", "/[�����]/"=>"o","/[����]/"=>"u","/[����]/"=>"u","/�/"=>"c","/�/"=> "c","/ /"=> "-","/_/"=> "-");

$formatado = preg_replace(array_keys($array_caracteres), array_values($array_caracteres), $playlist);

return strtolower($formatado);
}

// Fun��o para formatar texto retirando acentos e caracteres especiais
function formatar_texto($texto) {

$characteres = array(
    'S'=>'S', 's'=>'s', '�'=>'Dj','Z'=>'Z', 'z'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A',
    '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I',
    '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U', '�'=>'U',
    '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss','�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a',
    '�'=>'a', '�'=>'a', '�'=>'c', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i',
    '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'u',
    '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b', '�'=>'y', 'f'=>'f', '�'=> '', '�'=> '', '&'=> 'e',
	'�'=> '', '�'=> '', '$'=> '', '%'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', 'ã'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> ''
);

return strtr($texto, $characteres);

}

function pais_ip($ip,$tipo) {

$paises = array("AF" => "Afeganist�o", "AL" => "Alb�nia", "DE" => "Alemanha", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Ant�rctida", "AG" => "Antigua e Barbuda", "AN" => "Antilhas Holandesas", "SA" => "Ar�bia Saudita", "AR" => "Argentina", "DZ" => "Arg�lia", "AM" => "Arm�nia", "AW" => "Aruba", "AU" => "Austr�lia", "AZ" => "Azerbeij�o", "ZA" => "�frica do Sul", "AT" => "�ustria", "AX" => "�land", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BE" => "B�lgica", "BY" => "Bielo-R�ssia", "BO" => "Bol�via", "BW" => "Botswana", "BV" => "Bouvet", "BA" => "B�snia-Herzegovina", "BR" => "Brasil", "BN" => "Brunei", "BG" => "Bulg�ria", "BF" => "Burkina Faso", "BI" => "Burundi", "BT" => "But�o", "CV" => "Cabo Verde", "CM" => "Camar�es", "KH" => "Cambodja", "CA" => "Canad�", "KY" => "Cayman", "KZ" => "Cazaquist�o", "CF" => "Centro-africana, Rep�blica", "TD" => "Chade", "CZ" => "Rep�blica Checa", "CL" => "Chile", "CN" => "China", "CY" => "Chipre", "CX" => "Christmas", "CC" => "Cocos", "CO" => "Col�mbia", "KM" => "Comores", "CD" => "Rep�blica Democr�tica do Congo", "CG" => "Rep�blica do Congo", "CK" => "Cook", "KR" => "Coreia do Sul", "KP" => "Coreia do Norte", "CI" => "Costa do Marfim", "CR" => "Costa Rica", "HR" => "Cro�cia", "CU" => "Cuba", "DK" => "Dinamarca", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Rep�blica Dominicana", "EG" => "Egito", "SV" => "El Salvador", "AE" => "Emiratos �rabes", "EC" => "Equador", "ER" => "Eritreia", "SK" => "Eslov�quia", "SI" => "Eslov�nia", "ES" => "Espanha", "US" => "Estados Unidos", "EE" => "Est�nia", "ET" => "Eti�pia", "FO" => "Faroe", "FJ" => "Fiji", "PH" => "Filipinas", "FI" => "Finl�ndia", "FR" => "Fran�a", "GA" => "Gab�o", "GH" => "Gana", "GM" => "G�mbia", "GE" => "Ge�rgia", "GS" => "Ge�rgia do Sul", "GI" => "Gibraltar", "GD" => "Grenada", "GR" => "Gr�cia", "GL" => "Gronel�ndia", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GY" => "Guiana", "GF" => "Guiana Francesa", "GQ" => "Guin� Equatorial", "GW" => "Guin�-Bissau", "GN" => "Guin�-Conacri", "HT" => "Haiti", "HM" => "Heard e Ilhas McDonald", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungria", "YE" => "I�men", "ID" => "Indon�sia", "IQ" => "Iraque", "IR" => "Ir�o", "IE" => "Irlanda", "IS" => "Isl�ndia", "IL" => "Israel", "IT" => "It�lia", "IN" => "�ndia", "JM" => "Jamaica", "JP" => "Jap�o", "JE" => "Jersey", "JO" => "Jord�nia", "KI" => "Kiribati", "KW" => "Kuwait", "LA" => "Laos", "LS" => "Lesoto", "LV" => "Let�nia", "LR" => "Lib�ria", "LI" => "Liechtenstein", "LT" => "Litu�nia", "LB" => "L�bano", "LY" => "L�bia", "LU" => "Luxemburgo", "MO" => "Macau", "MK" => "Maced�nia, Rep�blica da", "MG" => "Madag�scar", "MW" => "Malawi", "MY" => "Mal�sia", "MV" => "Maldivas", "ML" => "Mali", "MT" => "Malta", "FK" => "Malvinas (Falkland)", "IM" => "Man", "MP" => "Marianas Setentrionais", "MA" => "Marrocos", "MH" => "Marshall", "MQ" => "Martinica", "MR" => "Maurit�nia", "MU" => "Maur�cia", "YT" => "Mayotte", "UM" => "Menores Distantes dos Estados Unidos", "MX" => "M�xico", "FM" => "Micron�sia", "MZ" => "Mo�ambique", "MD" => "Mold�via", "MN" => "Mong�lia", "ME" => "Montenegro", "MS" => "Montserrat", "MC" => "M�naco", "MM" => "Myanmar", "NA" => "Nam�bia", "NR" => "Nauru", "NP" => "Nepal", "NI" => "Nicar�gua", "NG" => "Nig�ria", "NU" => "Niue", "NE" => "N�ger", "NF" => "Norfolk", "NO" => "Noruega", "NC" => "Nova Caled�nia", "NZ" => "Nova Zel�ndia", "OM" => "Oman", "NL" => "Holanda", "PW" => "Palau", "PS" => "Palestina", "PA" => "Panam�", "PG" => "Papua-Nova Guin�", "PK" => "Paquist�o", "PY" => "Paraguai", "PE" => "Peru", "PN" => "Pitcairn", "PF" => "Polin�sia Francesa", "PL" => "Pol�nia", "PR" => "Porto Rico", "PT" => "Portugal", "QA" => "Qatar", "KE" => "Qu�nia", "KG" => "Quirguist�o", "GB" => "Reino Unido da Gr�-Bretanha e Irlanda do Norte", "RE" => "Reuni�o", "RO" => "Rom�nia", "RW" => "Ruanda", "RU" => "R�ssia", "EH" => "Saara Ocidental", "PM" => "Saint Pierre et Miquelon", "SB" => "Salom�o", "WS" => "Samoa (Samoa Ocidental)", "AS" => "Samoa Americana", "SM" => "San Marino", "SH" => "Santa Helena", "LC" => "Santa L�cia", "KN" => "S�o Crist�v�o e N�vis (Saint Kitts e Nevis)", "ST" => "S�o Tom� e Pr�ncipe", "VC" => "S�o Vicente e Granadinas", "SN" => "Senegal", "SL" => "Serra Leoa", "SC" => "Seychelles", "RS" => "S�rvia", "SG" => "Singapura", "SY" => "S�ria", "SO" => "Som�lia", "LK" => "Sri Lanka", "SZ" => "Suazil�ndia", "SD" => "Sud�o", "SE" => "Su�cia", "CH" => "Su��a", "SR" => "Suriname", "SJ" => "Svalbard e Jan Mayen", "TH" => "Tail�ndia", "TW" => "Taiwan", "TJ" => "Tajiquist�o", "TZ" => "Tanz�nia", "TF" => "Terras Austrais e Ant�rticas Francesas", "IO" => "Territ�rio Brit�nico do Oceano �ndico", "TL" => "Timor-Leste", "TG" => "Togo", "TO" => "Tonga", "TK" => "Toquelau", "TT" => "Trindade e Tobago", "TN" => "Tun�sia", "TC" => "Turks e Caicos", "TM" => "Turquemenist�o", "TR" => "Turquia", "TV" => "Tuvalu", "UA" => "Ucr�nia", "UG" => "Uganda", "UY" => "Uruguai", "UZ" => "Usbequist�o", "VU" => "Vanuatu", "VA" => "Vaticano", "VE" => "Venezuela", "VN" => "Vietname", "VI" => "Virgens Americanas", "VG" => "Virgens Brit�nicas", "WF" => "Wallis e Futuna", "ZM" => "Z�mbia", "ZW" => "Zimbabwe");

$pais_ip = geoip_country_code_by_name($ip);

$array_prefixos_ips_brasil = array("177","179","186","187","189","191");

list($ip_parte1, $ip_parte2, $ip_parte3, $ip_parte4) = explode(".",$ip);

if($tipo == "nome") {

if($paises[$pais_ip]) {
return $paises[$pais_ip];
} else {

if (in_array($ip_parte1, $array_prefixos_ips_brasil)) {
return $paises["BR"];
} else {
return "IP sem informa��es de localiza��o";
}

}

} else {

if($pais_ip) {
return $pais_ip;
} else {

if (in_array($ip_parte1, $array_prefixos_ips_brasil)) {
return "BR";
} else {
return "Desconhecido";
}

}

}

}

// Fun��o para remover acentos
function remover_acentos($msg) {
$a = array("/[�����]/"=>"A","/[�����]/"=>"a","/[����]/"=>"E","/[����]/"=>"e","/[����]/"=>"I","/[����]/"=>"i","/[�����]/"=>"O",	"/[�����]/"=>"o","/[����]/"=>"U","/[����]/"=>"u","/�/"=>"c","/�/"=> "C");

return preg_replace(array_keys($a), array_values($a), $msg);
}

// Fun��o para formatar os segundos em segundos, minutos e horas
function tempo_conectado($segundos) {

$days=intval($segundos/86400);
$remain=$segundos%86400;
$hours=intval($remain/3600);
$remain=$remain%3600;
$mins=intval($remain/60);
$secs=$remain%60;
if (strlen($mins)<2) {
$mins = '0'.$mins;
}
if($days > 0) $dia = $days.'d';
if($hours > 0) $hora = $hours.'hr, ';
if($mins > 0) $minuto = $mins.'min, ';

$segundo = $secs.'seg';
$segundos = $dia.$hora.$minuto.$segundo;

return $segundos;

}

function seconds2time($segundos) {

return @gmdate("H:i:s", round($segundos));

}

// Fun��o para retornar o tipo de medida do tamanho do arquivo(Byts, Kbytes, Megabytes, Gigabytes, etc...)
function tamanho($size)
{
    $filesizename = array(" MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return $size ? round($size/pow(1000, ($i = floor(log($size, 1000)))), 2) . $filesizename[$i] : '0 Bytes';
}

// Fun��o para criar um barra de porcentagem de uso do plano
function barra_uso_plano($porcentagem,$descricao) {

$porcentagem_progresso = ($porcentagem > 100) ? "100" : $porcentagem;

$cor = "#00CC00";
$cor = ($porcentagem_progresso > 50 && $porcentagem_progresso < 80) ? "#FFE16C" : $cor;
$cor = ($porcentagem_progresso > 80) ? "#FF0000" : $cor;

return "<div class='barra-uso-plano-corpo' title='".$descricao."'>
<div class='barra-uso-plano-progresso' style='background-color: ".$cor."; width: ".round($porcentagem_progresso)."%;'>
<div class='barra-uso-plano-texto'>".round($porcentagem)."%</div>
</div>
</div>";

}

// Fun��o para calcular tempo de exceuss�o
function tempo_execucao() {
    $sec = explode(" ",microtime());
    $tempo = $sec[1] + $sec[0];
    return $tempo;
}

function anti_sql_injection($str) {
    if (!is_numeric($str)) {
        $str = get_magic_quotes_gpc() ? stripslashes($str) : $str;
        $str = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($str) : mysql_escape_string($str);
    }
    return $str;
}

function zebrar($i) {
    return func_get_arg(abs($i) % (func_num_args() - 1) + 1);
}

function anti_hack_dominio($lista_bloqueados) {

$dominio = str_replace("www.","",$_SERVER['HTTP_HOST']);

if(preg_grep('/'.$dominio.'/i',$lista_bloqueados)) {

die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Manuten��o</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="http://audiocast.ml/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:200px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
<td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
<td width="870" align="left" class="texto_status_erro" scope="col">Oops! A p&aacute;gina que tentou acessar esta em manuten&ccedil;&atilde;o, volte em alguns minutos.</td>
</tr>
</table>
</body>
</html>');

}

}

function anti_hack_ip($lista_bloqueados) {

if(!array_search($_SERVER['REMOTE_ADDR'], $lista_bloqueados)) {

die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Acesso Bloqueado para '.$_SERVER['REMOTE_ADDR'].'</title>
<meta http-equiv="cache-control" content="no-cache">
<link href="http://audiocast.ml/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:200px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
<td width="30" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
<td width="870" align="left" class="texto_status_erro" scope="col">Oops! foram registrados tentativas de ataques vindo de seu endere�o IP e por seguran�a nosso firewall efetuou bloqueio de acesso.<br><br>Por favor contate nosso atendimento.</td>
</tr>
</table>
</body>
</html>');

}

}

// Fun��o para conectar a uma URL
function conectar_url($url) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)');
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return $resultado;
} else {

return $resultado;

}

}

// Fun��o para carregar avisos para streamings na inicializa��o
function carregar_avisos_streaming_inicializacao($porta,$servidor) {

$sql = mysql_query("SELECT * FROM avisos WHERE area = 'streaming'");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim") {

$checar_status_aviso = mysql_num_rows(mysql_query("SELECT * FROM avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."' AND area = 'streaming' AND codigo_usuario = '".$porta."'"));

if($checar_status_aviso == 0 && ($dados_aviso["codigo_servidor"] == "0" || $dados_aviso["codigo_servidor"] == $servidor)) {

echo "exibir_aviso('".$dados_aviso["codigo"]."');";

} // if aviso desativado usuario
} // if exibir sim/nao
} // while avisos

}

// Fun��o para carregar avisos para streamings
function carregar_avisos_streaming($porta,$servidor) {

$total_avisos = 0;

$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM avisos WHERE area = 'streaming'");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim" && ($dados_aviso["codigo_servidor"] == "0" || $dados_aviso["codigo_servidor"] == $servidor)) {

$checar_status_aviso = mysql_num_rows(mysql_query("SELECT * FROM avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."' AND area = 'streaming' AND codigo_usuario = '".$porta."'"));

if($checar_status_aviso == 0) {

echo "[".$dados_aviso["data"]."] ".$dados_aviso["descricao"]."&nbsp;<a href='#' onclick='exibir_aviso(\"".$dados_aviso["codigo"]."\");'>[+]</a><br />";

$total_avisos++;

} // if exibir sim/nao DESATIVADO
} // if exibir sim/nao
} // while avisos

if($total_avisos == 0) {
echo "<span class='texto_padrao'>".$lang['lang_info_pagina_informacoes_tab_avisos_vazio']."</span>";
}

}

// Fun��o para carregar avisos para revendas
function carregar_avisos_revenda() {

$total_avisos = 0;

$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM avisos WHERE area = 'revenda' ORDER by data DESC");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim") {

echo "[".$dados_aviso["data"]."] ".$dados_aviso["descricao"]."&nbsp;<a href='#' onclick='exibir_aviso(\"".$dados_aviso["codigo"]."\");'>[+]</a><br />";

$total_avisos++;
}
}

if($total_avisos == 0) {
echo "<span class='texto_padrao'>N�o h� registro de avisos.</span>";
}

}

// Fun��o para carregar avisos para streamings
function carregar_avisos_streaming_revenda($porta,$servidor) {

$total_avisos = 0;

$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM avisos WHERE area = 'streaming'");
while ($dados_aviso = mysql_fetch_array($sql)) {

if($dados_aviso["status"] == "sim") {

$checar_status_aviso = mysql_num_rows(mysql_query("SELECT * FROM avisos_desativados where codigo_aviso = '".$dados_aviso["codigo"]."' AND area = 'streaming' AND codigo_usuario = '".$porta."'"));

if($checar_status_aviso == 0 && ($dados_aviso["codigo_servidor"] == "0" || $dados_aviso["codigo_servidor"] == $servidor)) {

echo "[".$dados_aviso["data"]."] ".$dados_aviso["descricao"]."&nbsp;<a href='#' onclick='exibir_aviso(\"".$dados_aviso["codigo"]."\");'>[+]</a><br />";

$total_avisos++;

} // if aviso desativado usuario
} // if exibir sim/nao
} // while avisos

if($total_avisos == 0) {
echo "<span class='texto_padrao'>N�o h� registro de avisos.</span>";
}

}

// Fun��o para criar formatar dom�nio do servidor
function dominio_servidor( $nome ) {

if($_SESSION["porta_logada"]) {
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));
} elseif($_SESSION["code_user_logged"] && $_SESSION["type_logged_user"] == "cliente") {
$dados = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));
} else {
$dados = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
}

if($dados["dominio_padrao"]) {
return strtolower($nome).".".$dados["dominio_padrao"];
} else {
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
return strtolower($nome).".".$dados_config["dominio_padrao"];
}

}

function xml_entity_decode($_string) {
    // Set up XML translation table
    $_xml=array();
    $_xl8=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT);
    while (list($_key,)=each($_xl8))
        $_xml['&#'.ord($_key).';']=$_key;
    return strtr($_string,$_xml);
}

// Fun��o abreviar o nome do navegador
function formatar_navegador($navegador) {

if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$navegador,$matched)) {
return  'IE '.$matched[1].'';
} elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$navegador,$matched)) {
return  'Opera '.$matched[1].'';
} elseif(preg_match('|Firefox/([0-9\.]+)|',$navegador,$matched)) {
return  'Firefox '.$matched[1].'';
} elseif(preg_match('|Chrome/([0-9\.]+)|',$navegador,$matched)) {
return  'Chrome '.$matched[1].'';
} elseif(preg_match('|Safari/([0-9\.]+)|',$navegador,$matched)) {
return  'Safari '.$matched[1].'';
} else {
return 'Desconhecido';
}

}

// Fun��o para inserir registro do log de a��es do painel de administra��o/revenda no banco de dados
function logar_acao($log) {

mysql_query("INSERT INTO logs (data,host,ip,navegador,log) VALUES (NOW(),'http://".$_SERVER['HTTP_HOST']."','".$_SERVER['REMOTE_ADDR']."','".formatar_navegador($_SERVER['HTTP_USER_AGENT'])."','".$log."')") or die("Erro ao inserir log: ".mysql_error()."");

}

// Fun��o para inserir registro do log de a��es do painel de streaming no banco de dados
function logar_acao_streaming($codigo_stm,$log) {

mysql_query("INSERT INTO logs_streamings (codigo_stm,data,host,ip,navegador,log) VALUES ('".$codigo_stm."',NOW(),'http://".$_SERVER['HTTP_HOST']."','".$_SERVER['REMOTE_ADDR']."','".formatar_navegador($_SERVER['HTTP_USER_AGENT'])."','".$log."')") or die("Erro ao inserir log: ".mysql_error()."");

}

////////////////////////////////////////////////
////////// Fun��es Shoutcast & Wowza ///////////
////////////////////////////////////////////////

// Fun��o para checar status do streaming
function status_streaming($ip,$porta) {

$resultado_streaming = @fsockopen($ip, $porta, $errno, $errstr, 2);
@stream_set_timeout($resultado_streaming, 1);

if($resultado_streaming) {
return "ligado";
} else {
return "desligado";
}

}

// Fun��o para checar status do autodj
function status_streaming_transmissao($ip,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/admin.cgi?sid=1&mode=viewxml&pass=".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);

if(curl_error($ch)) {
return "off";
} else {

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

if(preg_match('/http/i',$xml->STREAMSOURCE)) {
return "relay";
} elseif(filter_var($xml->STREAMSOURCE, FILTER_VALIDATE_IP) && $xml->STREAMSOURCE == "127.0.0.1") {
return "autodj";
} elseif(filter_var($xml->STREAMSOURCE, FILTER_VALIDATE_IP) && $xml->STREAMSOURCE != "127.0.0.1") {
return "aovivo";
} else {
return "off";
}
}
curl_close($ch);
}

// Fun��o para checar status do autodj
function status_autodj($ip,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/admin.cgi?sid=1&mode=viewxml&pass=".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

if($xml->STREAMSOURCE == "127.0.0.1") {
return "ligado";
} else {
return "desligado";
}

}

// Fun��o para checar status do autodj
function status_relay($ip,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/admin.cgi?sid=1&mode=viewxml&pass=".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

if(preg_match('/http/i',$xml->STREAMSOURCE)) {
return "ligado";
} else {
return "desligado";
}

}

// Fun��o para obter as estatisticas do streaming no servidor shoutcast para a pagina de ouvintes conectados MOVEL
function estatistica_streaming_shoutcast($stm_ip,$stm_porta,$stm_senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$stm_ip.":".$stm_porta."/admin.cgi?sid=1&mode=viewxml&page=3&pass=".$stm_senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado) {

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_ouvintes = count($xml->LISTENERS->LISTENER);

if($total_ouvintes > 0) {

for($i=0;$i<$total_ouvintes;$i++){

$ip = $xml->LISTENERS->LISTENER[$i]->HOSTNAME;
$tempo_conectado = seconds2time($xml->LISTENERS->LISTENER[$i]->CONNECTTIME);
$player = formatar_useragent($xml->LISTENERS->LISTENER[$i]->USERAGENT);

$array_ouvintes .= "".$ip."|".$tempo_conectado."|".pais_ip($ip,"sigla")."|".pais_ip($ip,"nome")."|".$player."-";
}

}

}

return $array_ouvintes;
}

// Fun��o para obter as estatisticas do streaming para robots
function estatistica_streaming_shoutcast_robot($ip,$porta,$senha,$ponto) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/admin.cgi?sid=".$ponto."&mode=viewxml&page=3&pass=".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado) {

$resultado = str_replace("&#x","",$resultado);

return $resultado;
}

}

// Fun��o para obter as estatisticas do streaming no servidor aacplus para a pagina de ouvintes conectados MOVEL
function estatistica_streaming_aacplus($ip,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":8086/serverinfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
$data = ob_get_clean();

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_streamings = count($xml->VHost->Application);

if($total_streamings > 0) {

for($i=0;$i<$total_streamings;$i++){

if($xml->VHost->Application[$i]->Name == $porta) {

$total_ouvintes = count($xml->VHost->Application[$i]->ApplicationInstance->Client);

for($ii=0;$ii<$total_ouvintes;$ii++){

$ip = $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;
$tempo_conectado = seconds2time($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->TimeRunning);
$pais_sigla = pais_ip($ip,"sigla");
$pais_nome = pais_ip($ip,"nome");
$player = formatar_useragent("RTMP");

$array_ouvintes .= "".$ip."|".$tempo_conectado."|".pais_ip($ip,"sigla")."|".pais_ip($ip,"nome")."|".$player."-";

}

break;

}

}

}

return $array_ouvintes;
}

// Fun��o para obter as estatisticas do streaming no servidor aacplus para os robots
function estatistica_streaming_aacplus_robot($ip,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":8086/serverinfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729)');
$resultado = curl_exec($ch);
curl_close($ch);

return $resultado;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Fun��o para obter as estatisticas do streaming no servidor shoutcast para a pagina de ouvintes conectados
function estatistica_ouvintes_conectados_shoutcast($stm_ip,$stm_porta,$stm_senha,$ponto) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$stm_ip.":".$stm_porta."/admin.cgi?sid=".$ponto."&mode=viewxml&page=3&pass=".$stm_senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

if(preg_match('/Unauthorized/i',$resultado)) {

die('<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed"><tr>
<td width="50" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
<td width="750" align="left" scope="col" style="color: #AB1C10;	font-family: Geneva, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;">Senha admin shoutcast n�o configurada! Acesse menu de configura��o do streaming, defina uma senha e reinicie o streaming.<br><br>Shoutcast admin password not configured! Access the menu settings of streaming, define a password and restart the streaming.</td>
</tr></table>');

} else {

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_ouvintes = count($xml->LISTENERS->LISTENER);

if($total_ouvintes > 0) {

for($i=0;$i<$total_ouvintes;$i++){

$ip = $xml->LISTENERS->LISTENER[$i]->HOSTNAME;
$tempo_conectado = seconds2time($xml->LISTENERS->LISTENER[$i]->CONNECTTIME);
$player = formatar_useragent($xml->LISTENERS->LISTENER[$i]->USERAGENT);

$array_ouvintes[] = "".$ip."|".$tempo_conectado."|".pais_ip($ip,"sigla")."|".pais_ip($ip,"nome")."|".$player."";
}

}

return $array_ouvintes;
}

}

// Fun��o para obter as estatisticas do streaming no servidor aacplus para a pagina de ouvintes conectados
function estatistica_ouvintes_conectados_aacplus($ip,$porta,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":8086/serverinfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
$data = ob_get_clean();

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_streamings = count($xml->VHost->Application);

if($total_streamings > 0) {

for($i=0;$i<$total_streamings;$i++){

if($xml->VHost->Application[$i]->Name == $porta) {

$total_ouvintes = count($xml->VHost->Application[$i]->ApplicationInstance->Client);

for($ii=0;$ii<$total_ouvintes;$ii++){

$ip = $xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;
$tempo_conectado = seconds2time($xml->VHost->Application[$i]->ApplicationInstance->Client[$ii]->TimeRunning);
$pais_sigla = pais_ip($ip,"sigla");
$pais_nome = pais_ip($ip,"nome");
$player = formatar_useragent("RTMP");

$array_ouvintes[] = "".$ip."|".$tempo_conectado."|".pais_ip($ip,"sigla")."|".pais_ip($ip,"nome")."|".$player."";

}

break;

}

}

}

return $array_ouvintes;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Fun��o para mudar a m�sica atual no shoutcast para um texto qualquer
function definir_nome_musica_shoutcast($ip,$porta,$senha,$ponto,$musica) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/admin.cgi?sid=".$ponto."&mode=updinfo&song=".urlencode($musica)."&pass=".$senha."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

}

// Fun��o para criar authhash para shoutcast v2
function criar_authhash($stationname,$genre,$website) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://yp.shoutcast.com/createauthhash?k=sh1N7oyXzUvT8TRK&stationname=".rawurlencode($stationname)."&genre=".rawurlencode($genre)."&website=".$website."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$xml = @simplexml_load_string($resultado);

return array("statusCode" => $xml->statusCode, "statusText" => $xml->statusText, "statusDetailText" => $xml->statusDetailText, "authhash" => $xml->data->authhash);
}

// Fun��o para gerar arquivo de configura��o do streaming
function gerar_conf_streaming($porta,$config) {

$arquivo_config_streaming = "".$porta.".conf";
$handle_config_streaming = fopen("/home/painel/public_html/temp/".$arquivo_config_streaming."" ,"a");
fwrite($handle_config_streaming, $config);
fclose($handle_config_streaming);

return $arquivo_config_streaming;
}

// Fun��o para gerar arquivo de configura��o do AutoDJ
function gerar_conf_autodj($porta,$config) {

$arquivo_config_autodj = "autodj-".$porta.".conf";
$handle_config_autodj = fopen("/home/painel/public_html/temp/".$arquivo_config_autodj."" ,"a");
fwrite($handle_config_autodj, $config);
fclose($handle_config_autodj);

return $arquivo_config_autodj;
}

// Fun��o para gerar calendar de configura��o dos DJs do AutoDJ
function gerar_calendar_autodj($config) {

$config_calendar = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<eventlist>\n\n";

if($config["djs"]) {

foreach($config["djs"] as $dj_config) {

if($dj_config["total_restricoes"] == 0) {

$config_calendar .= "<event type=\"dj\">\n";
$config_calendar .= "<dj archive=\"0\">".$dj_config["login"]."</dj>\n";
$config_calendar .= "<calendar />\n";
$config_calendar .= "</event>\n\n";

} else {

$lista_restricoes = explode(",",$dj_config["restricoes"]);

foreach($lista_restricoes as $restricao) {

list($hora_inicio, $hora_fim, $dias_semana) = explode("|", $restricao);

if($hora_inicio && $hora_fim) {

$config_calendar .= "<event type=\"dj\">\n";
$config_calendar .= "<dj archive=\"0\">".$dj_config["login"]."</dj>\n";

list($hora1, $minuto1) = explode(":", $hora_inicio);
list($hora2, $minuto2) = explode(":", $hora_fim);

if($hora1 <= $hora2) {

$start_date = new DateTime(''.date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d"), date("Y"))).' '.$hora_inicio.':00');
$since_start = $start_date->diff(new DateTime(''.date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d"), date("Y"))).' '.$hora_fim.':00'));

$duracao = sprintf("%02d", $since_start->h).':'.sprintf("%02d", $since_start->i);

} else {

$start_date = new DateTime(''.date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d"), date("Y"))).' '.$hora_inicio.':00');
$since_start = $start_date->diff(new DateTime(''.date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")+1, date("Y"))).' '.$hora_fim.':00'));

$duracao = sprintf("%02d", $since_start->h).':'.sprintf("%02d", $since_start->i);

}

$config_calendar .= "<calendar starttime=\"".$hora_inicio.":00\" duration=\"".$duracao.":00\" repeat=\"".$dias_semana."\"/>\n";
$config_calendar .= "</event>\n\n";

}

}

}

}

}

/*
if($config["relay"]) {

foreach($config["relay"] as $relay) {

list($servidor,$frequencia,$data,$hora,$minuto,$duracao_hora,$duracao_minuto,$dias) = explode("|",$relay);

$duracao = ($duracao_hora != 00 || $duracao_minuto != 00) ? "duration=\"".$duracao_hora.":".$duracao_minuto.":00\"" : "";

$config_calendar .= "<event type=\"relay\">\n";

if($frequencia == "1") { // Executar uma vez(dia espec�fico)


$config_calendar .= "<calendar startdate=\"".str_replace("-","/",$data)."\" enddate=\"".str_replace("-","/",$data)."\" starttime=\"".$hora.":".$minuto.":00\" ".$duracao." />\n";

} else if($frequencia == "2") { // Executar diariamente

$config_calendar .= "<calendar starttime=\"".$hora.":".$minuto.":00\" ".$duracao." repeat=\"127\" />\n";

} else { // Executar em dias espec�ficos

$dias_executar = 0;

$array_dias = explode(",",$dias);

foreach($array_dias as $dia) {
$dias_executar += $dia;
}

$config_calendar .= "<calendar starttime=\"".$hora.":".$minuto.":00\" ".$duracao." repeat=\"".$dias_executar."\" />\n";
}

$config_calendar .= "<relay url=\"".$servidor."\" priority=\"1\"/>\n";

$config_calendar .= "</event>\n\n";

}

}
*/
$config_calendar .= "</eventlist>";

$arquivo_config_calendar = "calendar-".$config["porta"].".xml";
$handle_config_calendar = fopen("/home/painel/public_html/temp/".$arquivo_config_calendar."" ,"a");

fwrite($handle_config_calendar, $config_calendar);
fclose($handle_config_calendar);

return $arquivo_config_calendar;
}

// Fun��o para gerar arquivo de configura��o do AutoDJ
function gerar_playlist($playlist,$musicas) {

$handle_playlist = fopen("/home/painel/public_html/temp/".$playlist."" ,"a");
fwrite($handle_playlist, $musicas);
fclose($handle_playlist);

return $playlist;

}

// Fun��o para capturar informa��es de um streaming no shoutcast
function shoutcast_info($ip,$porta,$ponto) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":".$porta."/stats?sid=".$ponto."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);

$resultado = xml_entity_decode($resultado);

$xml = @simplexml_load_string(utf8_encode($resultado));

return array("ouvintes_total" => $xml->CURRENTLISTENERS, "ouvintes" => $xml->MAXLISTENERS, "bitrate" => $xml->BITRATE, "encoder" => $xml->CONTENT, "musica" => $xml->SONGTITLE, "titulo" => $xml->SERVERTITLE, "pico_ouvintes" => $xml->PEAKLISTENERS, "proxima_musica" => $xml->NEXTTITLE, "genero" => $xml->SERVERGENRE);
}

// Fun��o para capturar o TOTAL de ouvintes online de um streaming no Wowza
function stats_ouvintes_aacplus($porta,$ip,$senha) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$ip.":8086/connectioncounts");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERPWD, "admin:".code_decode($senha,"D").""); 
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
ob_start();
$resultado = curl_exec($ch);
$data = ob_get_clean();

$xml = @simplexml_load_string(utf8_encode($resultado));

$total_streamings = count($xml->VHost->Application);

if($total_streamings > 0) {

for($i=0;$i<$total_streamings;$i++){

if($xml->VHost->Application[$i]->Name == $porta) {

return array("ouvintes" => $xml->VHost->Application[$i]->ConnectionsCurrent);
break;

}

}

}

curl_close($ch);

}

/////////////////////////////////////////////
//////////// Fun��es App Android ////////////
/////////////////////////////////////////////

// Fun��o para formatar o nome da radio retirando acentos e caracteres especiais
function formatar_nome_radio($nome) {

$characteres = array(
    'S'=>'S', 's'=>'s', '�'=>'Dj','Z'=>'Z', 'z'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A',
    '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I',
    '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U', '�'=>'U',
    '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss','�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a',
    '�'=>'a', '�'=>'a', '�'=>'c', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i',
    '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'u',
    '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b', '�'=>'y', 'f'=>'f', '�'=> '', '�'=> '', '&'=> 'e',
	'�'=> '', '�'=> '', '$'=> '', '%'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', 'ã'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', '�'=> '',
	'/'=> '', '�'=> '', '+'=> '', '*'=> '', '['=> '', ']'=> ''
);

return strtr($nome, $characteres);

}

// Fun��o para formatar o nome do app para o google play retirando acentos e caracteres especiais
function nome_app_play($texto) {

$characteres = array(
    'S'=>'S', 's'=>'s', '�'=>'Dj','Z'=>'Z', 'z'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A',
    '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I',
    '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U', '�'=>'U',
    '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss','�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a',
    '�'=>'a', '�'=>'a', '�'=>'c', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i',
    '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'u',
    '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b', '�'=>'y', 'f'=>'f', '�'=> '', '�'=> '', '&'=> 'e',
	'�'=> '', '�'=> '', '$'=> '', '%'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', 'ã'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', ' '=> '',
	'-'=> '', '^'=> '', '~'=> '', '.'=> '', '|'=> '', ','=> '', '<'=> '', '>'=> '', '{'=> '', '}'=> '',
	'�'=> '', '/'=> '', '�'=> '', '+'=> '', '*'=> '', '['=> '', ']'=> ''
);

return strtolower(strtr($texto, $characteres));

}

// Fun��o para formatar o nome do apk do app retirando acentos e caracteres especiais
function nome_app_apk($texto) {

$characteres = array(
    'S'=>'S', 's'=>'s', '�'=>'Dj','Z'=>'Z', 'z'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A',
    '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I',
    '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U', '�'=>'U',
    '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss','�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a',
    '�'=>'a', '�'=>'a', '�'=>'c', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i',
    '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'u',
    '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b', '�'=>'y', 'f'=>'f', '�'=> '', '�'=> '', '&'=> 'e',
	'�'=> '', '�'=> '', '$'=> '', '%'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', '�'=> '', 'ã'=> '',
	'('=> '', ')'=> '', "'"=> '', '@'=> '', '='=> '', ':'=> '', '!'=> '', '?'=> '', '...'=> '', ' '=> '',
	'-'=> '', '^'=> '', '~'=> '', '.'=> '', '|'=> '', ','=> '', '<'=> '', '>'=> '', '{'=> '', '}'=> '',
	' '=> '', '�'=> '', '/'=> '', '�'=> '', '+'=> '', '*'=> '', '['=> '', ']'=> ''
);

return strtr($texto, $characteres);

}

// Fun��o para copiar o source para o novo app
function copiar_source($DirFont, $DirDest) {
    
    mkdir($DirDest);
    if ($dd = opendir($DirFont)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $PathIn = "$DirFont/$Arq";
                $PathOut = "$DirDest/$Arq";
                if(is_dir($PathIn)){
                    copiar_source($PathIn, $PathOut);
					chmod($PathOut,0777);
                }elseif(is_file($PathIn)){
                    copy($PathIn, $PathOut);
					chmod($PathOut,0777);
                }
            }
        }
        closedir($dd);
	}

}

// Fun��o para criar arquivos de configura��o do app
function criar_arquivo_config($arquivo,$conteudo) {

$fd = fopen ($arquivo, "w");
fputs($fd, $conteudo);
fclose($fd);

}

// Fun��o para carregar todos os arquivos e pastas de um diretorio
function browse($dir) {
global $filenames;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && is_file($dir.'/'.$file)) {
                $filenames[] = $dir.'/'.$file;
            }
            else if ($file != "." && $file != ".." && is_dir($dir.'/'.$file)) {
                browse($dir.'/'.$file);
            }
        }
        closedir($handle);
    }
    return $filenames;
}

// Fun��o para substituir uma string dentro de um arquivo de texto
function replace($arquivo,$string_atual,$string_nova) {

//$str = implode("\n",file($arquivo));
//$fp = fopen($arquivo,'w');
//$str = str_replace($string_atual,$string_nova,$str);

//fwrite($fp,$str,strlen($str));

$str = file_get_contents($arquivo);
$str = str_replace($string_atual,$string_nova,$str);
file_put_contents($arquivo,$str);

}

// Fun��o para remover o source do novo app
function remover_source_app($Dir){
    
    if ($dd = @opendir($Dir)) {
        while (false !== ($Arq = @readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $Path = "$Dir/$Arq";
                if(is_dir($Path)){
                    remover_source_app($Path);
                }elseif(is_file($Path)){
                    @unlink($Path);
                }
            }
        }
        @closedir($dd);
    }
    @rmdir($Dir);
}

// Fun��o para mudar a permiss�o de todos os arquivos e pasta no source do app
function mudar_permissao($Dir){

    if ($dd = opendir($Dir)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $Path = "$Dir/$Arq";
                @chmod($Path,0777);
            }
        }
        closedir($dd);
    }

}

// Fun��o de integra��o com api LastFM
function lastfm($tipo,$chave) {

$chave = urlencode($chave);
$url = "https://ws.audioscrobbler.com/2.0/?method=".$tipo.".getinfo&".$tipo."=".$chave."&user=advancehostbr&api_key=70ecb546f2e36b9858b1bbf14343a120";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Painel de Streaming 3.0.0');
$resultado = curl_exec($ch);
curl_close($ch);
                
if(!$resultado) {
return;  // Artist lookup failed.
}

$xml = new SimpleXMLElement($resultado);

if($xml->artist->image[2]) {
return array("status" => "ok", "imagem" => $xml->artist->image[2]);
} else {
return array("status" => "ok", "imagem" => $xml->artist->similar->artist->image[2]);
}
     
}

// Fun��o de integra��o com api LastFM
function vagalumeapi($tipo,$chave1,$chave2) {

$chave1 = urlencode(trim($chave1));
$chave2 = urlencode(trim($chave2));

if($tipo == "capa1") {
$url = "https://api.vagalume.com.br/search.php?art=".$chave1."&extra=artpic&nolyrics=1";
} elseif($tipo == "capa2") {
$url = "https://api.vagalume.com.br/search.php?art=".$chave1."&extra=alb&mus=".$chave2."&nolyrics=1";
} else {
$url = "https://api.vagalume.com.br/search.php?art=".$chave1."&mus=".$chave2."";
}

$resultado = @file_get_contents($url);
                
if(!$resultado) {
return array("status" => "erro", "status" => "N�o foi poss�vel conectar-se ao servidor de informa��es sobre a m�sica.");
}

$resultado = json_decode($resultado);

if($resultado->type == "notfound") {
return array("status" => "erro", "status" => "N�o foi poss�vel localizar.");
}

if($tipo == "capa1") {

return array("status" => "ok", "imagem" => $resultado->art->pic_medium);

} elseif($tipo == "capa2") {

return array("status" => "ok", "imagem" => $resultado->mus[0]->alb->img);

} else {

return array("status" => "ok", "letra" => $resultado->mus[0]->text, "traducao" => utf8_decode($resultado->mus[0]->translate[0]->text));

}
                
}

// Fun��o de monitoramento contra ataques
function monitoramento_ataques() {

$headers = "";
$headers .= 'MIME-Version: 1.0'."\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
$headers .= 'From: Painel de Streaming <cesar@advancehost.com.br>'."\r\n";
$headers .= 'To: cesar@advancehost.com.br'."\r\n";
$headers .= "X-Sender: Painel de Streaming <cesar@advancehost.com.br>\n";
$headers .= 'X-Mailer: PHP/' . phpversion();
$headers .= "X-Priority: 1\n";
$headers .= "Return-Path: cesar@advancehost.com.br\n";

$mensagem = "";
$mensagem .= "==========================================<br>";
$mensagem .= "======== Tentativa de invas�o! ========<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "IP: ".$_SERVER["REMOTE_ADDR"]."<br>";
$mensagem .= "Host: ".gethostbyaddr($_SERVER["REMOTE_ADDR"])."<br>";
$mensagem .= "Data: ".date("d/m/Y H:i:s")."<br>";
$mensagem .= "URI: ".$_SERVER['REQUEST_URI']."<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "======== Informa��es Diversas ========<br>";
$mensagem .= "==========================================<br>";
$mensagem .= "".$_SERVER['HTTP_REFERER']."<br>";
$mensagem .= "".$_SERVER['HTTP_HOST']."<br>";
$mensagem .= "".$_SERVER['HTTP_USER_AGENT']."<br>";
$mensagem .= "".$_SERVER['QUERY_STRING']."<br>";
$mensagem .= "".$_SERVER['REQUEST_METHOD']."<br>";
$mensagem .= "==========================================";

mail("cesar@advancehost.com.br","[Alerta] Tentativa de invas�o!",$mensagem,$headers);

}

// Fun��o abreviar o nome do navegador
function formatar_useragent($useragent) {

if(preg_match('/VLC/i',$useragent)) {
return  'VLC';
} elseif(preg_match('/NSPlayer/i',$useragent) || preg_match('/WMFSDK/i',$useragent) || preg_match('/WMPlayer/i',$useragent)) {
return  'Win Media Player';
} elseif(preg_match('/RMA/i',$useragent) || preg_match('/RealMedia/i',$useragent)) {
return  'Real Player';
} elseif(preg_match('/WinampMPEG/i',$useragent) && preg_match('/Ultravox/i',$useragent)) {
return  'Winamp';
} elseif(preg_match('/QuickTime/i',$useragent)) {
return  'QuickTime';
} elseif((preg_match('/EMPTY/i',$useragent) || preg_match('/Dalvik/i',$useragent)) && !preg_match('/Mozilla/i',$useragent)) {
return  'App Android';
} elseif(preg_match('/iTunes/i',$useragent)) {
return  'iTunes';
} elseif(preg_match('/Lavf/i',$useragent)) {
return  'TuneIn';
} elseif(preg_match('/AND/i',$useragent)) {
return  'Android RTSP';
} elseif(preg_match('/RTMP/i',$useragent)) {
return  'Flash RTMP';
} elseif(preg_match('/Chrome/i',$useragent)) {
return  'HTML5';
} elseif(preg_match('/Firefox/i',$useragent) || preg_match('/Safari/i',$useragent) || preg_match('/MSIE/i',$useragent)) {
return  'Flash';
} elseif(preg_match('/Sony/i',$useragent)) {
return  'Sony Mobile';
} elseif(preg_match('/LG/i',$useragent)) {
return  'LG Mobile';
} elseif(preg_match('/Samsung/i',$useragent)) {
return  'Samsung Mobile';
} elseif(preg_match('/MPlayer/i',$useragent)) {
return  'MPlayer Linux/Win';
} else {
return 'Outro';
}

}

// Fun��o para inserir elementos em uma array
function array_insert(&$array, $position, $insert)
{
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}

// Fun��o para transformar v�rias arrays dentro de uma mesma array em uma s� array
function flatten_array($array) {
    if (!is_array($array)) {
        // nothing to do if it's not an array
        return array($array);
    }

    $result = array();
    foreach ($array as $value) {
        // explode the sub-array, and add the parts
        $result = array_merge($result, flatten_array($value));
    }

    return $result;
}

// Calcula a diferen�a de horas entre 2 time zones
function get_timezone_offset($remote_tz, $origin_tz = null) {
    if($origin_tz === null) {
        if(!is_string($origin_tz = date_default_timezone_get())) {
            return false; // A UTC timestamp was returned -- bail out!
        }
    }
    $origin_dtz = new DateTimeZone($origin_tz);
    $remote_dtz = new DateTimeZone($remote_tz);
    $origin_dt = new DateTime("now", $origin_dtz);
    $remote_dt = new DateTime("now", $remote_dtz);
    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	
	$offset = $offset/3600;
    return $offset;
}

// Fun��o para formatar datas
function formatar_data($formato, $data, $timezone) {

$formato = (preg_match('/:/i',$data)) ? $formato : str_replace("H:i:s","",$formato);

$offset = get_timezone_offset('America/Sao_Paulo',$timezone);

$nova_data = strtotime ( ''.$offset.' hour' , strtotime ( $data ) ) ;
$nova_data = date ( $formato , $nova_data );

return $nova_data;

}

function isSSL() {

if( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' )
	return true;

if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
	return true;

return false;
}

function date_diff_minutes( $date ) {

$first  = new DateTime( $date );
$second = new DateTime( "now" );

$diff = $first->diff( $second );

return $diff->format( '%I' );

}

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function youtube_parser($url) {

preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);

return $matches[1];
}

?>