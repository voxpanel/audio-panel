<?php
if(query_string('1') != '' && !is_numeric(query_string('1'))) {
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('1'),"D")."'"));
} elseif(is_numeric(query_string('1'))) {
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('1')."'"));
} else {
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
}

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

if($dados_stm["idioma_painel"]) {
require_once("inc/lang-".$dados_stm["idioma_painel"].".php");
} else {
require_once("inc/lang-pt-br.php");
}

$status_streaming = status_streaming($dados_servidor["ip"],$dados_stm["porta"]);
	
if($status_streaming == "ligado") {

if($dados_stm["senha_admin"]) {

// Dados do streaming
if($dados_config["usar_cdn"] == "sim") {

$info = @file_get_contents("http://".$dados_config["dominio_cdn"]."/shoutcast-info.php?ip=".$dados_servidor["ip"]."&porta=".$dados_stm["porta"]."&ponto=1");

list($ouvintes_total, $ouvintes, $bitrate, $encoder, $musica_atual, $pico_ouvintes, $proxima_musica, $genero) = explode("|", $info);

$encoder = (preg_match('/aacp/i',$encoder)) ? "AACPlus" : "MP3";
$proxima_musica = str_replace("\n","",$proxima_musica);

} else {

$info = shoutcast_info($dados_servidor["ip"],$dados_stm["porta"],1);
$musica_atual = $info["musica"];
$bitrate = $info["bitrate"];
$encoder = (preg_match('/aacp/i',$info["encoder"])) ? "AACPlus" : "MP3";
$proxima_musica = str_replace("\n","",$info["proxima_musica"]);
$pico_ouvintes = $info["pico_ouvintes"];

}

$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_pontos > 0) {

$sql = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
while ($dados_ponto = mysql_fetch_array($sql)) {
$stats_shoutcast[] = estatistica_ouvintes_conectados_shoutcast($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"],$dados_ponto["id"]);
}

$stats_shoutcast = flatten_array($stats_shoutcast);

} else {
$stats_shoutcast = estatistica_ouvintes_conectados_shoutcast($dados_servidor["ip"],$dados_stm["porta"],$dados_stm["senha_admin"],1);
}

if($dados_stm["aacplus"] == 'sim') {
$stats_aacplus = estatistica_ouvintes_conectados_aacplus($dados_servidor_aacplus["ip"],$dados_stm["porta"],$dados_servidor_aacplus["senha"]);
}

if(count($stats_aacplus) > 0) {
$array_estatisticas = @array_merge($stats_shoutcast, $stats_aacplus);
} else {
$array_estatisticas = $stats_shoutcast;
}

$array_estatisticas = @array_filter($array_estatisticas);

if(count($array_estatisticas) > 0 && $dados_stm["aacplus"] == 'sim') {

foreach($array_estatisticas as $estatistica){

if(!preg_match('/'.$dados_servidor_aacplus["ip"].'/',$estatistica)) {
$estatisticas[] = $estatistica;
}

}
} else {
$estatisticas = $array_estatisticas;
}

$porcentagem_uso_ouvintes = ($dados_stm["ouvintes"] == 0) ? "0" : count($estatisticas)*100/$dados_stm["ouvintes"];
$porcentagem_uso_ouvintes = ($porcentagem_uso_ouvintes < 1 && count($estatisticas) > 0) ? "1" : $porcentagem_uso_ouvintes;

$porcentagem_uso_ftp = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];

} else {

die('<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
<tr>
        <td width="50" height="50" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="750"align="left" scope="col" style="color: #AB1C10;	font-family: Geneva, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;">'.$lang['lang_info_ouvintes_conectados_alerta_senha_admin'].'</td>
  </tr>
</table>');

}

} else {

die('<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
	<tr>
		<td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    	<td align="left" scope="col" style="color: #AB1C10;	font-family: Geneva, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold;">'.$lang['lang_info_ouvintes_conectados_alerta_streaming_desligado'].'</td>
  	</tr>
</table>');

}

$lista_chaves_api_google_maps = array("AIzaSyA_cgB0ttArNqEKihk0V6iOKeZoo1ZNHWs","AIzaSyA_cgB0ttArNqEKihk0V6iOKeZoo1ZNHWs");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?sensor=false&key=<?php echo $lista_chaves_api_google_maps[array_rand($lista_chaves_api_google_maps)]; ?>" type="text/javascript"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-3d.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<!--[if IE]><script type="text/javascript" src="/inc/excanvas.js"></script><![endif]-->
<script src="/inc/jquery.knob.min.js"></script>
<script type="text/javascript">
   window.onload = function() {
    setTimeout("window.location.reload(true);",60000);
	initialize();
	fechar_log_sistema();
	// Status de exibição dos quadros
	document.getElementById('tabela_info').style.display=getCookie('tabela_info');
	document.getElementById('tabela_info_uso').style.display=getCookie('tabela_info_uso');
	document.getElementById('tabela_ouvintes_conectados').style.display=getCookie('tabela_ouvintes_conectados');
	document.getElementById('tabela_mapa_ouvintes_conectados').style.display=getCookie('tabela_mapa_ouvintes_conectados');
	document.getElementById('tabela_grafico_ouvintes_conectados').style.display=getCookie('tabela_grafico_ouvintes_conectados');
	
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
<?php if($dados_stm["aacplus"] == 'sim' && $encoder == 'MP3') { ?>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; margin-bottom:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
	<tr>
		<td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    	<td align="left" class="texto_log_sistema_alerta" scope="col"><?php echo $lang['lang_info_ouvintes_conectados_info_encoder']; ?></td>
  	</tr>
</table>
<?php } ?>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px;">
	<tr>
        <td width="395" valign="top"><div id="quadro">
          <div id="quadro-topo"><strong><?php echo $lang['lang_info_ouvintes_conectados_trans_atual_tab_titulo']; ?></strong><span><a href="javascript:abrir_janela('/ouvintes-conectados',820,650 );" class="texto_padrao"><?php echo $lang['lang_info_ouvintes_conectados_botao_nova_janela']; ?></a>&nbsp;<img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span></div>
          <div class="texto_padrao_pequeno" id="quadro-conteudo">
            <table width="380" border="0" align="center" cellpadding="0" cellspacing="0" id="tabela_info" style="display:block">
              <tr>
                <td width="130" height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque" scope="col"><?php echo $lang['lang_info_ouvintes_conectados_trans_atual_musica_atual']; ?></td>
                <td width="255" align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno" scope="col"><?php echo $musica_atual; ?></td>
              </tr>
              <?php if($proxima_musica) { ?>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque"><?php echo $lang['lang_info_ouvintes_conectados_trans_atual_proxima_musica']; ?></td>
                <td align="left" class="texto_padrao_pequeno"><?php echo $proxima_musica; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_ouvintes_conectados_trans_atual_encoder']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo $encoder; ?></td>
              </tr>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque"><?php echo $lang['lang_info_ouvintes_conectados_trans_atual_bitrate']; ?></td>
                <td align="left" class="texto_padrao_pequeno"><?php echo $bitrate; ?>&nbsp;Kbps</td>
              </tr>
              <tr>
                <td height="25" align="left" bgcolor="#F8F8F8" class="texto_padrao_destaque"><?php echo $lang['lang_info_ouvintes_conectados_trans_atual_pico_ouvintes']; ?></td>
                <td align="left" bgcolor="#F8F8F8" class="texto_padrao_pequeno"><?php echo $pico_ouvintes; ?></td>
              </tr>
              <?php if(!$proxima_musica) { ?>
              <tr>
                <td height="25" align="left" class="texto_padrao_destaque"></td>
                <td align="left" class="texto_padrao_pequeno"></td>
              </tr>
              <?php } ?>              
            </table>
          </div>
        </div></td>
	    <td width="10">&nbsp;</td>
	    <td width="395" valign="top"><div id="quadro">
                <div id="quadro-topo"> <strong><?php echo $lang['lang_info_ouvintes_conectados_uso_plano_tab_titulo']; ?></strong><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" onclick="hide_show('tabela_info_uso');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span></div>
        <div class="texto_padrao_pequeno" id="quadro-conteudo">
          <table width="380" border="0" align="center" cellpadding="0" cellspacing="0" id="tabela_info_uso" style="display:block">
            <tr>
              <td width="190" align="center" scope="col"><input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="100" data-height="100" value="<?php echo round($porcentagem_uso_ouvintes); ?>" id="grafico_uso_ouvintes" /></td>
              <td width="190" align="center" scope="col"><input class="knob" data-fgcolor="#0066CC" data-thickness=".3" readonly="readonly" data-min="0" data-max="100" data-width="100" data-height="100" value="<?php echo round($porcentagem_uso_ftp); ?>" id="grafico_uso_ftp" /></td>
            </tr>
            <tr>
              <td height="25" align="center" class="texto_padrao_pequeno" scope="col"><?php echo count($estatisticas)." ".$lang['lang_info_ouvintes_conectados_uso_plano_ouvintes']; ?></td>
              <td height="25" align="center" class="texto_padrao_pequeno" scope="col"><?php echo $lang['lang_info_ouvintes_conectados_uso_plano_ftp']; ?></td>
            </tr>
          </table>
        </div>
        </div>
        </td>
	</tr>
</table>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px;">
	  <tr>
        <td height="30" align="left" class="texto_padrao_destaque"><div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_ouvintes_conectados_tab_titulo']; ?></strong><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" align="absmiddle" onclick="hide_show('tabela_ouvintes_conectados');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span></div>
          <div class="texto_medio" id="quadro-conteudo">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" id="tabela_ouvintes_conectados" style="display:block">
                <tr>
                  <td>
                  <table width="788" border="0" align="center" cellpadding="0" cellspacing="0" style="border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
  					<tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
  					<td width="268" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_ip']; ?></td>
  					<td width="220" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_pais']; ?></td>
  					<td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_player']; ?></td>
  					<td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_ouvintes_conectados_tempo_conectado']; ?></td>
  				</tr>
<?php
$i = 1;

if(count($estatisticas) > 0) {

foreach($estatisticas as $estatistica) {

list($ip, $tempo_conectado, $pais_sigla, $pais_nome, $player, $kick) = explode("|",$estatistica);

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

} else {

echo "
  <tr>
    <td height='30' colspan='4' align='center' class='texto_status_erro'>".$lang['lang_info_ouvintes_conectados_info_sem_ouvintes']."</td>
  </tr>
";

}
?>
			</table>
                  </td>
                </tr>
            </table>
          </div>
        </div></td>
      </tr>
</table>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px;">
<tr>
        <td height="30" align="left" class="texto_padrao_destaque"><div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_ouvintes_conectados_mapa_tab_titulo']; ?></strong><span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" align="absmiddle" onclick="hide_show('tabela_mapa_ouvintes_conectados');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="788" border="0" cellspacing="0" cellpadding="0" align="center" id="tabela_mapa_ouvintes_conectados" style="display:block">
                <tr>
                  <td height="25" class="texto_padrao_pequeno">
                  <div id="mapa_ips" style="width:100%; height: 400px; margin:0px auto" align="center"></div>
                  <table width="788" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
			  <tr>
    					<td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
    					<td align="left" class="texto_log_sistema_alerta" scope="col"><?php echo $lang['lang_info_ouvintes_conectados_info_ip_duplicado']; ?></td>
  					</tr>
				  </table>
                  </td>
                </tr>
              </table>
          </div>
        </div></td>
      </tr>
    </table>
	<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px;">
	  <tr>
        <td height="30" align="left" class="texto_padrao_destaque"><div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_ouvintes_conectados_estatisticas_tab_titulo']; ?></strong>
          <span><img src="/img/icones/img-icone-olho-64x64.png" width="16" height="16" align="absmiddle" onclick="hide_show('tabela_grafico_ouvintes_conectados');" style="cursor:pointer; padding-top:7px;" title="Ocultar/Hide" /></span></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="788" border="0" cellspacing="0" cellpadding="0" align="center" id="tabela_grafico_ouvintes_conectados" style="display:block">
                <tr>
                  <td height="25" class="texto_padrao_pequeno" align="center">
                  <div id="container" style="width:780px; height: 350px; margin: 0 auto"></div>
                  </td>
                </tr>
              </table>
          </div>
        </div></td>
      </tr>
    </table>
<br />
<script type="text/javascript">
// Google Maps
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
// Graficos
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area'
            },
            title: {
                text: '<?php echo $lang['lang_info_ouvintes_conectados_estatisticas_titulo']; ?>'
            },
			subtitle: {
                text: '<?php echo formatar_data($dados_stm["formato_data"], date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")-1, date("Y"))), $dados_stm["timezone"]). " - " . formatar_data($dados_stm["formato_data"], date("Y-m-d"), $dados_stm["timezone"]);?>'
            },
            xAxis: {
                categories: ['00:00-00:59','01:00-01:59','02:00-02:59','03:00-03:59','04:00-04:59','05:00-05:59','06:00-06:59','07:00-07:59','08:00-08:59','09:00-09:59','10:00-10:59','11:00-11:59','12:00-12:59','13:00-13:59','14:00-14:59','15:00-15:59','16:00-16:59','17:00-17:59','18:00-18:59','19:00-19:59','20:00-20:59','21:00-21:59','22:00-22:59','23:00-23:59'],
                tickmarkPlacement: 'on',
                title: {
                    enabled: true,
					text: '<?php echo $lang['lang_info_ouvintes_conectados_estatisticas_info_hora']; ?>'
                }
            },
            yAxis: {
                title: {
                    text: '<?php echo $lang['lang_info_ouvintes_conectados_estatisticas_total_ouvintes']; ?>'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+this.x+': '+ Highcharts.numberFormat(this.y, 0, ',') +' <?php echo $lang['lang_info_ouvintes_conectados_estatisticas_balao_ouvintes']; ?>';
                }
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
					cursor: 'pointer',
                    marker: {
                        lineWidth: 1,
                        lineColor: '#666666',
						enabled: false,
                    	symbol: 'circle',
                    	radius: 2,
                    	states: {
                        	hover: {
                            enabled: true
                        	}
						}
                    }
                }
            },
            series: [{
                name: '<?php echo $lang['lang_info_ouvintes_conectados_estatisticas_total_ouvintes_ontem']; ?>',
                data: [<?php
				
				$data_ontem = date("Y-m-d",mktime (0, 0, 0, date("m")  , date("d")-1, date("Y")));
				
				for($i=0;$i<=23;$i++){
				
				$hora = sprintf("%02s",$i);
				
				$total_ouvintes = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND data = '".$data_ontem."' AND HOUR(hora) = '".$hora."'"));
				
				$array_total_ouvintes .= $total_ouvintes.",";
				
				}
				echo substr($array_total_ouvintes, 0, -1);	
				
				unset($array_total_ouvintes);
				unset($total_ouvintes);
				?>]
				}, {
				name: '<?php echo $lang['lang_info_ouvintes_conectados_estatisticas_total_ouvintes_hoje']; ?>',
                data: [<?php
				
				for($i=0;$i<=23;$i++){
				
				$hora = sprintf("%02s",$i);
				
				$total_ouvintes = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND data = '".date("Y-m-d")."' AND HOUR(hora) = '".$hora."'"));
				
				$array_total_ouvintes .= $total_ouvintes.",";
				
				}
				echo substr($array_total_ouvintes, 0, -1);	
				
				unset($array_total_ouvintes);
				unset($total_ouvintes);
				?>]
            }]
        });
    });
    
});
// Barra de Progresso Ouvintes
$(function() {
	$(".knob").knob();
	document.getElementById('grafico_uso_ouvintes').value=document.getElementById('grafico_uso_ouvintes').value+'%';
	document.getElementById('grafico_uso_ftp').value=document.getElementById('grafico_uso_ftp').value+'%';
});
</script>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"><img src="/img/ajax-loader.gif" /></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>