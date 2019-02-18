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
<link href="inc/estilo.css" rel="stylesheet" type="text/css" />
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
<form style="padding:0; margin:0" onsubmit="buscar_revenda(document.getElementById('chave').value);return false;">
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
    <tr>
      <td width="30" height="30" align="center" class="texto_padrao_destaque" scope="col"><img src="/admin/img/icones/img-icone-cadastrar.png" alt="Cadastrar" width="16" height="16" /></td>
      <td width="350" align="left" scope="col"><a href="/admin/admin-cadastrar-revenda" class="texto_padrao_destaque">Cadastrar Revenda</a></td>
      <td width="300" align="left" class="texto_padrao_destaque" scope="col">
      <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-revendas';" <?php if(!query_string('2')) { echo 'checked="checked"'; } ?> />&nbsp;Todos
      <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-revendas/filtro/1';" <?php if(query_string('3') == '1') { echo 'checked="checked"'; } ?> />&nbsp;Ativos
      <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-revendas/filtro/2';" <?php if(query_string('3') == '2') { echo 'checked="checked"'; } ?> />&nbsp;Bloqueados      </td>
      <td width="350" align="right" class="texto_padrao_destaque" scope="col">
      Buscar por
        <input name="chave" type="text" id="chave" />
        <input type="button" class="botao_padrao" value="Buscar" onclick="buscar_revenda(document.getElementById('chave').value);" /></td>
    </tr>
  </table>
</form>
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="430" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Responsavel</td>
      <td width="300" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Plano</td>
      <td width="100" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Streamings</td>
      <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;Ações</td>
    </tr>
<?php

if(query_string('2') == 'resultado') {

if(preg_match("/@/i",query_string('3'))) {
$query = "SELECT * FROM revendas where email like '%".query_string('3')."%'";
$pagina_atual = query_string('4');
} else {
$query = "SELECT * FROM revendas where (nome like '%".query_string('3')."%' AND tipo = '1') OR id = '".query_string('3')."'";
$pagina_atual = query_string('4');
}

} elseif(query_string('2') == 'filtro') {

$query = "SELECT * FROM revendas where status = '".query_string('3')."'";
$pagina_atual = query_string('4');

} else {

$query = "SELECT * FROM revendas where tipo = '1'";
$pagina_atual = query_string('4');

}

$sql = mysql_query("".$query."");
$lpp = 500; // total de registros por p&aacute;gina
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("".$query." ORDER by nome ASC LIMIT $inicio, $lpp");

while ($dados_revenda = mysql_fetch_array($sql)) {


$total_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_revenda["codigo"]."'"));

$cor_status = ($dados_revenda["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$limite_subrevendas = ($dados_revenda["subrevendas"] == 999999) ? "Ilimitado" : $dados_revenda["subrevendas"];
$limite_streamings = ($dados_revenda["streamings"] == 999999) ? "Ilimitado" : $dados_revenda["streamings"];
$limite_ouvintes = ($dados_revenda["ouvintes"] == 999999) ? "Ilimitado" : $dados_revenda["ouvintes"];

$revenda_code = code_decode($dados_revenda["codigo"],"E");

$link_financeiro = "<a href='https://financeiro.advancehost.com.br/index.php?pagina=Busca&cat=dominio&chave=".$dados_revenda["id"]."' target='_blank'><img src='https://financeiro.advancehost.com.br/img/icones/busca.png' alt='Buscar no Financeiro' width='10' height='10' border='0' align='absmiddle' /></a>";

echo "<tr style='background-color:".$cor_status.";'>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$dados_revenda["nome"]." - ".$dados_revenda["id"]."&nbsp;".$link_financeiro."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>&nbsp;".$limite_subrevendas." / ".$limite_streamings." / ".$limite_ouvintes." / ".$dados_revenda["bitrate"]." Kbps / ".tamanho($dados_revenda["espaco"])."</td>
<td height='25' align='center' scope='col' class='texto_padrao'>&nbsp;".$total_stm."</td>
<td height='25' align='left' scope='col' class='texto_padrao'>
<select style='width:100%' id='".$revenda_code."' onchange='executar_acao_revenda(this.id,this.value);'>
  <option value='' selected='selected'>Escolha uma ação</option>
  <optgroup label='Administração'>
  <option value='bloquear'>Bloquear</option>
  <option value='desbloquear'>Desbloquear</option>
  <option value='alterar-senha'>Alterar Senha</option>
  <option value='configurar'>Alterar Configurações</option>
  <option value='remover'>Remover</option>
  </optgroup>
  <optgroup label='Streamings'>
  <option value='listar-streamings'>Listar Streamings</option>
  <option value='alterar-servidor'>Alterar Servidor</option>
  <option value='alterar-revenda'>Alterar Revenda</option>
  <option value='exportar-lista-streamings'>Exportar Lista de Streamings</option>
  </optgroup>
</select>
</td>
</tr>";

// Verifique se tem subrevenda e lista
$total_subrevendas = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_revenda["codigo"]."'"));

if($total_subrevendas > 0) {

$sql_subrevenda = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_revenda["codigo"]."' ORDER by id ASC");
while ($dados_subrevenda = mysql_fetch_array($sql_subrevenda)) {

$total_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_subrevenda["codigo"]."'"));

