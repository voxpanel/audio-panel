<?php
require_once("inc/protecao-revenda.php");

$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas WHERE codigo = '".$_SESSION["code_user_logged"]."'"));

mysql_query("Update revendas set senha = PASSWORD('".$_POST["senha1"]."'), alterar_senha = '1' where codigo = '".$dados_revenda["codigo"]."'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastrar Streaming</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/admin/img/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-revenda.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
</head>

<body>
<div id="sub-conteudo">
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
      <tr>
        <td width="30" height="30" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro_pequeno" scope="col">Senha alterada com sucesso, a partir de agora use a nova senha para acesso ao painel.</td>
      </tr>
    </table>
    
<?php
if(!$dados_revenda["smtp_servidor"] || !$dados_revenda["smtp_porta"] || !$dados_revenda["smtp_email"] || !$dados_revenda["smtp_senha"]) {
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px; background-color:#FFFF66; border:#DFDF00 4px dashed">
      <tr>
        <td width="30" height="30" align="center" scope="col"><img src="/img/icones/atencao.png" width="16" height="16" /></td>
        <td width="849" align="left" class="texto_status_erro_pequeno" scope="col">Você pode configurar um SMTP para recuperar sua senha caso seja esquecida ou enviar mensagens aos streamings. Para isso clique no ícone de Configurações no topo do painel.</td>
      </tr>
</table>
<?php } ?>
</div>
</body>
</html>
