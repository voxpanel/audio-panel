<?php
require_once("admin/inc/protecao-final.php");

$estatistica = query_string('1');
$mes = query_string('2');
$ano = query_string('3');

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Estatísticas do Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-3d.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
  <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px; background-color:#FFFF66; border:#DFDF00 1px solid">
  <tr>
    <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
    <td width="670" align="left" class="texto_log_sistema_alerta" scope="col"><?php echo $lang['lang_info_estatisticas_info']; ?></td>
  </tr>
</table>
	<table width="510" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
      <td height="50" align="center" valign="top" style="padding-left:5px; padding-right:5px">
      <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo $lang['lang_info_estatisticas_estatistica']; ?></strong></div>
   		  <div class="texto_medio" id="quadro-conteudo">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_estatisticas_estatistica']; ?></td>
        <td width="380" align="left">
        <select name="estatistica" class="input" id="estatistica" style="width:255px;" onchange="tipo_estatistica(this.value);">
          <option value="1"><?php echo $lang['lang_info_estatisticas_estatistica_ouvintes']; ?></option>
          <option value="2"><?php echo $lang['lang_info_estatisticas_estatistica_ouvintes_meses_ano']; ?></option>
          <option value="3"><?php echo $lang['lang_info_estatisticas_estatistica_tempo_conectado']; ?></option>
          <option value="4"><?php echo $lang['lang_info_estatisticas_estatistica_paises']; ?></option>
          <option value="5"><?php echo $lang['lang_info_estatisticas_estatistica_players']; ?></option>
          <option value="6"><?php echo $lang['lang_info_estatisticas_estatistica_ouvintes_hora']; ?></option>
        </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="left">
        <table width="500" border="0" cellspacing="0" cellpadding="0" id="tabela_data">
          <tr>
            <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_estatisticas_periodo']; ?></td>
        <td width="380" align="left">
        <select name="mes" class="input" id="mes" style="width:162px;">
          <option value="01" <?php if(date("m") == '01') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_01']; ?></option>
          <option value="02" <?php if(date("m") == '02') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_02']; ?></option>
          <option value="03" <?php if(date("m") == '03') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_03']; ?></option>
          <option value="04" <?php if(date("m") == '04') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_04']; ?></option>
          <option value="05" <?php if(date("m") == '05') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_05']; ?></option>
          <option value="06" <?php if(date("m") == '06') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_06']; ?></option>
          <option value="07" <?php if(date("m") == '07') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_07']; ?></option>
          <option value="08" <?php if(date("m") == '08') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_08']; ?></option>
          <option value="09" <?php if(date("m") == '09') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_09']; ?></option>
          <option value="10" <?php if(date("m") == '10') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_10']; ?></option>
          <option value="11" <?php if(date("m") == '11') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_11']; ?></option>
          <option value="12" <?php if(date("m") == '12') { echo ' selected="selected"'; } ?>><?php echo $lang['lang_info_estatisticas_periodo_12']; ?></option>
        </select>
        <select name="ano" class="input" id="ano" style="width:90px;">
			<?php
				$ano_inicial = date("Y")-1;
				$ano_final = date("Y")+1;
				$qtd = $ano_final-$ano_inicial;
					for($i=0; $i <= $qtd; $i++) {
							if(sprintf("%02s",$ano_inicial+$i) == date("Y")) {
								echo "<option value=\"".sprintf("%02s",$ano_inicial+$i)."\" selected=\"selected\">".sprintf("%02s",$ano_inicial+$i)."</option>\n";
							} else {
								echo "<option value=\"".sprintf("%02s",$ano_inicial+$i)."\">".sprintf("%02s",$ano_inicial+$i)."</option>\n";
							}
					}
			?>
        </select></td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="button" class="botao" value="<?php echo $lang['lang_botao_titulo_visualizar']; ?>" onclick="window.location = '/estatisticas/'+document.getElementById('estatistica').value+'/'+document.getElementById('mes').value+'/'+document.getElementById('ano').value+'';" /></td>
      </tr>
    </table>
    </div>
      </div>
      </td>
    </tr>
  </table>
