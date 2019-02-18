<?php
require_once("inc/protecao-admin.php");
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
      <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura&ccedil;&otilde;es</a></li>
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
  <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
    <tr>
      <td width="30" height="28" align="center" class="texto_padrao_destaque" scope="col"><img src="/admin/img/icones/img-icone-cadastrar.png" alt="Cadastrar" width="16" height="16" /></td>
      <td width="300" align="left" scope="col"><a href="/admin/admin-cadastrar-tutorial" class="texto_padrao_destaque">Cadastrar Tutorial</a></td>
      <td width="520" align="right" class="texto_padrao_destaque" scope="col">&nbsp;</td>
    </tr>
  </table>
  <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="510" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Titulo</td>
      <td width="120" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Data</td>
      <td width="120" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Vizualizações</td>
      <td width="150" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;Ações</td>
    </tr>
<?php
$pagina_atual = query_string('2');

$sql = mysql_query("SELECT * FROM tutoriais");
$lpp = 100; // total de registros por p&aacute;gina
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("SELECT *, DATE_FORMAT(data,'%d/%m/%Y') AS data FROM tutoriais ORDER by codigo ASC LIMIT $inicio, $lpp");

while ($dados_tutorial = mysql_fetch_array($sql)) {

$tutorial_code = code_decode($dados_tutorial["codigo"],"E");

echo "<tr style='background-color:#FFFFFF;'>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_tutorial["titulo"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_tutorial["data"]."</td>
<td height='25' align='center' scope='col' class='texto_padrao'>&nbsp;".$dados_tutorial["vizualizacoes"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>
<select style='width:100%' id='".$tutorial_code."' onchange='executar_acao_diversa(this.id,this.value);'>
  <option value='' selected='selected'>Escolha uma ação</option>
  <option value='/admin/admin-tutorial-vizualizar/".$tutorial_code."'>Vizualizar</option>
  <option value='editar-tutorial'>Editar</option>
  <option value='remover-tutorial'>Remover</option>
  </optgroup>
</select>
</td>
</tr>";

}
?>
  </table>
  <table width="870" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
    <tr>
      <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("SELECT * FROM tutoriais"));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">Nenhum tutorial encontrado.</span>";
} else {
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/admin/".query_string('1')."/$i";
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
