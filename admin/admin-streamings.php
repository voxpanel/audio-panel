<?php
require_once("inc/protecao-admin.php");

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
<script type="text/javascript" src="/admin/inc/ajax.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo.gif) no-repeat left;"></div>
</div>
<div id="menu">
<div id="menu-links">
  	<ul>
  		<li style="width:210px">&nbsp;</li>
	  
	  <li><a href="/admin/admin-streamings" class="texto_menu">Streamings</a></li>
	  
	  <li><em></em><a href="/admin/admin-revendas" class="texto_menu">Revendas</a></li>
      
	  <li><em></em><a href="/admin/admin-servidores" class="texto_menu">Servidores</a></li>
      
	  <li><em></em><a href="/admin/admin-dicas" class="texto_menu">Dicas</a></li>
      
	  <li><em></em><a href="/admin/admin-avisos" class="texto_menu">Avisos</a></li>
      
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

echo '<table width="790" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px">'.$status_acao.'</table>';

unset($_SESSION['status_acao']);
}
?>
<form style="padding:0; margin:0" onsubmit="buscar_streaming(document.getElementById('porta').value,'admin');return false;">
  <table width="1110" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
    <tr>
      <td width="27" align="center" class="texto_padrao_destaque" scope="col"><img src="/admin/img/icones/img-icone-cadastrar.png" alt="Cadastrar" width="16" height="16" /></td>
      <td width="332" align="left" scope="col"><a href="/admin/admin-cadastrar-streaming" class="texto_padrao_destaque">Cadastrar Streaming</a></td>
      <td width="438" align="left" scope="col" class="texto_padrao_destaque">
      <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-streamings<?php if(query_string('2')) { echo '/'.query_string('2'); } ?><?php if(query_string('3')) { echo '/'.query_string('3'); } ?>';" <?php if(!query_string('2')) { echo 'checked="checked"'; } ?> />&nbsp;Todos
      <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-streamings<?php if(query_string('2')) { echo '/'.query_string('2'); } ?><?php if(query_string('3')) { echo '/'.query_string('3'); } ?>/filtro/1';" <?php if(query_string('5') == '1') { echo 'checked="checked"'; } ?> />&nbsp;Ativos
      <input style="vertical-align:middle;" type="radio" name="filtro" onclick="window.location = '/admin/admin-streamings<?php if(query_string('2')) { echo '/'.query_string('2'); } ?><?php if(query_string('3')) { echo '/'.query_string('3'); } ?>/filtro/2';" <?php if(query_string('5') == '2') { echo 'checked="checked"'; } ?> />&nbsp;Bloqueados      </td>
      <td width="313" align="right" class="texto_padrao_destaque" scope="col">
      Porta
        <input name="porta" type="text" id="porta" />
        <input type="button" class="botao_padrao" value="Buscar" onclick="buscar_streaming(document.getElementById('porta').value,'admin');" />      </td>
    </tr>
  </table>
</form>
  <table width="1110" border="0" align="center" cellpadding="0" cellspacing="0" style=" border-top:#D5D5D5 1px solid; border-left:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;" id="tab" class="sortable">
    <tr style="background:url(/admin/img/img-fundo-titulo-tabela.png) repeat-x; cursor:pointer">
      <td width="70" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Porta</td>
      <td width="70" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Porta DJ</td>
      <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;IP Conexão</td>
      <td width="200" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Configuração</td>
      <td width="80" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;FTP</td>
      <td width="70" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Status</td>
      <td width="250" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid; border-right:#D5D5D5 1px solid;">&nbsp;Responsavel</td>
      <td width="170" height="23" align="left" class="texto_padrao_destaque" style="border-bottom:#D5D5D5 1px solid;">&nbsp;Ações</td>
    </tr>
<?php
if(query_string('2') == 'resultado') {
$query = "SELECT * FROM streamings where porta = '".query_string('3')."'";
$pagina_atual = query_string('4');
} elseif(query_string('2') == 'resultado-revenda') {

if(query_string('4') == 'filtro') {

if(query_string('5') == '1') {
$query = "SELECT * FROM streamings where codigo_cliente = '".code_decode(query_string('3'),"D")."' AND status = '1'";
} else {
$query = "SELECT * FROM streamings where codigo_cliente = '".code_decode(query_string('3'),"D")."' AND status != '1'";
}

} else {
$query = "SELECT * FROM streamings where codigo_cliente = '".code_decode(query_string('3'),"D")."'";
}

$pagina_atual = query_string('4');
} elseif(query_string('2') == 'resultado-servidor') {
$query = "SELECT * FROM streamings where codigo_servidor = '".code_decode(query_string('3'),"D")."'";
$pagina_atual = query_string('4');
} else {
$query = "SELECT * FROM streamings";
$pagina_atual = query_string('4');
}

$zebra_nr= 0;

$sql = mysql_query("".$query."");
$lpp = 500; // total de registros por p&aacute;gina
$total = mysql_num_rows($sql);
$paginas = ceil($total / $lpp); 
if(!isset($pagina_atual)) { $pagina_atual = 0; }
$inicio = $pagina_atual * $lpp;
$sql = mysql_query("".$query." ORDER by porta ASC LIMIT $inicio, $lpp");

while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$dados_stm["codigo_cliente"]."'"));

if($dados_stm["codigo_cliente"] == 0) {
$responsavel = (strlen($dados_stm["identificacao"]) > 45) ? substr($dados_stm["identificacao"], 0, 43)."..." : $dados_stm["identificacao"];
} else {
$responsavel = (strlen($dados_revenda["nome"]) > 45) ? substr($dados_revenda["nome"], 0, 43)."..." : $dados_revenda["nome"];
}

