<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));

if($_POST["cadastrar"]) {

list($dia,$mes,$ano) = explode("/",$_POST["data"]);
$data = $ano."-".$mes."-".$dia;

if(count($_POST["dias"]) > 0){
	$dias = implode(",",$_POST["dias"]);
}

if(!filter_var($_POST["servidor"], FILTER_VALIDATE_URL)) {
die ("<script> alert(\"Ooops!\\n\\n".lang_acao_gerenciador_agendamentos_resultado_erro_url."\");
 			 window.location = 'javascript:history.back(-1)'; </script>");
}

mysql_query("INSERT INTO relay_agendamentos (codigo_stm,servidor,frequencia,data,hora,minuto,duracao_hora,duracao_minuto,dias) VALUES ('".$dados_stm["codigo"]."','".$_POST["servidor"]."','".$_POST["frequencia"]."','".$data."','".$_POST["hora"]."','".$_POST["minuto"]."','".$_POST["duracao_hora"]."','".$_POST["duracao_minuto"]."','".$dias.",')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] .= status_acao("".lang_acao_gerenciador_agendamentos_resultado_ok."","ok");


header("Location: /gerenciar-agendamentos-relay");
exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/inc/ajax-streaming.js"></script>
<script type="text/javascript" src="/inc/javascript.js"></script>
<script type="text/javascript" src="/inc/javascript-abas.js"></script>
<script type="text/javascript" src="/inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
  <form method="post" action="/gerenciar-agendamentos-relay" style="padding:0px; margin:0px" name="agendamentos">
  <div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_agendamentos_relay_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_aba_agendamentos']; ?></h2>
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="220" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_servidor']; ?></td>
      <td width="520" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_horario_agendado']; ?></td>
      <td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_agendamentos_relay_executar_acao']; ?></td>
    </tr>
<?php
$total_agendamentos = mysql_num_rows(mysql_query("SELECT * FROM relay_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data"));

if($total_agendamentos > 0) {

$sql = mysql_query("SELECT * FROM relay_agendamentos where codigo_stm = '".$dados_stm["codigo"]."' ORDER by data");
while ($dados_agendamento = mysql_fetch_array($sql)) {

list($ano,$mes,$dia) = explode("-",$dados_agendamento["data"]);
$data = $dia."/".$mes."/".$ano;

if($dados_agendamento["frequencia"] == "1") {
$descricao = "".$lang['lang_info_gerenciador_agendamentos_relay_info_frequencia1']." ".$data." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"]."";
} elseif($dados_agendamento["frequencia"] == "2") {
$descricao = "".$lang['lang_info_gerenciador_agendamentos_relay_info_frequencia2']." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"]."";
} else {

$array_dias = explode(",",$dados_agendamento["dias"]);

foreach($array_dias as $dia) {

if($dia == "1") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_segunda'];
} elseif($dia == "2") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_terca'];
} elseif($dia == "3") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_quarta'];
} elseif($dia == "4") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_quinta'];
} elseif($dia == "5") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_sexta'];
} elseif($dia == "6") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_sabado'];
} elseif($dia == "7") {
$dia_nome = $lang['lang_info_gerenciador_agendamentos_relay_domingo'];
} else {
$dia_nome = "";
}

$lista_dias .= "".$dia_nome.", ";

}

$descricao = "".$lang['lang_info_gerenciador_agendamentos_relay_info_frequencia3']." ".substr($lista_dias, 0, -2)." ".$dados_agendamento["hora"].":".$dados_agendamento["minuto"]."";
}