$cor_status = ($dados_subrevenda["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$limite_subrevendas = ($dados_subrevenda["subrevendas"] == 999999) ? "Ilimitado" : $dados_subrevenda["subrevendas"];
$limite_streamings = ($dados_subrevenda["streamings"] == 999999) ? "Ilimitado" : $dados_subrevenda["streamings"];
$limite_ouvintes = ($dados_subrevenda["ouvintes"] == 999999) ? "Ilimitado" : $dados_subrevenda["ouvintes"];

$revenda_code = code_decode($dados_subrevenda["codigo"],"E");

echo "<tr style='background-color:".$cor_status.";'>
<td height='20' align='left' scope='col' class='texto_padrao_pequeno'><img src='/admin/img/icones/img-icone-branchbottom.gif' align='absmiddle' />&nbsp;".$dados_subrevenda["nome"]." - ".$dados_subrevenda["id"]."</td>
<td height='20' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$limite_subrevendas." / ".$limite_streamings." / ".$limite_ouvintes." / ".$dados_subrevenda["bitrate"]." Kbps / ".tamanho($dados_subrevenda["espaco"])."</td>
<td height='20' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;".$total_stm."</td>
<td height='20' align='left' scope='col' class='texto_padrao_pequeno'>
<select style='width:100%' id='".$revenda_code."' onchange='executar_acao_revenda(this.id,this.value);'>
  <option value='' selected='selected'>Escolha uma ação</option>
  <optgroup label='Administração'>
  <option value='bloquear'>Bloquear</option>
  <option value='desbloquear'>Desbloquear</option>
  <option value='alterar-senha'>Alterar Senha</option>
  <option value='configurar'>Alterar Configurações</option>
  <option value='remover'>Remover</option>
  </optgroup>
  <optgroup label='Streamings'>
  <option value='listar-streamings'>Listar Streamings</option>
  <option value='alterar-servidor'>Alterar Servidor</option>
  <option value='alterar-revenda'>Alterar Revenda</option>
  <option value='exportar-lista-streamings'>Exportar Lista de Streamings</option>
  </optgroup>
</select>
</td>
</tr>";

// Verifique se tem subrevenda e lista
$total_subrevendas_sub = mysql_num_rows(mysql_query("SELECT * FROM revendas WHERE codigo_revenda = '".$dados_subrevenda["codigo"]."'"));

if($total_subrevendas_sub > 0) {

$sql_subrevenda_sub = mysql_query("SELECT * FROM revendas where codigo_revenda = '".$dados_subrevenda["codigo"]."' ORDER by id ASC");
while ($dados_subrevenda_sub = mysql_fetch_array($sql_subrevenda_sub)) {

$total_stm = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_cliente = '".$dados_subrevenda_sub["codigo"]."'"));

$cor_status = ($dados_subrevenda_sub["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$limite_subrevendas = ($dados_subrevenda_sub["subrevendas"] == 999999) ? "Ilimitado" : $dados_subrevenda_sub["subrevendas"];
$limite_streamings = ($dados_subrevenda_sub["streamings"] == 999999) ? "Ilimitado" : $dados_subrevenda_sub["streamings"];
$limite_ouvintes = ($dados_subrevenda_sub["ouvintes"] == 999999) ? "Ilimitado" : $dados_subrevenda_sub["ouvintes"];

$revenda_code = code_decode($dados_subrevenda_sub["codigo"],"E");

echo "<tr style='background-color:".$cor_status.";'>
<td height='20' align='left' scope='col' class='texto_padrao_pequeno'><img src='/admin/img/icones/img-icone-branchbottom.gif' align='absmiddle' style='padding-left:15px' />&nbsp;".$dados_subrevenda_sub["nome"]." - ".$dados_subrevenda_sub["id"]."</td>
<td height='20' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$limite_subrevendas." / ".$limite_streamings." / ".$limite_ouvintes." / ".$dados_subrevenda_sub["bitrate"]." Kbps / ".tamanho($dados_subrevenda_sub["espaco"])."</td>
<td height='20' align='center' scope='col' class='texto_padrao_pequeno'>&nbsp;".$total_stm."</td>
<td height='20' align='left' scope='col' class='texto_padrao_pequeno'>
<select style='width:100%' id='".$revenda_code."' onchange='executar_acao_revenda(this.id,this.value);'>
  <option value='' selected='selected'>Escolha uma ação</option>
  <optgroup label='Administração'>
  <option value='bloquear'>Bloquear</option>
  <option value='desbloquear'>Desbloquear</option>
  <option value='alterar-senha'>Alterar Senha</option>
  <option value='configurar'>Alterar Configurações</option>
  <option value='remover'>Remover</option>
  </optgroup>
  <optgroup label='Streamings'>
  <option value='listar-streamings'>Listar Streamings</option>
  <option value='alterar-servidor'>Alterar Servidor</option>
  <option value='alterar-revenda'>Alterar Revenda</option>
  <option value='exportar-lista-streamings'>Exportar Lista de Streamings</option>
  </optgroup>
</select>
</td>
</tr>";

}
}

}
}

}
?>
  </table>
  <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
    <tr>
      <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("".$query.""));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">Nenhuma revenda encontrada.</span>";
} else {

$pagina_atual = query_string('2');
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/admin/admin-revendas/".query_string('2')."/".query_string('3')."/$i";
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
