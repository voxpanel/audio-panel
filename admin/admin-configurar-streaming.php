<?php
require_once("inc/protecao-admin.php");

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".code_decode(query_string('2'),"D")."'"));
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
  <form method="post" action="/admin/admin-configura-streaming" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="140" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Porta</td>
        <td width="360" align="left" class="texto_padrao_destaque">
        <input type="text" class="input" style="width:250px;" value="<?php echo $dados_stm["porta"]; ?>" disabled="disabled" />        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Cliente</td>
        <td align="left">
        <select name="codigo_cliente" class="input" id="codigo_cliente" style="width:255px;">
        <option value="0">Nenhum</option>
        
<?php

$query = mysql_query("SELECT * FROM revendas ORDER by nome ASC");
while ($dados_revenda = mysql_fetch_array($query)) {
/*
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."'"));

if($dados_revenda["codigo"] == $dados_stm["codigo_cliente"]) {
echo '<option value="' . $dados_revenda["codigo"] . '" selected="selected">' . $dados_revenda["nome"] . ' - ' . $dados_revenda["id"] . ' - ' . $dados_revenda["email"] . ' (' . $total_streamings . ')</option>';
} else {
echo '<option value="' . $dados_revenda["codigo"] . '">' . $dados_revenda["nome"] . ' - ' . $dados_revenda["id"] . ' - ' . $dados_revenda["email"] . ' (' . $total_streamings . ')</option>';
}
*/
if($dados_revenda["codigo"] == $dados_stm["codigo_cliente"]) {
echo '<option value="' . $dados_revenda["codigo"] . '" selected="selected">' . $dados_revenda["nome"] . ' - ' . $dados_revenda["id"] . ' - ' . $dados_revenda["email"] . '</option>';
} else {
echo '<option value="' . $dados_revenda["codigo"] . '">' . $dados_revenda["nome"] . ' - ' . $dados_revenda["id"] . ' - ' . $dados_revenda["email"] . '</option>';
}
}
?>
          </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Plano</td>
        <td align="left">
        <select name="plano" class="input" id="plano" style="width:255px;" onchange="configuracao_plano(this.value,'streaming');">
        <option value="" selected="selected" style="font-size:13px; font-weight:bold; background-color:#CCCCCC;">Selecione um plano padrão</option>
        <option value="100|48|10000">Streaming Econômico</option>
        <option value="99999|320|100000">Streaming Ilimitado</option>
        </select>          </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Servidor</td>
        <td align="left">
        <select name="servidor" class="input" id="servidor" style="width:255px;">
