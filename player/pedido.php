<?php
$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".query_string('1')."'"));

if(!empty($_POST["nome"]) && !empty($_POST["email"]) && !empty($_POST["artista"]) && !empty($_POST["musica"])) {

$nome = strip_tags($_POST["nome"]);
$email = strip_tags($_POST["email"]);
$artista = strip_tags($_POST["artista"]);
$musica = strip_tags($_POST["musica"]);

$musica_formatada = $artista." - ".$musica;

mysql_query("INSERT INTO streaming.pedidos_musicais (codigo_stm,nome,email,data,musica) VALUES ('".$dados_stm["codigo"]."','".$nome."','".$email."',NOW(),'".$musica_formatada."')") or die("Erro ao processar query.<br>Mensagem do servidor: ".mysql_error());

}

$array_lang = array("pt-br" => array("nome" => "Seu Nome", "email" => "Seu E-mail", "artista" => "Artista", "musica" => "Música", "resultado" => "Seu pedido foi enviado, em breve será executado."),
					"en" => array("nome" => "Your Name", "email" => "Your E-mail", "artista" => "Artist", "musica" => "Song Title", "resultado" => "Your request was sent, will soon be executed."), 
					"es" => array("nome" => "Nombre", "email" => "E-mail", "artista" => "Artista", "musica" => "Música", "resultado" => "Su solicitud fue enviada, pronto se va a ejecutar.")
					);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedir Música / Request Song</title>
<style>
body {
	margin: 0px auto;
	overflow: hidden;
}
input[type=text] {
	background: #FFFFFF;
	border:solid 1px #CCCCCC;
	height:20px;
	padding:2px;
}
.botao {
	background-color: #666666;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:12px;
	color:#FFFFFF;
	font-weight:bold;
	padding-left:10px;
	padding-right:10px;
	height:25px;
	border: #333333 1px solid;
	cursor:pointer;
}
.texto_padrao {
	color: #000000;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:bold;
}
.texto_sucesso {
	color: #009900;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size:15px;
	font-weight:bold;
}
</style>
</head>

<body>
<?php if(!empty($_POST["nome"]) && !empty($_POST["email"]) && !empty($_POST["artista"]) && !empty($_POST["musica"])) { ?>
<span class="texto_sucesso"><center><br /><br /><br /><?php echo $array_lang[$dados_stm["idioma_painel"]]["resultado"]; ?></center></span>
<?php if(query_string('2') == "site") { ?>
<br /><br /><center><input type="button" class="botao" onclick="self.close();" value="OK" /></center>
<?php } ?>
<?php } else { ?>
<form id="" name="" method="post" action="/pedido/<?php echo query_string('1'); ?>">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#F4F4F7; border:#CCCCCC 1px solid; margin-top:10px">
    <tr>
      <td width="28%" height="30" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $array_lang[$dados_stm["idioma_painel"]]["nome"]; ?></td>
      <td width="70%" align="left" class="texto_padrao"><input name="nome" type="text" class="input" id="nome" style="width:250px;" value="" /></td>
    </tr>
    <tr>
      <td height="30" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $array_lang[$dados_stm["idioma_painel"]]["email"]; ?></td>
      <td align="left" class="texto_padrao"><input name="email" type="text" class="input" id="email" style="width:250px;" value="" /></td>
    </tr>
    <tr>
      <td height="30" align="left" class="texto_padrao" style="padding-left:5px;"><?php echo $array_lang[$dados_stm["idioma_painel"]]["artista"]; ?></td>
      <td align="left" class="texto_padrao"><input name="artista" type="text" class="input" id="artista" style="width:250px;" value="" /></td>
    </tr>
    <tr>
      <td height="30" align="left" style="padding-left:5px;" class="texto_padrao"><?php echo $array_lang[$dados_stm["idioma_painel"]]["musica"]; ?></td>
      <td align="left" class="texto_padrao"><input name="musica" type="text" class="input" id="musica" style="width:250px;" value="" /></td>
    </tr>
    <tr>
      <td height="40" align="left" style="padding-left:5px;" class="texto_padrao_destaque"></td>
      <td align="left" class="texto_padrao"><input type="submit" class="botao" value="OK" style="width:100px" /></td>
    </tr>
  </table>
</form>
<?php } ?>
</body>
</html>
