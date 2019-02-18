<?php
require_once("inc/protecao-admin.php");
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
<script type="text/javascript">
function template_aviso( area ) {

if(area == "streaming") {

document.getElementById("descricao").value = "Seu streaming foi migrado para um novo servidor.";

document.getElementById("mensagem").value = "Seu streaming foi migrado para um novo servidor.\n\nSeu você ainda usa IP para conexão, você precisa troca-lo pelo novo endereço(domínio) que é exibido na tela inicial do seu painel de controle. Após esta primeira troca você não precisará efetuar mais nenhuma alteração.\n\nEsta migração tem a finalidade de resolver problemas que vem ocorrendo nos últimos dias para que possamos manter a qualidade do serviços.";

} else if(area == "agendamento") {

document.getElementById("descricao").value = "Agendamento de migração do servidor NOME.srvstm.com";

document.getElementById("mensagem").value = "A migração tem previsão de conclusão em xx/xx às xx\n\nO IP será atualizado automaticamente no domínio padrão NOME.srvstm.com e consequentemente refletido nos domínios próprios das revendas.\n\nNão será preciso fazer alterações nos streamings que já usam o domínio no lugar do IP. Streamings que ainda usam IP deverão alterar para o domínio citado ou para o domínio padrão da revenda caso esteja configurado.";

} else {

document.getElementById("descricao").value = "Servidor NOME.srvstm.com migrado com sucesso.";

document.getElementById("mensagem").value = "O IP foi atualizado automaticamente no domínio padrão NOME.srvstm.com e consequentemente refletido nos domínios próprios das revendas que o usam.\n\nNão será preciso alterar nada nos streamings que já usam o domínio no lugar no IP do servidor. Streamings que ainda usam IP deverão alterar para o domínio citado ou para o domínio padrão da revenda caso esteja configurado.";
}

}
</script>
</head>

<body>
<div id="topo">
<div id="topo-conteudo" style="background:url(/admin/img/logo-advance-host.gif) no-repeat left;"></div>
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
      <li><em></em><a href="/admin/admin-configuracoes" class="texto_menu">Configura&ccedil;&otilde;es</a></li>
      <li><em></em><a href="/admin/sair" class="texto_menu">Sair</a></li>
    </ul>
  </div>
</div>
<div id="conteudo">
  <form method="post" action="/admin/admin-cadastra-aviso" style="padding:0px; margin:0px">
    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
    <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor</td>
        <td width="380" align="left" class="texto_padrao">
        <select name="codigo_servidor" class="input" id="codigo_servidor" style="width:255px;">
        <option value="0" selected="selected">Todos</option>
<?php

$query = mysql_query("SELECT * FROM servidores WHERE tipo = 'streaming' ORDER by ordem ASC");
while ($dados_servidor = mysql_fetch_array($query)) {

echo '<option value="' . $dados_servidor["codigo"] . '">' . $dados_servidor["nome"] . ' - ' . $dados_servidor["ip"] . '</option>';

}
?>
          </select>
          </td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Área de Exibi&ccedil;&atilde;o</td>
        <td width="380" align="left" class="texto_padrao">
        <select name="area" class="input" id="area" style="width:255px;">
        <option value="streaming" selected="selected">Streamings</option>
        <option value="revenda">Revendas</option>
        </select>
          </td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Template</td>
        <td width="380" align="left" class="texto_padrao">
        <select style="width:255px;" onchange="template_aviso(this.value);">
        <option selected="selected">Escolha um template</option>
        <option value="streaming">Aviso de migração para streaming</option>
        <option value="revenda">Aviso de migração para revenda</option>
        <option value="agendamento">Agendamento de migração para revenda</option>
        </select>
          </td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Titulo</td>
        <td width="380" align="left"><input name="titulo" type="text" class="input" id="titulo" style="width:250px;" value="Migra&ccedil;&atilde;o " /></td>
      </tr>
      <tr>
        <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Descrição</td>
        <td width="380" align="left"><input name="descricao" type="text" class="input" id="descricao" style="width:250px;" onkeyup="contar_caracteres(this.id,'120');" />
        &nbsp;<span id="total_caracteres" class="texto_padrao_pequeno">120</span></td>
      </tr>
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Mensagem</td>
        <td align="left"><textarea name="mensagem" id="mensagem" style="width:250px;" rows="10" onkeyup="contar_caracteres(this.id,'430');"></textarea>
        <span id="total_caracteres" class="texto_padrao_pequeno">430</span></td>
      </tr>
      
      <tr>
        <td height="30" align="left" style="padding-left:5px;" class="texto_padrao_destaque">Status</td>
        <td align="left" class="texto_padrao"><input name="status" type="radio" value="sim" checked />&nbsp;Sim&nbsp;<input name="status" type="radio" value="nao" />&nbsp;Não</td>
      </tr>
      <tr>
        <td height="40">&nbsp;</td>
        <td align="left">
          <input type="submit" class="botao" value="Cadastrar" />
          <input type="button" class="botao" value="Cancelar" onclick="window.location = '/admin/admin-dicas';" /></td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
