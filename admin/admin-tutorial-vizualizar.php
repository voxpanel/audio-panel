<?php
require_once("inc/protecao-admin.php");

$tutorial_code = code_decode(query_string('2'),"D");

$dados_tutorial = mysql_fetch_array(mysql_query("SELECT * FROM tutoriais where codigo = '".$tutorial_code."'"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="/admin/inc/estilo.css" rel="stylesheet" type="text/css" />
<title><?php echo $dados_tutorial["titulo"]; ?></title>
</head>

<body>
<div class="texto_padrao" style="margin:10px">
<?php echo $dados_tutorial["tutorial"]; ?>
</div>
</body>
</html>
