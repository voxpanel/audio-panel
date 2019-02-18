<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));

if($_POST["cadastrar_dj"]) {

if(isset($_POST["dj_login"]) && isset($_POST["dj_senha"])) {

// Verifica se o DJ ja existe
$verifica_dj = mysql_num_rows(mysql_query("SELECT * FROM djs where codigo_stm = '".$dados_stm["codigo"]."' AND login = '".$_POST["dj_login"]."'"));

if($verifica_dj > 0) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_acao_gerenciador_djs_resultado_alerta']."","alerta");

header("Location: /gerenciar-djs");
exit();
}

mysql_query("INSERT INTO djs (codigo_stm,login,senha) VALUES ('".$dados_stm["codigo"]."','".$_POST["dj_login"]."','".$_POST["dj_senha"]."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_acao_gerenciador_djs_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_acao_gerenciador_djs_resultado_erro']."","erro");

}

header("Location: /gerenciar-djs");
exit();

} // Fim cadastra DJ

if($_POST["cadastrar_restricao"]) {

if(!$_POST["dias_semana"]) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_acao_gerenciador_djs_resultado_alerta_restricao_dias_semana']."","alerta");

header("Location: /gerenciar-djs");
exit();
}

foreach($_POST["dias_semana"] as $dia) {
$dias_semana += $dia;
}

mysql_query("INSERT INTO djs_restricoes (codigo_stm,codigo_dj,hora_inicio,hora_fim,dias_semana) VALUES ('".$dados_stm["codigo"]."','".$_POST["codigo_dj"]."','".$_POST["hora_inicio"].":".$_POST["minuto_inicio"]."','".$_POST["hora_fim"].":".$_POST["minuto_fim"]."','".$dias_semana."')");

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_acao_gerenciador_djs_resultado_ok']."","ok");

header("Location: /gerenciar-djs");
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

function verificar_restricao_dias_semana() {

if (!document.querySelectorAll('input[type="checkbox"]:checked').length) {
alert("Ooops!\n\n<?php echo $lang['lang_acao_gerenciador_djs_resultado_alerta_restricao_dias_semana']; ?>");
return false;
}

}
</script>
</head>

<body>
<div id="sub-conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_djs_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
  <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
    <tr>
      <td width="30" height="25" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
      <td width="760" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_djs_info_reiniciar_autodj']; ?></td>
    </tr>
  </table>
  <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_djs_aba_djs']; ?></h2>
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_porta_dj']; ?></td>
      <td width="160" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_login']; ?></td>
      <td width="160" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_senha']; ?></td>
      <td width="300" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_senha_conexao']; ?></td>
      <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_executar_acao']; ?></td>
    </tr>
<?php
$total_djs = mysql_num_rows(mysql_query("SELECT * FROM djs where codigo_stm = '".$dados_stm["codigo"]."' ORDER by login"));