<br />
<center>
<?php if($estatistica == "1") { ?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area'
            },
            title: {
                text: '<?php echo $lang['lang_info_estatisticas_info_stats_ouvintes']; ?>'
            },
			subtitle: {
                text: '<?php echo $lang['lang_info_estatisticas_periodo_'.$mes.'']; ?> <?php echo $ano; ?>'
            },
            xAxis: {
                categories: [
				<?php
				$array_dias_meses = array("01" => "31", "02" => "29", "03" => "31", "04" => "30", "05" => "31", "06" => "30", "07" => "31", "08" => "31", "09" => "30", "10" => "31", "11" => "30", "12" => "31");
				
				for($i=1;$i<=$array_dias_meses[$mes];$i++){
				
				$dias .= sprintf("%02s",$i).",";

				}
				
				echo substr($dias, 0, -1);			
				?>				
				],
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: '<?php echo $lang['lang_info_estatisticas_info_stats_ouvintes_total']; ?>'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x +'/<?php echo $mes;?>/<?php echo $ano;?>: '+ Highcharts.numberFormat(this.y, 0, ',') +' <?php echo $lang['lang_info_estatisticas_legenda_ouvintes']; ?>';
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
                name: '<?php echo $lang['lang_info_estatisticas_estatistica_ouvintes']; ?>',
                data: [
				<?php
				
				for($i=1;$i<=$array_dias_meses["".$mes.""];$i++){
				
				$dia = sprintf("%02s",$i);
				
				$total_ouvintes = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND YEAR(data) = '".$ano."' AND MONTH(data) = '".$mes."' AND DAY(data) = '".$dia."'"));
				
				echo $total_ouvintes.",";
				echo "\n";
				
				}
				?>
				]
            }]
        });
    });
    
});
</script>

<?php } else if($estatistica == "2") { ?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area'
            },
            title: {
                text: '<?php echo $lang['lang_info_estatisticas_info_stats_ouvintes']; ?>'
            },
			subtitle: {
                text: '<?php echo $ano; ?>'
            },
            xAxis: {
                categories: [
				<?php
				echo "'".$lang['lang_info_estatisticas_periodo_01']."','".$lang['lang_info_estatisticas_periodo_02']."','".$lang['lang_info_estatisticas_periodo_03']."','".$lang['lang_info_estatisticas_periodo_04']."','".$lang['lang_info_estatisticas_periodo_05']."','".$lang['lang_info_estatisticas_periodo_06']."','".$lang['lang_info_estatisticas_periodo_07']."','".$lang['lang_info_estatisticas_periodo_08']."','".$lang['lang_info_estatisticas_periodo_09']."','".$lang['lang_info_estatisticas_periodo_10']."','".$lang['lang_info_estatisticas_periodo_11']."','".$lang['lang_info_estatisticas_periodo_12']."'";
				?>				
				],
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: '<?php echo $lang['lang_info_estatisticas_info_stats_ouvintes_total']; ?>'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x +'/<?php echo $ano;?>: '+ Highcharts.numberFormat(this.y, 0, ',') +' <?php echo $lang['lang_info_estatisticas_legenda_ouvintes']; ?>';
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
                name: '<?php echo $lang['lang_info_estatisticas_estatistica_ouvintes_meses_ano']; ?>',
                data: [
				<?php
				
				$array_meses_ano = array("01","02","03","04","05","06","07","08","09","10","11","12");
				
				foreach($array_meses_ano as $mes_ano) {
				
				$mes_ano = sprintf("%02s",$mes_ano);
				
				$total_ouvintes = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND YEAR(data) = '".$ano."' AND MONTH(data) = '".$mes_ano."'"));
				
				$total_ouvintes_meses .= $total_ouvintes.",";
				
				}
				echo substr($total_ouvintes_meses, 0, -1);	
				?>
				]
            }]
        });
    });
    
});
</script>

<?php } else if($estatistica == "3") { ?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area'
            },
            title: {
                text: '<?php echo $lang['lang_info_estatisticas_info_stats_tempo_conectado']; ?>'
            },
			subtitle: {
                text: '<?php echo $lang['lang_info_estatisticas_periodo_'.$mes.'']; ?> <?php echo $ano;?>'
            },
            xAxis: {
                categories: [
				<?php
				$array_dias_meses = array("01" => "31", "02" => "28", "03" => "31", "04" => "30", "05" => "31", "06" => "30", "07" => "31", "08" => "31", "09" => "30", "10" => "31", "11" => "30", "12" => "31");
				
				for($i=1;$i<=$array_dias_meses[$mes];$i++){
				
				$dias .= sprintf("%02s",$i).",";

				}
				
				echo substr($dias, 0, -1);			
				?>				
				],
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: '<?php echo $lang['lang_info_estatisticas_info_stats_tempo_conectado_tempo_minutos']; ?>'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x +'/<?php echo $mes;?>/<?php echo $ano;?>: '+ Highcharts.numberFormat(this.y, 0, ',') +' <?php echo $lang['lang_info_estatisticas_legenda_minutos']; ?>';
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
                name: '<?php echo $lang['lang_info_estatisticas_info_stats_tempo_conectado_tempo_minutos']; ?>',
                data: [
				<?php
				for($i=1;$i<=$array_dias_meses["".$mes.""];$i++){
				
				$dia = sprintf("%02s",$i);
				
				$soma = 0;
				$contador = 0;
				
				$query = mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND YEAR(data) = '".$ano."' AND MONTH(data) = '".$mes."' AND DAY(data) = '".$dia."'");
				
				while ($dados_estatistica = mysql_fetch_array($query)) {

				$soma += $dados_estatistica["tempo_conectado"];
				$contador++;

				}
				
				$media = ($contador > 0) ? $soma / $contador : '0';
				
				echo date('i',mktime(0,0,$media,15,03,2013)).",";
				echo "\n";
				
				}
				?>
				]
            }]
        });
    });
    
});