<?php
$query_servidor = mysql_query("SELECT * FROM servidores WHERE tipo = 'streaming' ORDER by ordem ASC");
while ($dados_servidor = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

if($dados_stm["codigo_servidor"] == $dados_servidor["codigo"]) {
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
$query_servidor = mysql_query("SELECT * FROM servidores WHERE tipo = 'aacplus' ORDER by ordem ASC");
while ($dados_servidor_aacplus = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor_aacplus = '".$dados_servidor_aacplus["codigo"]."'"));

if($dados_stm["codigo_servidor_aacplus"] == $dados_servidor_aacplus["codigo"]) {
echo '<option value="'.$dados_servidor_aacplus["codigo"].'" selected="selected">'.$dados_servidor_aacplus["nome"].' - '.$dados_servidor_aacplus["ip"].' ('.$total_streamings.')</option>';
} else {
echo '<option value="'.$dados_servidor_aacplus["codigo"].'">'.$dados_servidor_aacplus["nome"].' - '.$dados_servidor_aacplus["ip"].' ('.$total_streamings.')</option>';
}

}
?>
        </select>        </td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">M&aacute;ximo Ouvintes</td>
        <td align="left"><input name="ouvintes" type="text" class="input" id="ouvintes" style="width:250px;" value="<?php echo $dados_stm["ouvintes"]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">M&aacute;ximo Bitrate</td>
        <td align="left">
        <select name="bitrate" class="input" id="bitrate" style="width:255px;">
          <?php
		   foreach(array("24","32","48","64","96","128","256","320") as $bitrate){
		   
		   if($dados_stm["bitrate"] == $bitrate) {
		    echo "<option value=\"".$bitrate."\" selected=\"selected\">".$bitrate." Kbps</option>";
		   } else {
		    echo "<option value=\"".$bitrate."\">".$bitrate." Kbps</option>";
		   }
		   
		   }
		  ?>
         </select>         </td>
      </tr>

      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Programetes ?</td>
        <td align="left">
        <select name="programetes" class="input" id="programetes" style="width:255px;">
     <option value="<?php if($dados_stm["programetes"] == "sim"){ echo "sim";}else{echo "nao";}?>">Selecione uma opção.</option>
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
          <input name="autodj" type="radio" id="autodj" onclick="document.getElementById('espaco').disabled=false;document.getElementById('espaco').style.cursor = 'auto';" value="sim" <?php if($dados_stm["autodj"] == "sim") {echo 'checked="checked"';} ?> />
          &nbsp;Sim
          <input type="radio" name="autodj" id="autodj" value="nao" onclick="document.getElementById('espaco').disabled=true;document.getElementById('espaco').style.cursor = 'not-allowed';document.getElementById('espaco').value='0';" <?php if($dados_stm["autodj"] == "nao") {echo 'checked="checked"';} ?> />&nbsp;Não</td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Espaço AutoDJ</td>
        <td align="left" class="texto_padrao_pequeno">
        <input name="espaco" type="text" class="input" id="espaco" style="width:250px;" value="<?php echo $dados_stm["espaco"]; ?>" <?php if($dados_stm["autodj"] == "nao") {echo 'disabled="disabled"';} ?> />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('O valor deve ser em megabytes ex.: 1GB = 1000');" style="cursor:pointer" />        </td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha</td>
        <td align="left"><input name="senha" type="text" class="input" id="senha" style="width:250px;" value="<?php echo $dados_stm["senha"]; ?>" />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('Use apenas lestras e/ou números.\n\nCaracteres como !@#$%¨& não irão funcionar corretamente.');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Senha Admin</td>
        <td align="left"><input name="senha_admin" type="text" class="input" id="senha_admin" style="width:250px;" value="<?php echo $dados_stm["senha_admin"]; ?>" />
        <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('Use apenas lestras e/ou números.\n\nCaracteres como !@#$%¨& não irão funcionar corretamente.');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">E-mail</td>
        <td align="left"><input name="email" type="text" class="input" id="email" style="width:250px;" value="<?php echo $dados_stm["email"]; ?>" />
          <img src="/admin/img/icones/ajuda.gif" title="Ajuda sobre este item." width="16" height="16" onclick="alert('Informe um e-mail para receber avisos do painel, como espaço em disco excedido, migrações etc...\n\nO envio será feito caso a revenda tenha configurado um SMTP.');" style="cursor:pointer" /></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Ativar AAC+</td>
        <td align="left" class="texto_padrao">
          <input type="radio" name="aacplus" id="aacplus" value="sim"<?php if($dados_stm["aacplus"] == "sim") { echo ' checked="checked"'; } ?> onclick="configurar_aacplus_streaming(this.value,'<?php echo $dados_config["codigo_servidor_aacplus_atual"]; ?>');" />&nbsp;Sim
          <input type="radio" name="aacplus" id="aacplus" value="nao"<?php if($dados_stm["aacplus"] == "nao") { echo ' checked="checked"'; } ?> onclick="configurar_aacplus_streaming(this.value,'<?php echo $dados_config["codigo_servidor_aacplus_atual"]; ?>');" />&nbsp;Não        </td>
      </tr>
      <tr>
        <td height="40">
          <input name="porta" type="hidden" id="porta" value="<?php echo $dados_stm["porta"]; ?>" />        </td>
        <td align="left">
          <input type="submit" class="botao" value="Alterar Dados" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-streamings';" />          </td>
      </tr>
    </table>
  </form>
</div>
</body>
</html>