<?php
require_once("inc/protecao-admin.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".code_decode(query_string('2'),"D")."'"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
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
  <form method="post" action="/admin/admin-altera-servidor-streaming-revenda" style="padding:0px; margin:0px" name="frm">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Revenda
          <input type="hidden" name="codigo_cliente" id="codigo_cliente" value="<?php echo $dados_revenda["codigo"]; ?>" /></td>
        <td width="380" align="left" class="texto_padrao">&nbsp;<?php echo $dados_revenda["nome"]; ?></td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Novo Servidor</td>
        <td width="380" align="left">
        <select name="servidor_geral" class="input" id="servidor_geral" style="width:255px;" onchange="selecionar_servidor(this.value);">
        <option value="" selected="selected">Selecione o novo servidor para todos os streamings</option>
<?php
$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));

$query_servidor = mysql_query("SELECT * FROM servidores ORDER by codigo ASC");
while ($dados_servidor = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

echo '<option value="'.$dados_servidor["codigo"].'">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';

}
?>
        </select></td>
      </tr>
    </table>
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid; margin-top:5px;">
<?php
$sql_streamings = mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."' ORDER by codigo_servidor ASC");
while ($dados_stm = mysql_fetch_array($sql_streamings)) {
?>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Streaming <?php echo $dados_stm["porta"]; ?></td>
        <td width="380" align="left" class="texto_padrao"><select name="servidor_novo[<?php echo $dados_stm["porta"]; ?>]" class="input" id="servidor_novo" style="width:255px;">
<?php
$query_servidor = mysql_query("SELECT * FROM servidores ORDER by codigo ASC");
while ($dados_servidor = mysql_fetch_array($query_servidor)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

if($dados_servidor["codigo"] == $dados_stm["codigo_servidor"]) {
echo '<option value="'.$dados_servidor["codigo"].'" selected="selected" style="font-weight:bold">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
} else {
echo '<option value="'.$dados_servidor["codigo"].'">'.$dados_servidor["nome"].' - '.$dados_servidor["ip"].' ('.$total_streamings.')</option>';
}

}
?>
        </select></td>
      </tr>
<?php } ?>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left"><input type="submit" class="botao" value="Alterar" />
            <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-revendas';" /></td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