$agendamento_code = code_decode($dados_agendamento["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_agendamento["servidor"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$descricao."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

echo "<select style='width:100%' id='".$agendamento_code."' onchange='executar_acao_streaming_autodj(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_gerenciador_agendamentos_relay_acao']."</option>
  <option value='autodj-remover-agendamento-relay'>".$lang['lang_info_gerenciador_agendamentos_relay_acao_remover']."</option>
</select>";

echo "</td>
</tr>";

unset($lista_dias);
unset($dia_nome);
}

} else {

echo "<tr>
    <td height='23' colspan='3' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
  </table>
  <br />
<br />
<br />
<br />
<br />
  </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_aba_cadastrar_agendamento']; ?></h2>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="160" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_servidor']; ?></td>
        <td width="730" align="left" class="texto_padrao_destaque">
        <input name="servidor" type="text" class="input" id="servidor" style="width:250px;" value="http://000.000.000.000:0000" />
        &nbsp;( http://<span class="texto_padrao_vermelho">xxxxxx</span>:<span class="texto_padrao_vermelho">xxxx</span> )</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencias']; ?></td>
        <td align="left">
        <select name="frequencia" id="frequencia" style="width:250px;" onchange="valida_opcoes_frequencia(this.value);">
          <option value="1" selected="selected"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencia1']; ?></option>
          <option value="2"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencia2']; ?></option>
          <option value="3"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_frequencia3']; ?></option>
        </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_data_inicio']; ?></td>
        <td align="left"><input name="data" type="text" id="data" onkeypress="return txtBoxFormat(this, '99/99/9999', event);" value="__/__/____" maxlength="10" onclick="this.value=''" style="width:75px;" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_horario_inicio']; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="hora" id="hora" style="width:50px;">
          <option value="00">00</option>
          <option value="01">01</option>
          <option value="02">02</option>
          <option value="03">03</option>
          <option value="04">04</option>
          <option value="05">05</option>
          <option value="06">06</option>
          <option value="07">07</option>
          <option value="08">08</option>
          <option value="09">09</option>
          <option value="10">10</option>
          <option value="11">11</option>
          <option value="12">12</option>
          <option value="13">13</option>
          <option value="14">14</option>
          <option value="15">15</option>
          <option value="16">16</option>
          <option value="17">17</option>
          <option value="18">18</option>
          <option value="19">19</option>
          <option value="20">20</option>
          <option value="21">21</option>
          <option value="22">22</option>
          <option value="23">23</option>
        </select>
          <span class="texto_padrao_titulo">:</span>&nbsp;
          <select name="minuto" id="minuto" style="width:50px;">
            <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
          </select></td>
      </tr>
      <tr>
        <td height="45" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_duracao']; ?></td>
        <td align="left" class="texto_padrao_pequeno">
        <select name="duracao_hora" id="duracao_hora" style="width:50px;">
          <option value="00" selected="selected">00</option>
          <option value="01">01</option>
          <option value="02">02</option>
          <option value="03">03</option>
          <option value="04">04</option>
          <option value="05">05</option>
          <option value="06">06</option>
          <option value="07">07</option>
          <option value="08">08</option>
          <option value="09">09</option>
          <option value="10">10</option>
          <option value="11">11</option>
          <option value="12">12</option>
          <option value="13">13</option>
          <option value="14">14</option>
          <option value="15">15</option>
          <option value="16">16</option>
          <option value="17">17</option>
          <option value="18">18</option>
          <option value="19">19</option>
          <option value="20">20</option>
          <option value="21">21</option>
          <option value="22">22</option>
          <option value="23">23</option>
        </select>
          <span class="texto_padrao_titulo">:</span>&nbsp;
          <select name="duracao_minuto" id="duracao_minuto" style="width:50px;">
            <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
          </select>
          <br />
          <?php echo $lang['lang_info_gerenciador_agendamentos_relay_duracao_info']; ?></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_dias_especificos']; ?></td>
        <td align="left" valign="middle" class="texto_padrao">
        <input name="dias[]" type="checkbox" value="1" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_segunda']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="2" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_terca']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="3" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_quarta']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="4" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_quinta']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="5" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_sexta']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="6" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_sabado']; ?>&nbsp;
        <input name="dias[]" type="checkbox" value="7" id="dias" disabled="disabled" style="vertical-align:middle" /><?php echo $lang['lang_info_gerenciador_agendamentos_relay_domingo']; ?></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
          <input name="cadastrar" type="hidden" id="cadastrar" value="sim" />
          </td>
      </tr>
    </table>
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque"><div id="quadro">
            <div id="quadro-topo"> <strong><?php echo $lang['lang_info_gerenciador_agendamentos_relay_tab_info_titulo']; ?></strong></div>
          <div class="texto_medio" id="quadro-conteudo">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="25" class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_agendamentos_relay_instrucoes']; ?></td>
                </tr>
              </table>
          </div>
        </div></td>
      </tr>
    </table>
      </div>
      </div>
</div>
    </div>
  </form>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
