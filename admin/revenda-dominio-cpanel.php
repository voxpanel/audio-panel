<?php
require_once("inc/protecao-revenda.php");

if($_POST["configurar"]) {

// Função para conectar a uma URL
function criar_entrada_dns_cpanel($dominio, $usuario, $senha, $servidor) {

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://".$dominio.":2083/json-api/cpanel?cpanel_jsonapi_user=".$usuario."&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=ZoneEdit&cpanel_jsonapi_func=add_zone_record&domain=".$dominio."&name=".$servidor."&type=CNAME&cname=".$servidor.".srvstm.com&ttl=600&class=IN");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)');
$header[0] = "Authorization: Basic " . base64_encode($usuario.":".$senha) . "\n\r";
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$resultado = curl_exec($ch);
curl_close($ch);

if($resultado === false) {
return "Error connection, try again.";
} else {

$xml = json_decode($resultado,true);
$status = $xml["cpanelresult"]["data"][0]["result"]["status"];

if($status == 1) {
return "OK";
} else {
return "Error: ".$xml["cpanelresult"]["data"][0]["result"]["statusmsg"];
}

}

}

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

// Colocar contador de 1 a 100 pra adicionar as zonas e resultado individual
$resultado = "";

for ($i = 1; $i <= 100; $i++) {
$resultado_requisicao = criar_entrada_dns_cpanel($dados_revenda["dominio_padrao"], $_POST["usuario_cpanel"], $_POST["senha_cpanel"], "stm".$i);
$resultado_final .= "[".$resultado_requisicao."] stm".$i.".".$dados_revenda["dominio_padrao"]." -> stm".$i.".srvstm.com<br>";
}

$resultado_requisicao_api = criar_entrada_dns_cpanel($dados_revenda["dominio_padrao"], $_POST["usuario_cpanel"], $_POST["senha_cpanel"], "api");
$resultado_final .= "[".$resultado_requisicao_api."] api.".$dados_revenda["dominio_padrao"]." -> api.srvstm.com<br>";

$resultado_requisicao_player = criar_entrada_dns_cpanel($dados_revenda["dominio_padrao"], $_POST["usuario_cpanel"], $_POST["senha_cpanel"], "player");
$resultado_final .= "[".$resultado_requisicao_player."] player.".$dados_revenda["dominio_padrao"]." -> player.srvstm.com<br>";


// Cria o sessão do status das ações executadas e redireciona.
$_SESSION["resultado"] = $resultado_final;
header("Location: /admin/revenda-dominio-cpanel");
exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<link href="inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/javascript-abas.js"></script>
<script type="text/javascript">
   window.onload = function() {
	fechar_log_sistema();
   };
</script>
</head>

<body>
<div id="sub-conteudo">
<?php if($dados_revenda["status"] == '1') { ?>
<?php if(empty($_SESSION['resultado'])) { ?>
<form method="post" action="/admin/revenda-dominio-cpanel" style="padding:0px; margin:0px">
<div id="quadro">
<div id="quadro-topo"><strong><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_tab_titulo; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25">
    <div class="tab-pane" id="tabPane1">
   	  <div class="tab-page" id="tabPage1">
       	<h2 class="tab"><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_tab_dados_cpanel; ?></h2>
        <table width="890" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border-left:#CCCCCC 1px solid; border-right:#CCCCCC 1px solid; border-bottom:#CCCCCC 1px solid;">
                <tr>
                  <td width="150" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_dominio; ?></td>
                  <td width="740" align="left"><?php echo ($dados_revenda["dominio_padrao"]) ? '<span class="texto_padrao_pequeno">'.$dados_revenda["dominio_padrao"].'</span>' : '<span class="texto_padrao_vermelho_destaque">'.lang_info_pagina_dominio_proprio_ferramenta_cpanel_status.'</span> <a href="#" onclick="abrir_log_sistema();window.open(\'/admin/revenda-configuracoes\',\'conteudo\');">'.lang_info_pagina_dominio_proprio_ferramenta_cpanel_status_botao.'</a>'; ?></td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_usuario; ?></td>
                  <td align="left"><input name="usuario_cpanel" type="text" class="input" id="usuario_cpanel" style="width:250px;" /></td>
                </tr>
                <tr>
                  <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;"><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_senha; ?></td>
                  <td align="left"><input name="senha_cpanel" type="password" class="input" id="senha_cpanel" style="width:250px;" /></td>
                </tr>
              </table>
   	  </div>
      </div></td>
  </tr>
  <tr>
    <td height="40" align="center"><?php echo ($dados_revenda["dominio_padrao"]) ? '<input type="submit" class="botao" value="'.lang_botao_titulo_configurar.'" />' : '<input type="submit" class="botao" value="'.lang_botao_titulo_configurar.'" disabled="disabled" />'; ?>
      <input name="configurar" type="hidden" id="configurar" value="<?php echo time(); ?>" /></td>
  </tr>
</table>
    </div>
      </div>
</form>
<?php } else { ?>
<div id="quadro">
<div id="quadro-topo"><strong><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_tab_titulo_resultado; ?></strong></div>
<div class="texto_medio" id="quadro-conteudo">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color: #C1E0FF; border: #006699 1px solid; margin-bottom:5px">
                <tr>
                  <td width="30" height="25" align="center" scope="col"><img src="img/icones/ajuda.gif" width="16" height="16" /></td>
                  <td align="left" class="texto_padrao_pequeno" scope="col"><?php echo lang_info_pagina_dominio_proprio_ferramenta_cpanel_info_erro; ?></td>
        </tr>
              </table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="texto_padrao_pequeno"><div id="resultado_requisicao" style="width:98%; height:200px; border:#999999 1px solid; text-align:left; overflow-y:scroll; padding:5px; background-color:#F4F4F7"><?php echo $_SESSION['resultado'];unset($_SESSION["resultado"]); ?></div></td>
  </tr>
</table>
    </div>
      </div>
<?php } ?>
<?php } else { ?>
<table width="879" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
	<tr>
        <td width="30" height="50" align="center" scope="col"><img src="/admin/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro" scope="col"><?php echo lang_alerta_bloqueio; ?></td>
    </tr>
</table>
<?php } ?>
</div>
</body>
</html>
