<?php
require_once("inc/protecao-admin.php");

$total_apps = mysql_num_rows(mysql_query("SELECT * FROM apps"));
$total_apps_concluida = mysql_num_rows(mysql_query("SELECT * FROM apps WHERE status = '1'"));
$total_apps_aguardando = mysql_num_rows(mysql_query("SELECT * FROM apps WHERE status != '1'"));
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
<script type="text/javascript" src="/admin/inc/ajax.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
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
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
  <tr>
    <td width="30" height="28" align="center" class="texto_padrao_destaque" scope="col"><img src="/admin/img/icones/img-icone-cadastrar.png" alt="Cadastrar" width="16" height="16" /></td>
    <td width="300" align="left" scope="col"><a href="/admin/admin-app-compilar-todos" class="texto_padrao_destaque">Compilar Todos</a></td>
    <td width="320" align="left" class="texto_padrao" scope="col"><strong>Solicitações:</strong> <?php echo $total_apps; ?>&nbsp;&nbsp;<strong>Concluídas:</strong> <?php echo $total_apps_concluida; ?>&nbsp;&nbsp;<strong>Na Fila:</strong> <?php echo $total_apps_aguardando; ?></td>
    <td width="350" align="right" class="texto_padrao_destaque" scope="col">
    <form style="padding:0; margin:0" onsubmit="buscar_app(document.getElementById('porta').value);return false;">
    Porta/Package
        <input name="porta" type="text" id="porta" />
        <input type="button" class="botao_padrao" value="Buscar" onclick="buscar_app(document.getElementById('porta').value);" />
    </form>
    </td>
  </tr>
</table>
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="80" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Porta</td>
      <td width="250" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Revenda</td>
      <td width="140" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Data</td>
      <td width="320" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Package</td>
      <td width="50" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Play</td>
      <td width="140" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;Ações</td>
    </tr>
<?php
if(query_string('2') == 'resultado') {
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('3')."'"));
$query1 = "SELECT * FROM apps where codigo_stm = '".$dados_stm["codigo"]."'";
$query2 = "SELECT *, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data FROM apps where codigo_stm = '".$dados_stm["codigo"]."'";
} else {
$query1 = "SELECT * FROM apps";
$query2 = "SELECT *, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data FROM apps";
}

$pagina_atual = query_string('4');

$sql = mysql_query($query1);
$lpp = 100; // total de registros por p&aacute;gina
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("".$query2." ORDER by codigo DESC LIMIT $inicio, $lpp");

while ($dados_app = mysql_fetch_array($sql)) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where codigo = '".$dados_app["codigo_stm"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

if($dados_app["status"] == 1) {
$cor_status = '#C6FFC6';
} elseif($dados_app["status"] == 2) {
$cor_status = '#FFB9B9';
} elseif($dados_app["compilado"] == "sim") {
$cor_status = '#FFFFB7';
} else {
$cor_status = '#FFFFFF';
}

$app_code = code_decode($dados_app["codigo"],"E");

echo "<tr style='background-color:".$cor_status.";'>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_stm["porta"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_revenda["nome"]." - ".$dados_revenda["id"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_app["data"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_app["package"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".ucfirst($dados_app["play"])."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;<a href='/admin/admin-app-detalhes/".$app_code."' title='Detalhes da Solicitação'>[Detalhes]</a>&nbsp;<a href=\"javascript:executar_acao_diversa('".$app_code."','app-android-remover' );\" title='Remover Solicitação'>[Remover]</a></td>
</tr>";

}
?>
  </table>
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
    <tr>
      <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query($query1));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">Nenhuma requisição encontrada.</span>";
} else {
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/admin/admin-apps/".query_string('2')."/".query_string('3')."/$i";
              echo " <a href=\"$url\" class=\"texto_padrao\" title=\"Ir para p&aacute;gina $linksp\">$linksp</a></span>";
      }
	}

}
?>      </td>
    </tr>
  </table>
</div>

<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/admin/img/icones/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="Fechar" /></div>
<div id="log-sistema-conteudo"><img src="/admin/img/ajax-loader.gif" /></div>
</div>
<!-- Fim div log do sistema -->
</body>
</html>