$porcentagem_uso_espaco = ($dados_stm["espaco_usado"] == 0 || $dados_stm["espaco"] == 0) ? "0" : $dados_stm["espaco_usado"]*100/$dados_stm["espaco"];
$porcentagem_uso_espaco_barra = ($porcentagem_uso_espaco > 100) ? "100" : $porcentagem_uso_espaco;

$cor_status = ($dados_stm["status"] == 1) ? "#FFFFFF" : "#FFB3B3";

$status_inicial = ($dados_stm["status"] != 1) ? "Bloqueado" : "<img src='/admin/img/spinner.gif' />";

list($ano,$mes,$dia) = explode("-",$dados_stm["data_cadastro"]);
$data_cadastro = $dia."/".$mes."/".$ano;

$porta_code = code_decode($dados_stm["porta"],"E");

if($dados_stm["codigo_cliente"] == 1) {
$link_financeiro = "<a href='https://site.com.br/index.php?pagina=Busca&cat=dominio&chave=".$dados_stm["porta"]."' target='_blank'><img src='https://site.com.br/img/icones/busca.png' alt='Buscar no Financeiro' width='10' height='10' border='0' align='absmiddle' /></a>";
}

echo "<tr style='background-color:".$cor_status.";' onmouseover='this.style.backgroundColor=\"#F3F3F3\"' onmouseout='this.style.backgroundColor=\"".$cor_status."\"'>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno' title='Identificação: ".$dados_stm["identificacao"]." | Data Cadastro: ".$data_cadastro."'>&nbsp;".$dados_stm["porta"]."&nbsp;".$link_financeiro."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["porta_dj"]."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno' title='Servidor ".$dados_servidor["nome"]." - ".$dados_servidor["ip"]."'>&nbsp;".$dados_servidor["ip"]." (".$dados_servidor["nome"]." | ".$dados_servidor_aacplus["nome"].")</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$dados_stm["ouvintes"]." ouvin. | ".$dados_stm["bitrate"]." Kbps | ".tamanho($dados_stm["espaco"])."</td>
<td height='25' align='center' scope='col'><div class='meter-wrap'><div class='meter-value' style='background-color: red; width: ".round($porcentagem_uso_espaco_barra)."%;'><div class='meter-text'>".round($porcentagem_uso_espaco)."%</div></div></div></td>
<td height='25' align='center' scope='col' class='texto_padrao_pequeno' style='cursor:pointer' onClick='status_streaming(\"".$porta_code."\")' id='".$porta_code."'>".$status_inicial."</td>
<td height='25' align='left' scope='col' class='texto_padrao_pequeno'>&nbsp;".$responsavel."</td>
<td height='25' align='left' scope='col'>
<select style='width:100%' id='".$porta_code."' onchange='executar_acao_streaming_admin(this.id,this.value);'>
  <option value='' selected='selected'>Escolha uma ação</option>
  <optgroup label='Streaming'>
  <option value='ligar'>Ligar</option>
  <option value='desligar'>Desligar</option>
  <option value='configurar'>Alterar Configurações</option>
  <option value='http://".$dados_servidor["ip"].":".$dados_stm["porta"]."'>Abrir Painel ShoutCast</option>
  </optgroup>
  <optgroup label='AutoDJ'>
  <option value='ligar-autodj'>Ligar</option>
  <option value='desligar-autodj'>Desligar</option>
  </optgroup>
  <optgroup label='Ferramentas'>
  <option value='ativar-protecao'>Ativar Proteção contra Ataques</option>";
  if($dados_stm["aacplus"] == 'sim') {
  echo "<option value='sincronizar-aacplus'>Sincronizar AAC+</option>";
  }
  echo "<option value='sincronizar-playlists'>Sincronizar Playlists</option>
  <option value='http://".$dados_servidor["ip"].":".$dados_stm["porta"]."/admin.cgi?pass=".$dados_stm["senha_admin"]."'>Abrir Painel Admin ShoutCast</option>
  <option value='acessar-painel-streaming'>Acessar Painel de Streaming</option>
  </optgroup>
  <optgroup label='Administração'>
  <option value='bloquear'>Bloquear</option>
  <option value='desbloquear'>Desbloquear</option>
  <option value='logs'>Logs de Ações</option>
  <option value='remover'>Remover</option>
  </optgroup>
</select>
</td>
</tr>";

// Adiciona na lista de checagem do status apenas se estiver ativo
if($dados_stm["status"] == 1) {
$array_streamings .= "".$porta_code."|";
}

$zebra_nr++;

}

?>
  </table>
  <table width="1110" border="0" align="center" cellpadding="0" cellspacing="0" style=" border:#D5D5D5 1px solid;">
    <tr>
      <td height="20" align="center"><?php
$total_registros = mysql_num_rows(mysql_query("".$query.""));

if($total_registros == 0) {
echo "<span class=\"texto_padrao_destaque\">Nenhum streaming encontrado.</span>";
} else {
	
	for($i = 0; $i < $paginas; $i++) {
      $linksp = $i + 1;
      if ($pagina_atual == $i) {
              echo " <span class=\"texto_padrao_destaque\" title=\"P&aacute;gina $linksp\">$linksp</span>";
      } else {
              $url = "/admin/admin-streamings/".query_string('2')."/".query_string('3')."/$i";
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
<script type="text/javascript">
// Checar o status dos streamings
checar_status_streamings('<?php echo $array_streamings; ?>');
</script>
</body>
</html>
