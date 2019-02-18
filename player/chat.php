<?php
$porta = query_string('1');

if(!is_numeric($porta)) {
die ("Error! Missing data.");
}

// Verifica se a conex�o com mysql foi estabelecida para definir se ir� usar os dados do banco de dados ou do cache no TXT
$dados_config = @mysql_fetch_array(@mysql_query("SELECT * FROM configuracoes"));

// Verifica a última modificação do cache para usa-lo ou atualiza-lo
$data_hora_cache = date ("Y-m-d H:i:s", @filemtime("cache/".$porta.".txt"));
$checagem_ultima_modificacao_cache = data_diff_horas( $data_hora_cache );

if(!empty($dados_config["dominio_padrao"]) && $checagem_ultima_modificacao_cache > 12) {

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$porta."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$dados_servidor_aacplus = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor_aacplus"]."'"));

$servidor = $dados_servidor["ip"];
$servidor_rtmp = $dados_servidor_aacplus["ip"];
$autoplay = $dados_stm["player_autoplay"];
$autodj_prog_aovivo = $dados_stm["autodj_prog_aovivo"];
$autodj_prog_aovivo_msg = $dados_stm["autodj_prog_aovivo_msg"];
$volume_inicial = $dados_stm["player_volume_inicial"];
$aacplus = $dados_stm["aacplus"];
$chat = $dados_stm["player_exibir_chat"];
$pedidos_musicais = $dados_stm["player_exibir_pedido_musical"];

// Grava/Atualiza cache para uso posterior
@file_put_contents("cache/".$porta.".txt","".$servidor."|".$servidor_rtmp."|".$autoplay."|".$autodj_prog_aovivo."|".$autodj_prog_aovivo_msg."|".$volume_inicial."|".$aacplus."|".$chat."|".$pedidos_musicais."");

} else { // Else -> Checagem conexão mysql -> Não conectado

list($servidor, $servidor_rtmp, $autoplay, $autodj_prog_aovivo, $autodj_prog_aovivo_msg, $volume_inicial, $aacplus, $chat, $pedidos_musicais) = explode("|",@file_get_contents("cache/".$porta.".txt"));

} // FIM -> Checagem conexão mysql

$dados_servidor_shoutcast = shoutcast_info($servidor,$porta,1);

$check_protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/')));
$host_url = ($_SERVER['HTTP_HOST'] == "player.srvstm.com" && $check_protocol == "https") ? "https://player.srvstm.com" : "http://".$_SERVER['HTTP_HOST']."";

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Chat <?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?></title>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $host_url; ?>/inc/estilo-chat.css">
	<script type="text/javascript" src="<?php echo $host_url; ?>/inc/chat.js"></script>
<style>
body:after {
  content: url(https://player.srvstm.com/img/icones/chat/img-icone-chat-feliz.gif) url(https://player.srvstm.com/img/icones/chat/img-icone-chat-triste.gif) url(https://player.srvstm.com/img/icones/chat/img-icone-chat-bravo.gif) url(https://player.srvstm.com/img/icones/chat/img-icone-chat-lingua.gif) url(https://player.srvstm.com/img/icones/chat/img-icone-chat-coracao.gif) url(https://player.srvstm.com/img/icones/chat/img-icone-chat-piscada.gif) url(https://player.srvstm.com/img/icones/chat/img-icone-chat-assustado.gif);
  display: none;
}
.preload-images {
  display: none;
  width: 0;
  height: 0;
  background: url(https://player.srvstm.com/img/icones/chat/img-icone-chat-feliz.gif), url(https://player.srvstm.com/img/icones/chat/img-icone-chat-triste.gif), url(https://player.srvstm.com/img/icones/chat/img-icone-chat-bravo.gif),
 url(https://player.srvstm.com/img/icones/chat/img-icone-chat-lingua.gif), url(https://player.srvstm.com/img/icones/chat/img-icone-chat-coracao.gif), url(https://player.srvstm.com/img/icones/chat/img-icone-chat-piscada.gif), url(https://player.srvstm.com/img/icones/chat/img-icone-chat-assustado.gif);
}
</style>
</head>
<body>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="23%" align="left" scope="col"><div id="ListaOnline"></div></td>
    <td width="5%" align="center" scope="col">&nbsp;</td>
    <td width="70%" align="left" scope="col"><div id="Elchat"></div></td>
  </tr>
</table>
<script type="text/javascript">
$('#Elchat').ChatSocket({
	   Room:"<?php echo $porta; ?>",
       lblTitulChat:"&nbsp;<?php echo ucwords(strtolower($dados_servidor_shoutcast["titulo"])); ?>",
       lblCampoEntrada:"",
       lblEnviar:"Enviar",
       urlImg:"http://icons.iconarchive.com/icons/custom-icon-design/mono-general-1/32/chat-icon.png", // Avatar chat
       btnEntrar:"btnEntrar",
       btnEnviar:"btnEnviar",
       lblBtnEnviar:"OK",
       lblTxtEntrar:"txtEntrar",
       lblTxtEnviar:"txtMensaje",
       lblBtnEntrar:"OK",
       idOnline:"ListaOnline",
       lblUsuariosOnline:"Online",
       lblEntradaNombre:"Nome/Name/Nombre",
       panelColor:"success"
});
</script>
</body>
</html>