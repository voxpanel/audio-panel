<?php

list($codigo_stm, $dj_login, $dj_senha) = explode("|",$_SESSION["dj_logado"]);

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$codigo_stm."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="inc/estilo-dj.css" rel="stylesheet" type="text/css" />
<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
   window.onload = function() {
   	initialize();
    setTimeout("window.location.reload(true);",30000);
   };
</script>
<style type="text/css">
<!--
body {
	overflow-x: hidden;
}
-->
</style>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
  <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
    <td width="30%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_ip']; ?></td>
    <td width="20%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_pais']; ?></td>
    <td width="20%" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_player']; ?></td>
    <td width="30%" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_tempo_conectado']; ?></td>
  </tr>
<?php
$i = 1;

if($dados_stm["aacplus"] == 'sim') {

$stats_shoutcast = estatistica_streaming_shoutcast($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
$stats_aacplus = estatistica_streaming_aacplus($dados_servidor_aacplus["ip"],$dados_stm["porta"],$dados_servidor_aacplus["senha"]);
$estatisticas = $stats_shoutcast.$stats_aacplus;

} else {

$stats_shoutcast = estatistica_streaming_shoutcast($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha"]);
$estatisticas = $stats_shoutcast;

}

if($stats_shoutcast) {

$estatisticas = explode("-",substr($estatisticas, 0, -1));

foreach($estatisticas as $estatistica) {

list($ip, $tempo_conectado, $pais_sigla, $pais_nome, $player) = explode("|",$estatistica);

if($ip != $dados_servidor_aacplus["ip"]) {

echo "
  <tr>
    <td height='23' class='texto_padrao'>&nbsp;".$ip."</td>
    <td height='23' class='texto_padrao'>&nbsp;<img src='/img/icones/paises/".strtolower($pais_sigla).".png' border='0' align='absmiddle' />&nbsp;".$pais_nome."</td>
    <td height='23' class='texto_padrao'>&nbsp;".$player."</td>
	<td height='23' class='texto_padrao'>&nbsp;".$tempo_conectado."</td>
  </tr>
";

// Dados para o mapa
$dados_ip = geoip_record_by_name($ip);

if($dados_ip["latitude"] && $dados_ip["longitude"]) {

$dados_LatLng_array .= "myLatlng".$i.",";
$dados_LatLng_array_nome .= "\"".$lang['lang_info_ouvintes_conectados_ouvinte'].": ".$ip."\",";
$dados_LatLng_array_info .= "\"<div class='texto_padrao' style='text-align:left'><strong>".$lang['lang_info_ouvintes_conectados_ip'].":</strong> ".$ip."<br><strong>".$lang['lang_info_ouvintes_conectados_pais'].":</strong> ".$pais_nome."&nbsp;<img src='/img/icones/paises/".strtolower($pais_sigla).".png' border='0' align='absmiddle' /><br><strong>".$lang['lang_info_ouvintes_conectados_player'].":</strong> ".$player."<br><strong>".$lang['lang_info_ouvintes_conectados_tempo_conectado'].":</strong> ".$tempo_conectado."</div>\",";
$dados_LatLng .= "var myLatlng".$i." = new google.maps.LatLng( ".$dados_ip["latitude"].", ".$dados_ip["longitude"].");\n";
$i++;
}

}

}

} else {

echo "
  <tr>
    <td height='30' colspan='4' align='center' class='texto_status_erro'>".$lang['lang_info_ouvintes_conectados_info_sem_ouvintes']."</td>
  </tr>
";

}
?>
</table>
<br />
<script type="text/javascript">
function initialize() {

<?php
		
  echo substr($dados_LatLng, 0, -1);
		
?>
  
  var locationArray = [<?php echo substr($dados_LatLng_array, 0, -1); ?>];
  var locationArrayName = [<?php echo substr($dados_LatLng_array_nome, 0, -1); ?>];
  var locationArrayInfo = [<?php echo substr($dados_LatLng_array_info, 0, -1); ?>];
  
  var myOptions = {
  zoom: 2,
  center: new google.maps.LatLng(5,-20),
  mapTypeId: google.maps.MapTypeId.ROADMAP,
  }
  
  var map = new google.maps.Map(document.getElementById("mapa_ips"), myOptions);
  
  for(var cont = 0; cont < locationArray.length; cont++) {
  
  var infowindow = new google.maps.InfoWindow({
      content: "Carregando..."
  });
  
  var marker = new google.maps.Marker({
    position: locationArray[cont],
    title: locationArrayName[cont],
	html: locationArrayInfo[cont]
  });
  
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(this.html);
	infowindow.open(map,this);
  });

  marker.setMap(map);
  }
}
</script>
<div id="mapa_ips" style="width: 100%; height: 300px; margin:0px auto" align="center"></div>
</body>
</html>