</script>

<?php } else if($estatistica == "4") { ?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'pie',
            		options3d: {
                		enabled: true,
                		alpha: 45,
                		beta: 0
            		}
            },
            title: {
                text: '<?php echo $lang['lang_info_estatisticas_info_stats_pais']; ?>'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 0, ',') +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
					depth: 35,
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '<?php echo $lang['lang_info_estatisticas_info_stats_pais_ouvintes']; ?>',
                data: [
				
				<?php
				
				$sql_paises = mysql_query("SELECT distinct(pais) as pais, count(pais) as total FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' GROUP by pais ORDER by total DESC LIMIT 5");
				while ($dados_pais_ip = mysql_fetch_array($sql_paises)) {

				if($dados_pais_ip["total"] > 1) {
				
				echo "['".$dados_pais_ip["pais"]."', ".$dados_pais_ip["total"]."],";
				echo "\n";

				}

				}
				
				?>

                ]
            }]
        });
    });
    
});
</script>

<?php } else if($estatistica == "5") { ?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'pie',
            		options3d: {
                		enabled: true,
                		alpha: 45,
                		beta: 0
            		}
            },
            title: {
                text: '<?php echo $lang['lang_info_estatisticas_info_stats_players']; ?>'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 0, ',') +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
					depth: 35,
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                data: [
				
				<?php
				
				$sql_players = mysql_query("SELECT distinct(player) as player, count(player) as total FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND player != '' GROUP by player ORDER by total DESC");
				while ($dados_player = mysql_fetch_array($sql_players)) {

				if($dados_player["total"] > 0) {
				
				echo "['".$dados_player["player"]."', ".$dados_player["total"]."],";
				echo "\n";

				}

				}
				
				?>

                ]
            }]
        });
    });
    
});
</script>

<?php } else if($estatistica == "6") { ?>
<script type="text/javascript">

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area'
            },
            title: {
                text: '<?php echo $lang['lang_info_estatisticas_estatistica_ouvintes_hora']; // Ouvintes Conectados por Hora ?>'
            },
			subtitle: {
                text: '<?php echo $lang['lang_info_estatisticas_periodo_'.$mes.'']; ?> <?php echo $ano;?>'
            },
            xAxis: {
                categories: ['00:00-00:59','01:00-01:59','02:00-02:59','03:00-03:59','04:00-04:59','05:00-05:59','06:00-06:59','07:00-07:59','08:00-08:59','09:00-09:59','10:00-10:59','11:00-11:59','12:00-12:59','13:00-13:59','14:00-14:59','15:00-15:59','16:00-16:59','17:00-17:59','18:00-18:59','19:00-19:59','20:00-20:59','21:00-21:59','22:00-22:59','23:00-23:59'],
                tickmarkPlacement: 'on',
            },
            yAxis: {
                title: {
                    text: '<?php echo $lang['lang_info_estatisticas_info_stats_ouvintes_total']; // Total de Ouvintes ?>'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+this.x+': '+ Highcharts.numberFormat(this.y, 0, ',') +' <?php echo $lang['lang_info_estatisticas_legenda_ouvintes']; // ouvinte(s) ?>';
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
                name: '<?php echo $lang['lang_info_estatisticas_estatistica_ouvintes']; ?>',
                data: [<?php
				
				for($i=0;$i<=23;$i++){
				
				$hora = sprintf("%02s",$i);
				
				$total_ouvintes = mysql_num_rows(mysql_query("SELECT * FROM estatisticas where codigo_stm = '".$dados_stm["codigo"]."' AND YEAR(data) = '".$ano."' AND MONTH(data) = '".$mes."' AND HOUR(hora) = '".$hora."'"));
				
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

</script>

<?php } ?>
<div id="container" style="min-width: 600px; height: 350px; margin: 0 auto"></div>
</center>
</div>
</body>
</html>
