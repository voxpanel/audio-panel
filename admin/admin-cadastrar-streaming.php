<?php
require_once("inc/protecao-admin.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

$porta_livre_stm = false;
$porta_livre_dj = false;

$nova_porta_stm = 6998;
$nova_porta_dj = 34998;

while(!$porta_livre_stm) {

$nova_porta_stm += 2;

if($nova_porta_stm != 6984 && $nova_porta_stm != 6985) {

$total_porta_livre_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta = '".$nova_porta_stm."' ORDER BY porta"));

if($total_porta_livre_stm == 0) {
$porta_livre_stm = true;
}

}

}

while(!$porta_livre_dj) {

$nova_porta_dj += 2;

$total_porta_livre_dj = mysql_num_rows(mysql_query("SELECT * FROM streamings WHERE porta_dj = '".$nova_porta_dj."' ORDER BY porta_dj"));

if($total_porta_livre_dj == 0) {
$porta_livre_dj = true;
}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<link href="/admin/inc/estilo-menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
</div>
<div id="menu">
<div id="menu-links">
  	<ul>
      <li style="width:150px">&nbsp;</li>
  		<li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
  		<li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
        <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
        <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
        <li><em></em><a href="/admin/admin-avisos" class="texto_menu">Avisos</a></li>
        <li><em></em><a href="/admin/admin-tutoriais" class="texto_menu">Tutoriais</a></li>
        <li><em></em><a href="/admin/admin-apps" class="texto_menu">Apps</a></li>
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configurações</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
  <form method="post" action="/admin/admin-cadastra-streaming" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120"  height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Plano</td>
        <td width="380" align="left">
        <select name="plano" class="input" id="plano" style="width:255px;" onchange="configuracao_plano(this.value,'streaming');">
        <option value="" selected="selected" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione um plano padrão</option>
        <option value="100|48|10000">Streaming Econômico</option>
        <option value="300|48|10000">Streaming Econômico2</option>
        <option value="99999|320|100000">Streaming Ilimitado</option>
        </select></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Servidor</td>
        <td align="left">
        <select name="servidor" class="input" id="servidor" style="width:255px;">
<?php
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

$query_servidor = mysql_query("SELECT * FROM servidores where tipo = 'streaming' ORDER by codigo ASC");
while ($dados_servidor = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

if($dados_config["codigo_servidor_atual"] == $dados_servidor["codigo"]) {
echo '<option value="'.$dados_servidor["codigo"].'" selected="selected">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
} else {
echo '<option value="'.$dados_servidor["codigo"].'">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
}

}
?>
        </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Servidor AAC+</td>
        <td align="left">
        <select name="servidor_aacplus" class="input" id="servidor_aacplus" style="width:255px;">
        <option value="0">Nenhum</option>
<?php
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

$query_servidor = mysql_query("SELECT * FROM servidores where tipo = 'aacplus' ORDER by codigo ASC");
while ($dados_servidor = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor_aacplus = '".$dados_servidor["codigo"]."'"));

if($dados_config["codigo_servidor_aacplus_atual"] == $dados_servidor["codigo"]) {
echo '<option value="'.$dados_servidor["codigo"].'" selected="selected">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
} else {
echo '<option value="'.$dados_servidor["codigo"].'">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
}

}
?>
        </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Porta / Porta DJ</td>
        <td align="left">
          <input name="porta" type="text" class="input" id="porta" style="width:120px;" value="<?php echo $nova_porta_stm; ?>" />
          <input name="porta_dj" type="text" class="input" id="porta_dj" style="width:120px;" value="<?php echo $nova_porta_dj; ?>" />          </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Ouvintes</td>
        <td align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" value="" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Bitrate</td>
        <td align="left">
        <select name="bitrate" class="input" id="bitrate" style="width:255px;">
          <option value="" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione uma op&ccedil;&atilde;o</option>
          <?php
		   foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
		    echo "<option value=\"".$bitrate."\">".$bitrate." Kbps</option>\n";
		   }
		  ?>
         </select>         </td>
      </tr>

<tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Programetes ?</td>
        <td align="left">
        <select name="programetes" class="input" id="programetes" style="width:255px;">
          <option value="" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione uma op&ccedil;&atilde;o</option>
   <option value="sim">Sim</option>
      <option value="nao">Não</option>
         </select>         </td>
      </tr>

      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Encoder</td>
        <td align="left">
        <select name="encoder" class="input" id="encoder" style="width:255px;">
          <option value="mp3"<?php if($dados_stm["encoder"] == "mp3") { echo ' selected="selected"'; } ?>>Formato de Transmissão MP3</option>
          <option value="aacp"<?php if($dados_stm["encoder"] == "aacp") { echo ' selected="selected"'; } ?>>Formato de Transmissão AACP(recomendado)</option>
         </select>
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('Formato de transmissão do streaming.\n\nEscolha entre MP3 e AACP.\n\nRecomendamos o uso do formato MP3 que é compátivel com qualquer player.');" style="cursor:pointer" />        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Ativar AutoDJ</td>
        <td align="left" class="texto_padrao">
          <input name="autodj" type="radio" id="autodj" onclick="document.getElementById('espaco').disabled=false;document.getElementById('espaco').style.cursor = 'auto';" value="sim" checked="checked" />
          &nbsp;Sim
          <input type="radio" name="autodj" id="autodj" value="nao" onclick="document.getElementById('espaco').disabled=true;document.getElementById('espaco').style.cursor = 'not-allowed';" />&nbsp;Não</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Espaço AutoDJ</td>
        <td align="left" class="texto_padrao_pequeno"><input name="espaco" type="text" class="input" id="espaco" style="width:250px;" />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('O valor deve ser em megabytes ex.: 1GB = 1000');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha</td>
        <td align="left"><input name="senha" type="text" class="input" id="senha" style="width:250px; vertical-align:middle" value="" />
          &nbsp;<img src="/admin/img/icones/img-icone-senha-24x24.png" alt="Gerar Senha" width="16" height="16" align="absmiddle" onclick="gerar_senha('senha');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Identifica&ccedil;&atilde;o</td>
        <td align="left"><input name="identificacao" type="text" class="input" id="identificacao" style="width:250px;" />
          &nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">E-mail</td>
        <td align="left"><input name="email" type="text" class="input" id="email" style="width:250px;" />
          &nbsp;</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Ativar AAC+</td>
        <td align="left" class="texto_padrao">
          <input type="radio" name="aacplus" id="aacplus" value="sim" onclick="configurar_aacplus_streaming(this.value,'<?php echo $dados_config["codigo_servidor_aacplus_atual"]; ?>');" />&nbsp;Sim
          <input type="radio" name="aacplus" id="aacplus" value="nao" checked="checked" onclick="configurar_aacplus_streaming(this.value,'<?php echo $dados_config["codigo_servidor_aacplus_atual"]; ?>');" />&nbsp;Não        </td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Cadastrar" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-streamings';" />        </td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
