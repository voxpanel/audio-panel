<?php
require_once("admin/inc/protecao-final.php");
require_once("admin/inc/classe.ftp.php");
require_once("admin/inc/classe.ssh.php");

$porta_code = code_decode($_SESSION["porta_logada"],"E");

$dados_stm = mysql_fetch_array(mysql_query("SELECT * FROM streamings where porta = '".$_SESSION["porta_logada"]."'"));
$dados_revenda = mysql_fetch_array(mysql_query("SELECT * FROM revendas where codigo = '".$dados_stm["codigo_cliente"]."'"));
$dados_servidor = mysql_fetch_array(mysql_query("SELECT * FROM servidores where codigo = '".$dados_stm["codigo_servidor"]."'"));
$porta_code = code_decode($dados_stm["porta"],"E");
$pasta = "200200";
$diretorio = code_decode($pasta,"E");



// Salva a Playlist
if($_POST["playlist"]) {

$dados_playlist = mysql_fetch_array(mysql_query("SELECT * FROM playlists where codigo = '".$_POST["playlist"]."'"));

// Remove as músicas atuais da playlist para gravar as novas música
mysql_query("DELETE FROM playlists_musicas where codigo_playlist = '".$dados_playlist["codigo"]."'");

// Adiciona as musicas da playlist ao banco de dados
foreach($_POST["musicas_adicionadas"] as $ordem => $musica) {

$musica_path = $musica;
$musica_nome = str_replace("/","",strstr($musica,"/"));
$playlist_nome = "".addslashes($musica_nome)."";
$playlist_nome = preg_replace( '/Hora-certahora-feminina/', 'hora-feminina', $playlist_nome, 1 );
$playlist_nome = preg_replace( '/Hora-certahora-masculina/', 'hora-masculina', $playlist_nome, 1 );


      
// Adiciona música na playlist
mysql_query("INSERT INTO playlists_musicas (codigo_playlist,path_musica,musica,tipo,ordem) VALUES ('".$dados_playlist["codigo"]."','".addslashes($musica_path)."','".$playlist_nome."','".$tipo[0]."','".$ordem."')") or die(mysql_error());

// Adiciona a música na lista para adicionar ao arquivo da playlist
$lista_musicas .= "/home/streaming/".$dados_stm["porta"]."/".$musica_path."\n";

}

// Cria o arquivo da playlist para enviar ao servidor do streaming
$handle_playlist = fopen("/home/painel/public_html/temp/".$dados_playlist["arquivo"]."" ,"a");
fwrite($handle_playlist, $lista_musicas);
fclose($handle_playlist);

// Envia o arquivo da playlist para o servidor do streaming
// Conexão SSH
$ssh = new SSH();
$ssh->conectar($dados_servidor["ip"],$dados_servidor["porta_ssh"]);
$ssh->autenticar("root",code_decode($dados_servidor["senha"],"D"));

$resultado_envio = $ssh->enviar_arquivo("/home/painel/public_html/temp/".$dados_playlist["arquivo"]."","/home/streaming/playlists/".$dados_playlist["arquivo"]."",0777);

// Remove o arquivo temporário usado para criar a playlist
unlink("temp/".$dados_playlist["arquivo"]."");

$resuldado_final = "<span class='texto_status_sucesso'>Playlist <strong>".$dados_playlist["nome"]."</strong> salva com sucesso.</span>";

}

$url_logo = ($dados_revenda["url_logo"] == "") ? "http://".$_SERVER['HTTP_HOST']."/admin/img/img-logo-painel.gif" : $dados_revenda["url_logo"];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerenciar Programets</title>
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link href="/admin/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />
<link href="admin/inc/estilo-streaming.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/admin/inc/ajax-streaming-programets.js"></script>
<script type="text/javascript" src="/admin/inc/javascript.js"></script>
<script type="text/javascript" src="/admin/inc/sorttable.js"></script>
</script>
<link rel="stylesheet" href="../cupertino/jquery.ui.all.css" type="text/css">
<script type="text/javascript">
   window.onload = function() {
    carregar_pastas('<?php echo $diretorio; ?>');
	  carregar_playlists('<?php echo $porta_code; ?>');
         
	<?php if($_SESSION["resuldado"]) { ?>
    document.getElementById('log-sistema-conteudo').innerHTML = "<?php echo $_SESSION["resuldado"]; ?>";
    document.getElementById('log-sistema-fundo').style.display = "block";
    document.getElementById('log-sistema').style.display = "block";
  <?php unset($_SESSION["resuldado"]); ?>
  <?php } else { ?>
  fechar_log_sistema();
  <?php } ?>
   };
</script>
</head>



