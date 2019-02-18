<?php
require_once("inc/protecao-admin.php");

if($_POST["alterar_dados"]) {

mysql_query("Update configuracoes set dominio_cdn = '".$_POST["dominio_cdn"]."', dominio_padrao = '".$_POST["dominio_padrao"]."', codigo_servidor_atual = '".$_POST["codigo_servidor_atual"]."', codigo_servidor_aacplus_atual = '".$_POST["codigo_servidor_aacplus_atual"]."', usar_cdn = '".$_POST["usar_cdn"]."', manutencao = '".$_POST["manutencao"]."'");

// Cria o sessão do status das ações executadas e redireciona.

if(!mysql_error()) {
$_SESSION["status_acao"] = status_acao("Configurações alteradas com sucesso.","ok");
} else {
$_SESSION["status_acao"] .= status_acao("Não foi possível alterar as configurações.","alerta");
$_SESSION["status_acao"] .= status_acao("Erro: ".mysql_error()."","erro");
}

header("Location: /admin/admin-configuracoes");
exit();
}

$dados_config = mysql_fetch_array(mysql_query("SELECT * FROM configuracoes"));
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
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
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
        <li><em></em><a href="/admin/admin-estatisticas" class="texto_menu">Estatísticas</a></li>
        <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configurações</a></li>
        <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
  	</ul>
</div>
</div>
<div id="conteudo">
<?php
if($_SESSION['status_acao']) {

$status_acao = stripslashes($_SESSION['status_acao']);

echo '<table width="770" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
  <form method="post" action="/admin/admin-configuracoes" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
      <tr>
        <td width="150" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Domínio Padrão</td>
        <td width="350" align="left"><input name="dominio_padrao" type="text" class="input" id="dominio_padrao" style="width:250px;" value="<?php echo $dados_config["dominio_padrao"]; ?>" /></td>
      </tr>
      <tr>
        <td width="150" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Dom&iacute;nio CDN</td>
        <td width="350" align="left"><input name="dominio_cdn" type="text" class="input" id="dominio_cdn" style="width:250px;" value="<?php echo $dados_config["dominio_cdn"]; ?>" /></td>
      </tr>
      <tr>
        <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor Atual</td>
        <td align="left">
          <select name="codigo_servidor_atual" class="input" id="codigo_servidor_atual" style="width:255px;">
            <?php

$query = mysql_query("SELECT * FROM servidores WHERE tipo = 'streaming' AND status = 'on' ORDER by ordem ASC");
while ($dados_servidor = mysql_fetch_array($query)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".$dados_servidor["codigo"]."'"));

$alerta_capacidade = ($total_streamings > $dados_servidor["limite_streamings"]) ? "#FFC1C1" : "#FFFFFF";
$alerta_capacidade = ($total_streamings > $dados_servidor["limite_streamings"]) ? "#FFC1C1" : "#FFFFFF";

if($total_streamings > $dados_servidor["limite_streamings"]) {
$alerta_capacidade = "#FFC1C1"; 
} elseif($total_streamings < ($dados_servidor["limite_streamings"]/2)) {
$alerta_capacidade = "#BFFFBF"; 
} else {
$alerta_capacidade = "#FFFFFF"; 
}

if($dados_servidor["codigo"] == $dados_config["codigo_servidor_atual"]) {
echo '<option value="' . $dados_servidor["codigo"] . '" selected="selected" style="background-color:'.$alerta_capacidade.'; font-weight:bold">' . $dados_servidor["nome"] . ' - ' . $dados_servidor["ip"] . '</option>';
echo '<option disabled="disabled" style="background-color:'.$alerta_capacidade.'">--> Streamings: ' . $total_streamings . ' Capacidade: '.$dados_servidor["limite_streamings"].' Load: '.$dados_servidor["load"].'</option>';
} else {
echo '<option value="' . $dados_servidor["codigo"] . '" style="background-color:'.$alerta_capacidade.'">' . $dados_servidor["nome"] . ' - ' . $dados_servidor["ip"] . '</option>';
echo '<option disabled="disabled" style="background-color:'.$alerta_capacidade.'">--> Streamings: ' . $total_streamings . ' Capacidade: '.$dados_servidor["limite_streamings"].' Load: '.$dados_servidor["load"].'</option>';
}

}
?>
          </select></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Servidor AACP Atual</td>
        <td align="left">
          <select name="codigo_servidor_aacplus_atual" class="input" id="codigo_servidor_aacplus_atual" style="width:255px;">
            <?php

$query = mysql_query("SELECT codigo,ip,nome FROM servidores WHERE tipo = 'aacplus' AND status = 'on' ORDER by ordem ASC");
while ($dados_servidor_aacplus = mysql_fetch_array($query)) {

$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor_aacplus = '".$dados_servidor_aacplus["codigo"]."'"));

if($dados_servidor_aacplus["codigo"] == $dados_config["codigo_servidor_aacplus_atual"]) {
echo '<option value="' . $dados_servidor_aacplus["codigo"] . '" selected="selected">' . $dados_servidor_aacplus["nome"] . ' - ' . $dados_servidor_aacplus["ip"] . '</option>';
echo '<option disabled="disabled">--> Streamings: ' . $total_streamings . ' Load: '.$dados_servidor_aacplus["load"].'</option>';
} else {
echo '<option value="' . $dados_servidor_aacplus["codigo"] . '">' . $dados_servidor_aacplus["nome"] . ' - ' . $dados_servidor_aacplus["ip"] . '</option>';
echo '<option disabled="disabled">--> Streamings: ' . $total_streamings . ' Load: '.$dados_servidor_aacplus["load"].'</option>';
}

}
?>
          </select>          </td>
      </tr>
      <tr>
        <td width="150" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Usar CDN</td>
        <td width="350" align="left" class="texto_padrao"><input name="usar_cdn" type="radio" value="sim" <?php if($dados_config["usar_cdn"] == "sim") { echo 'checked="checked"';} ?> />&nbsp;Sim&nbsp;
      <input name="usar_cdn" type="radio" value="nao" <?php if($dados_config["usar_cdn"] == "nao") { echo 'checked="checked"';} ?> />&nbsp;Não      </tr>
      <tr>
        <td width="150" height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Manutenção</td>
        <td width="350" align="left" class="texto_padrao"><input name="manutencao" type="radio" value="sim" <?php if($dados_config["manutencao"] == "sim") { echo 'checked="checked"';} ?> />&nbsp;Sim&nbsp;
      <input name="manutencao" type="radio" value="nao" <?php if($dados_config["manutencao"] == "nao") { echo 'checked="checked"';} ?> />&nbsp;Não      </tr>
      <tr>
        <td height="40"><input name="alterar_dados" type="hidden" id="alterar_dados" value="sim" /></td>
        <td align="left">
          <input type="submit" class="botao" value="Alterar Configurações" /></td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
