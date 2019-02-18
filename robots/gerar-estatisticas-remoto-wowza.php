<?php
ini_set("max_execution_time", 3600);

require_once("inc/conecta-remoto.php");

// Função para calcular tempo de exceussão
function tempo_execucao() {
    $sec = explode(" ",microtime());
    $tempo = $sec[1] + $sec[0];
    return $tempo;
}

// Função para codificar e decodificar strings
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

// Função para obter nome do País
function pais_ip($ip,$tipo) {

$paises = array("AF" => "Afeganistão", "AL" => "Albânia", "DE" => "Alemanha", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antárctida", "AG" => "Antigua e Barbuda", "AN" => "Antilhas Holandesas", "SA" => "Arábia Saudita", "AR" => "Argentina", "DZ" => "Argélia", "AM" => "Arménia", "AW" => "Aruba", "AU" => "Austrália", "AZ" => "Azerbeijão", "ZA" => "África do Sul", "AT" => "Áustria", "AX" => "Åland", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BE" => "Bélgica", "BY" => "Bielo-Rússia", "BO" => "Bolívia", "BW" => "Botswana", "BV" => "Bouvet", "BA" => "Bósnia-Herzegovina", "BR" => "Brasil", "BN" => "Brunei", "BG" => "Bulgária", "BF" => "Burkina Faso", "BI" => "Burundi", "BT" => "Butão", "CV" => "Cabo Verde", "CM" => "Camarões", "KH" => "Cambodja", "CA" => "Canadá", "KY" => "Cayman", "KZ" => "Cazaquistão", "CF" => "Centro-africana, República", "TD" => "Chade", "CZ" => "República Checa", "CL" => "Chile", "CN" => "China", "CY" => "Chipre", "CX" => "Christmas", "CC" => "Cocos", "CO" => "Colômbia", "KM" => "Comores", "CD" => "República Democrática do Congo", "CG" => "República do Congo", "CK" => "Cook", "KR" => "Coreia do Sul", "KP" => "Coreia do Norte", "CI" => "Costa do Marfim", "CR" => "Costa Rica", "HR" => "Croácia", "CU" => "Cuba", "DK" => "Dinamarca", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "República Dominicana", "EG" => "Egito", "SV" => "El Salvador", "AE" => "Emiratos Árabes", "EC" => "Equador", "ER" => "Eritreia", "SK" => "Eslováquia", "SI" => "Eslovénia", "ES" => "Espanha", "US" => "Estados Unidos", "EE" => "Estónia", "ET" => "Etiópia", "FO" => "Faroe", "FJ" => "Fiji", "PH" => "Filipinas", "FI" => "Finlândia", "FR" => "França", "GA" => "Gabão", "GH" => "Gana", "GM" => "Gâmbia", "GE" => "Geórgia", "GS" => "Geórgia do Sul", "GI" => "Gibraltar", "GD" => "Grenada", "GR" => "Grécia", "GL" => "Gronelândia", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GY" => "Guiana", "GF" => "Guiana Francesa", "GQ" => "Guiné Equatorial", "GW" => "Guiné-Bissau", "GN" => "Guiné-Conacri", "HT" => "Haiti", "HM" => "Heard e Ilhas McDonald", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungria", "YE" => "Iémen", "ID" => "Indonésia", "IQ" => "Iraque", "IR" => "Irão", "IE" => "Irlanda", "IS" => "Islândia", "IL" => "Israel", "IT" => "Itália", "IN" => "Índia", "JM" => "Jamaica", "JP" => "Japão", "JE" => "Jersey", "JO" => "Jordânia", "KI" => "Kiribati", "KW" => "Kuwait", "LA" => "Laos", "LS" => "Lesoto", "LV" => "Letónia", "LR" => "Libéria", "LI" => "Liechtenstein", "LT" => "Lituânia", "LB" => "Líbano", "LY" => "Líbia", "LU" => "Luxemburgo", "MO" => "Macau", "MK" => "Macedónia, República da", "MG" => "Madagáscar", "MW" => "Malawi", "MY" => "Malásia", "MV" => "Maldivas", "ML" => "Mali", "MT" => "Malta", "FK" => "Malvinas (Falkland)", "IM" => "Man", "MP" => "Marianas Setentrionais", "MA" => "Marrocos", "MH" => "Marshall", "MQ" => "Martinica", "MR" => "Mauritânia", "MU" => "Maurícia", "YT" => "Mayotte", "UM" => "Menores Distantes dos Estados Unidos", "MX" => "México", "FM" => "Micronésia", "MZ" => "Moçambique", "MD" => "Moldávia", "MN" => "Mongólia", "ME" => "Montenegro", "MS" => "Montserrat", "MC" => "Mónaco", "MM" => "Myanmar", "NA" => "Namíbia", "NR" => "Nauru", "NP" => "Nepal", "NI" => "Nicarágua", "NG" => "Nigéria", "NU" => "Niue", "NE" => "Níger", "NF" => "Norfolk", "NO" => "Noruega", "NC" => "Nova Caledónia", "NZ" => "Nova Zelândia", "OM" => "Oman", "NL" => "Holanda", "PW" => "Palau", "PS" => "Palestina", "PA" => "Panamá", "PG" => "Papua-Nova Guiné", "PK" => "Paquistão", "PY" => "Paraguai", "PE" => "Peru", "PN" => "Pitcairn", "PF" => "Polinésia Francesa", "PL" => "Polónia", "PR" => "Porto Rico", "PT" => "Portugal", "QA" => "Qatar", "KE" => "Quénia", "KG" => "Quirguistão", "GB" => "Reino Unido da Grã-Bretanha e Irlanda do Norte", "RE" => "Reunião", "RO" => "Roménia", "RW" => "Ruanda", "RU" => "Rússia", "EH" => "Saara Ocidental", "PM" => "Saint Pierre et Miquelon", "SB" => "Salomão", "WS" => "Samoa (Samoa Ocidental)", "AS" => "Samoa Americana", "SM" => "San Marino", "SH" => "Santa Helena", "LC" => "Santa Lúcia", "KN" => "São Cristóvão e Névis (Saint Kitts e Nevis)", "ST" => "São Tomé e Príncipe", "VC" => "São Vicente e Granadinas", "SN" => "Senegal", "SL" => "Serra Leoa", "SC" => "Seychelles", "RS" => "Sérvia", "SG" => "Singapura", "SY" => "Síria", "SO" => "Somália", "LK" => "Sri Lanka", "SZ" => "Suazilândia", "SD" => "Sudão", "SE" => "Suécia", "CH" => "Suíça", "SR" => "Suriname", "SJ" => "Svalbard e Jan Mayen", "TH" => "Tailândia", "TW" => "Taiwan", "TJ" => "Tajiquistão", "TZ" => "Tanzânia", "TF" => "Terras Austrais e Antárticas Francesas", "IO" => "Território Britânico do Oceano Índico", "TL" => "Timor-Leste", "TG" => "Togo", "TO" => "Tonga", "TK" => "Toquelau", "TT" => "Trindade e Tobago", "TN" => "Tunísia", "TC" => "Turks e Caicos", "TM" => "Turquemenistão", "TR" => "Turquia", "TV" => "Tuvalu", "UA" => "Ucrânia", "UG" => "Uganda", "UY" => "Uruguai", "UZ" => "Usbequistão", "VU" => "Vanuatu", "VA" => "Vaticano", "VE" => "Venezuela", "VN" => "Vietname", "VI" => "Virgens Americanas", "VG" => "Virgens Britânicas", "WF" => "Wallis e Futuna", "ZM" => "Zâmbia", "ZW" => "Zimbabwe");

$pais_ip = geoip_country_code_by_name($ip);

if($tipo == "nome") {

if($paises[$pais_ip]) {
return $paises[$pais_ip];
} else {
return "IP sem informações de localização";
}

} else {

if($pais_ip) {
return $pais_ip;
} else {
return "Desconhecido";
}

}

}

// Função para obter as estatisticas do streaming no servidor aacplus
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