</head>
<div id="conteudo">
    <form method="post" action="/gerenciar-programets/<?php echo query_string('1'); ?>" style="padding:0px; margin:0px" name="gerenciador" enctype="multipart/form-data">
   <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
 <tbody><tr>
        <td height="30" align="left" class="texto_padrao_destaque">
        <div id="quadro">
            	<div id="quadro-topo"> <strong>ADICIONAR PROGRAMETS : <?php echo $diretorio; ?>/ <?php echo $porta_code; ?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tbody>
     <tr>
      <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;">ESCOLHA UMA PLAYLISTS</td> 
      </tr>
      <tr>
       <td align="left" valign="top" style="padding-left:5px;">
        <div style="background-color:#E9FFE9; border: #CCCCCC 1px solid; width:800px; height:100px; text-align:left; float:left; padding:5px; overflow: auto;">
        <span id="status_lista_playlists" class="texto_padrao_pequeno"></span>
		<ul id="lista-playlists">
        </ul>
            </td>
      </tr>
       <tr>
        <td height="30" align="right" style="padding-left:5px;padding-right:7px;">
        
        <input type="button" class="botao" onclick="carregar_playlists('<?php echo $porta_code; ?>','comerciais');" value="Carregar playlist" />
     </td>

      </tr>


      <tr>
      <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;">CATEGORIA DE PROGRAMETS <span style="color:#FF0000;font-family:Arial;font-size:13px;"> </span></td> 
      </tr>
      
      <tr>
        <td align="left" valign="top" style="padding-left:5px;">
        <div style="background-color:#E9FFE9; border: #CCCCCC 1px solid; width:800px; height:200px; text-align:left; float:left; padding:5px; overflow: auto;">
        <span id="status_lista_pastas" class="texto_padrao_pequeno"></span>
         
		<ul id="lista-pastas">
                <ul id="lista-horas">
		</ul>
		</div>
        </td>
        
      </tr>
      
      <tr>
        <td height="30" align="right" style="padding-left:5px;padding-right:7px;">
        <input type="button" class="botao" onclick="carregar_pastas('<?php echo $diretorio; ?>');" value="Recarregar Programets" />
        
        </td>
        </tr>



 
      <tr>
      <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;">LISTA DE BLOCOS</td><br>
      </tr>
     
      <tr>
        <td align="left" valign="top" style="padding-left:5px;">
        <div style="background-color:#FFFFA0; border: #CCCCCC 1px solid; width:800px; height:150px; text-align:left; float:left; padding:5px; overflow: auto;">
        
        <span id="msg_pasta" class="texto_padrao_pequeno"><?php echo "$AutoDJ_gerenciar_playlists03";?></span>
        <ul id="lista-musicas-pasta">
        </ul>
        
        </div> 
         
        </td>
      </tr>
       
       <tr>
        <td height="30" align="right" style="padding-left:5px;padding-right:7px;">
        
     </td>

      </tr>

      <tr>
      <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;">Músicas da Playlist<span id="quantidade_musicas_playlist"></span></td> 
      </tr>
      <tr>
         <td align="left" valign="top">
        <div id="musicas_playlist" style="background-color:#E1E1E1; border: #CCCCCC 1px solid; width:800px; height:200px; text-align:left; float:right; padding:5px; overflow: auto;">
        <span id="msg_playlist" class="texto_padrao_pequeno"><?php echo "$AutoDJ_gerenciar_playlists09";?></span> 
        
        <ul id="lista-musicas-playlist"></ul>
            </td>
      </tr>
      
      
        
    
      <tr>
        <td width="310" height="25" align="left" class="texto_padrao_destaque" style="padding-left:5px;"> </td>
        <td width="580" height="25" align="left" class="texto_padrao_destaque" style="padding-left:9px;">
     </td>
      </tr>
      
     
      <tr>
        <td height="30" align="right" style="padding-left:5px;padding-right:7px;">
        <input type="button" class="botao" onclick="salvar_playlist('<?php echo $porta_code; ?>');" value="Salvar playlist" />
        <input type="button" class="botao" onclick="limpar_lista_musicas('<?php echo $porta_code; ?>');" value="Limpar a lista" />
     </td>
        <td height="30" align="right" style="padding-left:9px;">
      
        <input name="playlist" type="hidden" id="playlist" value="" />
        </form>

    </table>

</div>
<!-- Início div log do sistema -->
<div id="log-sistema-fundo"></div>
<div id="log-sistema">
<div id="log-sistema-botao"><img src="/Streaming_files/img-icone-fechar.png" onclick="document.getElementById('log-sistema-fundo').style.display = 'none';document.getElementById('log-sistema').style.display = 'none';" style="cursor:pointer" title="Fechar" /></div>
<div id="log-sistema-conteudo"></div>
</div>
</table>
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
      <tbody><tr>
        <td height="30" align="left" class="texto_padrao_destaque">
        <div id="quadro">
            	<div id="quadro-topo"> <strong><?php echo "$AutoDJ_agendament31";?></strong></div>
            		<div class="texto_medio" id="quadro-conteudo">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tbody><tr>
    <td height="25" class="texto_padrao_pequeno"><br>
    
    </span></td>
    </tr>
</tbody></table>
    </div>
      </div>
        </td>
      </tr>
    </tbody></table>
    <br>
  </form>
</div>
</html>