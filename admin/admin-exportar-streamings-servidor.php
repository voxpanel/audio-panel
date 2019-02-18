<?php
require_once("inc/protecao-admin.php");

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores WHERE codigo = '".code_decode(query_string('2'),"D")."'"));
$total_streamings = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".code_decode(query_string('2'),"D")."'"));

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
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid;">
    <tr>
      <td width="120" height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Servidor</td>
      <td width="380" align="left" class="texto_padrao">&nbsp;<?php echo $dados_servidor["nome"]; ?></td>
    </tr>
    <tr>
      <td height="30" align="left" class="texto_padrao_destaque" style="padding-left:5px;">Streamings</td>
      <td align="left" class="texto_padrao">&nbsp;<?php echo $total_streamings; ?></td>
    </tr>
  </table>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid; margin-top:5px;">
      <tr>
        <td height="30" align="center" class="texto_padrao_destaque" style="padding:5px;">
          <textarea name="lista-streamings" id="lista-streamings" style="width:98%; height:400px;" onclick="this.select();">
<?php
$total = 0;

$total_registros = mysql_num_rows(mysql_query("SELECT * FROM streamings where codigo_servidor = '".code_decode(query_string('2'),"D")."' ORDER by porta ASC"));

$sql = mysql_query("SELECT * FROM streamings where codigo_servidor = '".code_decode(query_string('2'),"D")."' ORDER by porta ASC");
while ($dados_stm = mysql_fetch_array($sql)) {

$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));

echo "sshpass -p 'XXXXXXX' rsync --progress -rogpae 'ssh -p 6985 -o StrictHostKeyChecking=no' root@".$dados_servidor["ip"].":/home/streaming/".$dados_stm["porta"]."/ /home/streaming/".$dados_stm["porta"]."/\n";

}
echo "playlists";
?></textarea>
        </td>
      </tr>
    </table>
</body>
</html>
