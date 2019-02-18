<?php
ini_set("max_execution_time", 3600);

require_once("inc/conecta-remoto.php");

// Fun��o para calcular tempo de exceuss�o
function tempo_execucao() {
    $sec = explode(" ",microtime());
    $tempo = $sec[1] + $sec[0];
    return $tempo;
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
  $alpha_array = array('0','D','5','R','7','Y','8','M','A','T','Z','X','A','E','Y','4','8','1','D','J','L');
  $sesencoded =
  $sesencoded."+".$alpha_array[$num];
  $sesencoded = base64_encode($sesencoded);
  return $sesencoded;
  
  } else {
  
   $alpha_array = array('0','D','5','R','7','Y','8','M','A','T','Z','X','A','E','Y','4','8','1','D','J','L');
   $decoded = base64_decode($texto);
   list($decoded,$letter) = split("\+",$decoded);
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

// Fun��o para obter nome do Pa�s
function pais_ip($ip,$tipo) {

$paises = array("AF" => "Afeganist�o", "AL" => "Alb�nia", "DE" => "Alemanha", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Ant�rctida", "AG" => "Antigua e Barbuda", "AN" => "Antilhas Holandesas", "SA" => "Ar�bia Saudita", "AR" => "Argentina", "DZ" => "Arg�lia", "AM" => "Arm�nia", "AW" => "Aruba", "AU" => "Austr�lia", "AZ" => "Azerbeij�o", "ZA" => "�frica do Sul", "AT" => "�ustria", "AX" => "�land", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BE" => "B�lgica", "BY" => "Bielo-R�ssia", "BO" => "Bol�via", "BW" => "Botswana", "BV" => "Bouvet", "BA" => "B�snia-Herzegovina", "BR" => "Brasil", "BN" => "Brunei", "BG" => "Bulg�ria", "BF" => "Burkina Faso", "BI" => "Burundi", "BT" => "But�o", "CV" => "Cabo Verde", "CM" => "Camar�es", "KH" => "Cambodja", "CA" => "Canad�", "KY" => "Cayman", "KZ" => "Cazaquist�o", "CF" => "Centro-africana, Rep�blica", "TD" => "Chade", "CZ" => "Rep�blica Checa", "CL" => "Chile", "CN" => "China", "CY" => "Chipre", "CX" => "Christmas", "CC" => "Cocos", "CO" => "Col�mbia", "KM" => "Comores", "CD" => "Rep�blica Democr�tica do Congo", "CG" => "Rep�blica do Congo", "CK" => "Cook", "KR" => "Coreia do Sul", "KP" => "Coreia do Norte", "CI" => "Costa do Marfim", "CR" => "Costa Rica", "HR" => "Cro�cia", "CU" => "Cuba", "DK" => "Dinamarca", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Rep�blica Dominicana", "EG" => "Egito", "SV" => "El Salvador", "AE" => "Emiratos �rabes", "EC" => "Equador", "ER" => "Eritreia", "SK" => "Eslov�quia", "SI" => "Eslov�nia", "ES" => "Espanha", "US" => "Estados Unidos", "EE" => "Est�nia", "ET" => "Eti�pia", "FO" => "Faroe", "FJ" => "Fiji", "PH" => "Filipinas", "FI" => "Finl�ndia", "FR" => "Fran�a", "GA" => "Gab�o", "GH" => "Gana", "GM" => "G�mbia", "GE" => "Ge�rgia", "GS" => "Ge�rgia do Sul", "GI" => "Gibraltar", "GD" => "Grenada", "GR" => "Gr�cia", "GL" => "Gronel�ndia", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GY" => "Guiana", "GF" => "Guiana Francesa", "GQ" => "Guin� Equatorial", "GW" => "Guin�-Bissau", "GN" => "Guin�-Conacri", "HT" => "Haiti", "HM" => "Heard e Ilhas McDonald", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungria", "YE" => "I�men", "ID" => "Indon�sia", "IQ" => "Iraque", "IR" => "Ir�o", "IE" => "Irlanda", "IS" => "Isl�ndia", "IL" => "Israel", "IT" => "It�lia", "IN" => "�ndia", "JM" => "Jamaica", "JP" => "Jap�o", "JE" => "Jersey", "JO" => "Jord�nia", "KI" => "Kiribati", "KW" => "Kuwait", "LA" => "Laos", "LS" => "Lesoto", "LV" => "Let�nia", "LR" => "Lib�ria", "LI" => "Liechtenstein", "LT" => "Litu�nia", "LB" => "L�bano", "LY" => "L�bia", "LU" => "Luxemburgo", "MO" => "Macau", "MK" => "Maced�nia, Rep�blica da", "MG" => "Madag�scar", "MW" => "Malawi", "MY" => "Mal�sia", "MV" => "Maldivas", "ML" => "Mali", "MT" => "Malta", "FK" => "Malvinas (Falkland)", "IM" => "Man", "MP" => "Marianas Setentrionais", "MA" => "Marrocos", "MH" => "Marshall", "MQ" => "Martinica", "MR" => "Maurit�nia", "MU" => "Maur�cia", "YT" => "Mayotte", "UM" => "Menores Distantes dos Estados Unidos", "MX" => "M�xico", "FM" => "Micron�sia", "MZ" => "Mo�ambique", "MD" => "Mold�via", "MN" => "Mong�lia", "ME" => "Montenegro", "MS" => "Montserrat", "MC" => "M�naco", "MM" => "Myanmar", "NA" => "Nam�bia", "NR" => "Nauru", "NP" => "Nepal", "NI" => "Nicar�gua", "NG" => "Nig�ria", "NU" => "Niue", "NE" => "N�ger", "NF" => "Norfolk", "NO" => "Noruega", "NC" => "Nova Caled�nia", "NZ" => "Nova Zel�ndia", "OM" => "Oman", "NL" => "Holanda", "PW" => "Palau", "PS" => "Palestina", "PA" => "Panam�", "PG" => "Papua-Nova Guin�", "PK" => "Paquist�o", "PY" => "Paraguai", "PE" => "Peru", "PN" => "Pitcairn", "PF" => "Polin�sia Francesa", "PL" => "Pol�nia", "PR" => "Porto Rico", "PT" => "Portugal", "QA" => "Qatar", "KE" => "Qu�nia", "KG" => "Quirguist�o", "GB" => "Reino Unido da Gr�-Bretanha e Irlanda do Norte", "RE" => "Reuni�o", "RO" => "Rom�nia", "RW" => "Ruanda", "RU" => "R�ssia", "EH" => "Saara Ocidental", "PM" => "Saint Pierre et Miquelon", "SB" => "Salom�o", "WS" => "Samoa (Samoa Ocidental)", "AS" => "Samoa Americana", "SM" => "San Marino", "SH" => "Santa Helena", "LC" => "Santa L�cia", "KN" => "S�o Crist�v�o e N�vis (Saint Kitts e Nevis)", "ST" => "S�o Tom� e Pr�ncipe", "VC" => "S�o Vicente e Granadinas", "SN" => "Senegal", "SL" => "Serra Leoa", "SC" => "Seychelles", "RS" => "S�rvia", "SG" => "Singapura", "SY" => "S�ria", "SO" => "Som�lia", "LK" => "Sri Lanka", "SZ" => "Suazil�ndia", "SD" => "Sud�o", "SE" => "Su�cia", "CH" => "Su��a", "SR" => "Suriname", "SJ" => "Svalbard e Jan Mayen", "TH" => "Tail�ndia", "TW" => "Taiwan", "TJ" => "Tajiquist�o", "TZ" => "Tanz�nia", "TF" => "Terras Austrais e Ant�rticas Francesas", "IO" => "Territ�rio Brit�nico do Oceano �ndico", "TL" => "Timor-Leste", "TG" => "Togo", "TO" => "Tonga", "TK" => "Toquelau", "TT" => "Trindade e Tobago", "TN" => "Tun�sia", "TC" => "Turks e Caicos", "TM" => "Turquemenist�o", "TR" => "Turquia", "TV" => "Tuvalu", "UA" => "Ucr�nia", "UG" => "Uganda", "UY" => "Uruguai", "UZ" => "Usbequist�o", "VU" => "Vanuatu", "VA" => "Vaticano", "VE" => "Venezuela", "VN" => "Vietname", "VI" => "Virgens Americanas", "VG" => "Virgens Brit�nicas", "WF" => "Wallis e Futuna", "ZM" => "Z�mbia", "ZW" => "Zimbabwe");

$pais_ip = geoip_country_code_by_name($ip);

if($tipo == "nome") {

if($paises[$pais_ip]) {
return $paises[$pais_ip];
} else {
return "IP sem informa��es de localiza��o";
}

} else {

if($pais_ip) {
return $pais_ip;
} else {
return "Desconhecido";
}

}

}

// Fun��o para obter as estatisticas do streaming no servidor aacplus
function estatistica_streaming_aacplus($ip,$senha) {

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

//////////////////////////////////////////////////////////////////////////////////////////////

$inicio_execucao = tempo_execucao();

parse_str($argv[1]);

list($inicial,$final) = explode("-",$registros);

echo "\n\n--------------------------------------------------------------------\n\n";

// Grava cache com o XML do wowza de todos os servidores RTMP
$sql_servidores = mysql_query("SELECT * FROM servidores where status = 'on' AND tipo = 'aacplus' ORDER by ordem ASC");
while ($dados_servidor_aacplus = mysql_fetch_array($sql_servidores)) {

$xml_wowza = @simplexml_load_string(utf8_encode(estatistica_streaming_aacplus($dados_servidor_aacplus["ip"],$dados_servidor_aacplus["senha"])));

$array_xml["stats"][$dados_servidor_aacplus["codigo"]] = $xml_wowza;


echo "Servidor Wowza: ".$dados_servidor_aacplus["nome"]."\n";

}

echo "\n--------------------------------------------------------------------\n\n";

// Gera as estatisticas
$sql = mysql_query("SELECT * FROM streamings where status = '1' AND aacplus = 'sim' ORDER by porta ASC LIMIT ".$inicial.", ".$final."");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

if($dados_servidor["status"] == "on") {

if($dados_servidor_aacplus["status"] == "on") {

$xml_stats_wowza = $array_xml["stats"][$dados_servidor_aacplus["codigo"]];

$total_registros_wowza = count($xml_stats_wowza->VHost->Application);

if($total_registros_wowza > 0) {

for($i=0;$i<$total_registros_wowza;$i++){

if($xml_stats_wowza->VHost->Application[$i]->Name == $dados_stm["porta"]) {

$total_ouvintes_wowza = count($xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client);

for($ii=0;$ii<$total_ouvintes_wowza;$ii++){

$ip_wowza = $xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->IpAddress;
$tempo_conectado_wowza = $xml_stats_wowza->VHost->Application[$i]->ApplicationInstance->Client[$ii]->TimeRunning;
$pais_wowza = pais_ip($ip_wowza,"nome");


$verifica_ouvinte_wowza = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip_wowza."' AND data = '".date("Y-m-d")."'"));

if($verifica_ouvinte_wowza == 0) {

mysql_query("INSERT INTO estatisticas (codigo_stm,data,ip,pais,tempo_conectado) VALUES ('".$dados_stm["codigo"]."',NOW(),'".$ip_wowza."','".$pais_wowza."','".$tempo_conectado_wowza."')");

echo "[".$dados_stm["porta"]."][Wowza] Ouvinte: ".$ip_wowza." adicionado.\n";

} else {

mysql_query("Update estatisticas set tempo_conectado = '".$tempo_conectado_wowza."' where codigo_stm = '".$dados_stm["codigo"]."' AND ip = '".$ip_wowza."' AND data = '".date("Y-m-d")."'");

echo "[".$dados_stm["porta"]."][Wowza] Ouvinte: ".$ip_wowza." atualizado.\n";

}

}

break;

}

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