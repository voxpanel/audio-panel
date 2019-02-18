<?php
require_once("admin/inc/protecao-final.php");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));

if(isset($_POST["cadastrar"])) {

$dados_ultimo_ponto = mysql_fetch_array(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by ID DESC LIMIT 1"));

$id = $dados_ultimo_ponto["id"]+1;
$ponto = str_replace("/","",$_POST["ponto"]);
$ponto = str_replace(" ","",$ponto);
$ponto = "/".$ponto;

mysql_query("INSERT INTO multipoint (codigo_stm,id,ponto,ouvintes,bitrate,encoder) VALUES ('".$dados_stm["codigo"]."','".$id."','".$ponto."','".$_POST["ouvintes"]."','".$_POST["bitrate"]."','".$_POST["encoder"]."')");


if(!mysql_error()) {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_gerenciador_multipoint_resultado_ok']."","ok");

} else {

// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["status_acao"] = status_acao("".$lang['lang_info_gerenciador_multipoint_resultado_erro']." ".mysql_error()."","erro");

}

header("Location: /gerenciar-multipoint");
exit();

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<link href="/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="inc/ajax-streaming.js"></script>
<script type="text/javascript" src="inc/javascript.js"></script>
<script type="text/javascript" src="inc/javascript-abas.js"></script>
<script type="text/javascript" src="/inc/sorttable.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo-pequeno">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="900" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form method="post" action="/gerenciar-multipoint" style="padding:0px; margin:0px">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo $lang['lang_info_gerenciador_multipoint_tab_titulo']; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25">
    <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px; background-color:#FFFF66; border:#DFDF00 1px solid">
  	  <tr>
        <td width="30" height="25" align="center" scope="col"><img src="/admin/img/icones/dica.png" width="16" height="16" /></td>
        <td width="860" align="left" class="texto_pequeno_erro" scope="col"><?php echo $lang['lang_info_gerenciador_multipoint_info']; ?></td>
     </tr>
    </table>
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_multipoint_aba_pontos']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;; border-bottom:#D5D5D5 1px solid;" id="tab" class="sortable">
          <tr style="background:url(/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
            <td width="80" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_tabela_id']; ?></td>
            <td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_tabela_ponto']; ?></td>
            <td width="110" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_tabela_ouvintes']; ?></td>
            <td width="110" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_tabela_bitrate']; ?></td>
            <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_tabela_encoder']; ?></td>
            <td width="140" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_tabela_acao']; ?></td>
          </tr>
<?php
$total_pontos = mysql_num_rows(mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."'"));

if($total_pontos > 0) {

$contador_pontos = 1;

$sql = mysql_query("SELECT * FROM multipoint where codigo_stm = '".$dados_stm["codigo"]."' ORDER by id ASC");
while ($dados_ponto = mysql_fetch_array($sql)) {

if($dados_ponto["encoder"] == "mp3") {
$encoder = "MP3";
} else {
$encoder = "AAC+";
}

$ponto_code = code_decode($dados_ponto["codigo"],"E");

echo "<tr>
<td height='25' align='left' scope='col' class='texto_padrao' style='padding-left:5px'>".$dados_ponto["id"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_ponto["ponto"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_ponto["ouvintes"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_ponto["bitrate"]." Kbps</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$encoder."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>";

if($contador_pontos == $total_pontos) {
echo "<select style='width:100%' id='".$ponto_code."' onchange='executar_acao_multipoint(this.id,this.value);'>";
} else {
echo "<select style='width:100%' disabled='disabled'>";
}

echo "<option value='' selected='selected'>".$lang['lang_info_gerenciador_multipoint_acao']."</option>
  <option value='remover-ponto'>".$lang['lang_info_gerenciador_multipoint_acao_remover']."</option>
</select>
</td>
</tr>";

$contador_pontos++;
}

} else {

echo "<tr>
    <td height='23' colspan='6' align='center' class='texto_padrao'>".$lang['lang_info_sem_registros']."</td>
  </tr>";

}
?>
        </table>
   	  </div>
      <div class="tab-page" id="tabPage2">
       	<h2 class="tab"><?php echo $lang['lang_info_gerenciador_multipoint_aba_cadastrar_ponto']; ?></h2>
        <table width="690" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-bottom:#CCCCCC 1px solid; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid;">
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_multipoint_ponto']; ?></td>
            <td align="left"><input name="ponto" type="text" class="input" id="ponto" style="width:250px;" onkeyup="bloquear_acentos(this);" /></td>
          </tr>
          <tr>
            <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_multipoint_ouvintes']; ?></td>
            <td align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" /></td>
          </tr>
          <tr>
            <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo $lang['lang_info_gerenciador_multipoint_bitrate']; ?></td>
            <td width="740" align="left" class="texto_padrao">
            <select name="bitrate" id="bitrate" style="width:255px;">
            <?php
			foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
	
				if($bitrate <= $dados_stm["bitrate"]) {
			
					echo '<option value="'.$bitrate.'">'.$bitrate.' Kbps</option>';
		
				}
		
			}			
			?>
            </select>
            </td>
          </tr>
          <tr>
            <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque"><?php echo $lang['lang_info_gerenciador_multipoint_encoder']; ?></td>
            <td align="left" class="texto_padrao_pequeno">
            <select name="encoder" class="input" id="encoder" style="width:255px;">
            <?php if($dados_stm["encoder_mp3"] == "sim") { ?>
             <option value="mp3">MP3</option>
            <?php } ?>
            <?php if($dados_stm["encoder_aacplus"] == "sim") { ?>
             <option value="aacp">AAC+</option>
            <?php } ?>
            </select>
            <?php if($dados_stm["encoder_mp3"] != "sim" && $dados_stm["encoder_aacplus"] != "sim") { ?>
            &nbsp;<?php echo $lang['lang_info_gerenciador_multipoint_encoder_inativo_info']; ?>
            <?php } ?>            </td>
          </tr>
          <tr>
            <td height="45" align="left" style="padding-left:5px;" class="texto_padrao_destaque">&nbsp;</td>
            <td align="left"><input type="submit" class="botao" value="<?php echo $lang['lang_botao_titulo_cadastrar']; ?>" />
              <input name="cadastrar" type="hidden" id="cadastrar" value="<?php echo time(); ?>" /></td>
          </tr>
        </table>
      </div>
      </div>
    </td>
  </tr>
</table>
    </div>
      </div>
</form>
</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="<?php echo $lang['lang_titulo_fechar']; ?>" /></div>
<div id="log-sistema-conteudo"></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>