if($total_djs > 0) {

$sql = mysql_query("SELECT * FROM djs where codigo_stm = '".$dados_stm["codigo"]."' ORDER by login");
while ($dados_dj = mysql_fetch_array($sql)) {

$dj_code = code_decode($dados_dj["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_stm["porta_dj"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_dj["login"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_dj["senha"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_dj["login"].":".$dados_dj["senha"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

echo "<select style='width:100%' id='".$dj_code."' onchange='executar_acao_streaming_autodj(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_gerenciador_djs_acao']."</option>
  <option value='autodj-remover-dj'>".$lang['lang_info_gerenciador_djs_acao_remover']."</option>
</select>";

echo "</td>
</tr>";
}

} else {

echo "<tr>
    <td height='23' colspan='3' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
  </table>
  </div>
  <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_djs_aba_restricoes']; ?></h2>
<table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="160" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_login']; ?></td>
      <td width="560" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_restricoes_restricao']; ?></td>
      <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_djs_executar_acao']; ?></td>
    </tr>
<?php
$total_djs_restricoes = mysql_num_rows(mysql_query("SELECT * FROM djs_restricoes where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_djs_restricoes > 0) {

$sql_restricoes = mysql_query("SELECT * FROM djs_restricoes where codigo_stm = '".$dados_stm["codigo"]."' ORDER by codigo ASC");
while ($dados_djs_restricao = mysql_fetch_array($sql_restricoes)) {

$dados_dj = mysql_fetch_array(mysql_query("SELECT * FROM djs where codigo = '".$dados_djs_restricao["codigo_dj"]."'"));

// Restrição horario
if($dados_djs_restricao["hora_inicio"] && $dados_djs_restricao["hora_fim"]) {
$restricao_horario = $dados_djs_restricao["hora_inicio"]." ".$lang['lang_info_gerenciador_djs_restricoes_horario_ate']." ".$dados_djs_restricao["hora_fim"];
} else {
$restricao_horario = $lang['lang_info_gerenciador_djs_info_sem_restricao'];
}

// Restrição dias da semana
if($dados_djs_restricao["dias_semana"] == 1) {
$restricao_dia_semana  = $lang['lang_label_domingo'];
} else if($dados_djs_restricao["dias_semana"] == 2) {
$restricao_dia_semana  = $lang['lang_label_segunda'];
} else if($dados_djs_restricao["dias_semana"] == 4) {
$restricao_dia_semana  = $lang['lang_label_terca'];
} else if($dados_djs_restricao["dias_semana"] == 8) {
$restricao_dia_semana  = $lang['lang_label_quarta'];
} else if($dados_djs_restricao["dias_semana"] == 16) {
$restricao_dia_semana  = $lang['lang_label_quinta'];
} else if($dados_djs_restricao["dias_semana"] == 32) {
$restricao_dia_semana  = $lang['lang_label_sexta'];
} else if($dados_djs_restricao["dias_semana"] == 64) {
$restricao_dia_semana  = $lang['lang_label_sabado'];
} else if($dados_djs_restricao["dias_semana"] == 127) { // todos os dias
$restricao_dia_semana  = $lang['lang_label_domingo'].", ".$lang['lang_label_segunda'].", ".$lang['lang_label_terca'].", ".$lang['lang_label_quarta'].", ".$lang['lang_label_quinta'].", ".$lang['lang_label_sexta'].", ".$lang['lang_label_sabado'];
} else if($dados_djs_restricao["dias_semana"] == 62) { // Dias uteis
$restricao_dia_semana  = $lang['lang_label_segunda'].", ".$lang['lang_label_terca'].", ".$lang['lang_label_quarta'].", ".$lang['lang_label_quinta'].", ".$lang['lang_label_sexta'];
} else if($dados_djs_restricao["dias_semana"] == 43) {
$restricao_dia_semana  = $lang['lang_label_segunda'].", ".$lang['lang_label_quarta'].", ".$lang['lang_label_sexta'].", ".$lang['lang_label_domingo'];
} else if($dados_djs_restricao["dias_semana"] == 65) {
$restricao_dia_semana  = $lang['lang_label_sabado'].", ".$lang['lang_label_domingo'];
}

$dj_restricao_code = code_decode($dados_djs_restricao["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_dj["login"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$restricao_horario." ".$restricao_dia_semana."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

echo "<select style='width:100%' id='".$dj_restricao_code."' onchange='executar_acao_streaming_autodj(this.id,this.value);'>
  <option value='' selected='selected'>".$lang['lang_info_gerenciador_djs_acao']."</option>
  <option value='autodj-remover-dj-restricao'>".$lang['lang_info_gerenciador_djs_acao_remover']."</option>
</select>";

echo "</td>
</tr>";
}

} else {

echo "<tr>
    <td height='23' colspan='3' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
  </table>
  </div>
  <div class="tab-page" id="tabPage3">
  <h2 class="tab"><?php echo $lang['lang_info_gerenciador_djs_aba_cadastrar_dj']; ?></h2>
    <form name="djs" id="djs" method="post" action="/gerenciar-djs" style="padding:0px; margin:0px">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_djs_login']; ?></td>
        <td width="760" align="left"><input name="dj_login" type="text" class="input" id="dj_login" style="width:250px;" onkeyup="bloquear_acentos(this);" />
          <span class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_djs_info_caracteres_especiais']; ?></span></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_djs_senha']; ?></td>
        <td align="left"><input name="dj_senha" type="text" class="input" id="dj_senha" style="width:250px;" onkeyup="bloquear_acentos(this);" />
          <span class="texto_padrao_pequeno"><?php echo $lang['lang_info_gerenciador_djs_info_caracteres_especiais']; ?></span></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
          <input name="cadastrar_dj" type="hidden" id="cadastrar_dj" value="sim" />
        </td>
      </tr>
    </table>
    </form>
    <br />
    <center><img src="/admin/img/img-plugin-djport.jpg" alt="Dj Port" width="350" height="200" /></center>
    </div>
    <div class="tab-page" id="tabPage4">
  <h2 class="tab"><?php echo $lang['lang_info_gerenciador_djs_aba_cadastrar_restricao']; ?></h2>
    <form name="restricoes" id="restricoes" method="post" action="/gerenciar-djs" style="padding:0px; margin:0px" onsubmit="return verificar_restricao_dias_semana();">
    <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
      <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_djs_login']; ?></td>
            <td width="760" align="left">
            <select name="codigo_dj" class="input" id="codigo_dj" style="width:255px;">
            <?php
			$sql_djs = mysql_query("SELECT * FROM djs where codigo_stm = '".$dados_stm["codigo"]."' ORDER by login");
			while ($dados_dj = mysql_fetch_array($sql_djs)) {
				echo '<option value="' . $dados_dj["codigo"] . '">' . $dados_dj["login"] . '</option>';
			}
			?>
           </select>
            </td>
          </tr>
          <tr>
        <td width="130" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_djs_restricoes_horario']; ?></td>
            <td width="760" align="left" class="texto_padrao_titulo"><select name="hora_inicio" id="hora_inicio" style="width:50px;">
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
              :
              <select name="minuto_inicio" id="minuto_inicio" style="width:50px;">
                <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
              </select>
              &nbsp;<span class="texto_padrao"><?php echo $lang['lang_info_gerenciador_djs_restricoes_horario_ate']; ?></span>&nbsp;
              <select name="hora_fim" id="hora_fim" style="width:50px;">
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
              :
              <select name="minuto_fim" id="minuto_fim" style="width:50px;">
                <?php 
			for ($minuto=0;$minuto<=59;$minuto++){

			echo '<option value="'.sprintf("%02d",$minuto).'">'.sprintf("%02d",$minuto).'</option>';
			
			}
			?>
            </select></td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_djs_restricoes_horario_dias_semana']; ?></td>
            <td align="left" valign="middle" class="texto_padrao">
        <input name="dias_semana[]" type="checkbox" value="2" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_segunda']; ?>&nbsp;
        <input name="dias_semana[]" type="checkbox" value="4" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_terca']; ?>&nbsp;
        <input name="dias_semana[]" type="checkbox" value="8" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_quarta']; ?>&nbsp;
        <input name="dias_semana[]" type="checkbox" value="16" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_quinta']; ?>&nbsp;
        <input name="dias_semana[]" type="checkbox" value="32" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_sexta']; ?>&nbsp;
        <input name="dias_semana[]" type="checkbox" value="64" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_sabado']; ?>&nbsp;
        <input name="dias_semana[]" type="checkbox" value="1" id="dias_semana" checked="checked" style="vertical-align:middle" /><?php echo $lang['lang_label_domingo']; ?></td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
          <input name="cadastrar_restricao" type="hidden" id="cadastrar_restricao" value="sim" />
        </td>
      </tr>
    </table>
    </form>
    </div>
    </div>
    </div>
    </div>
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
