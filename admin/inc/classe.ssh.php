<?php
class SSH {

  function conectar($ip,$porta) {

     $this->conexao = ssh2_connect($ip,$porta);
     if(!$this->conexao) {
       echo "<span style='color: #FFCC00;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;'>Não foi possível se conectar-se ao servidor.</span><br>";
     }

  }

  function autenticar($usuario,$senha) {

     if(!ssh2_auth_password($this->conexao,$usuario,$senha)) {
       echo "<span style='color: #FFCC00;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;'>Não foi possível autenticar-se ao servidor.</span><br>";
     }

  }

  function executar($comando, $length = 4096) {

     $this->stream = ssh2_exec($this->conexao,$comando.';sleep 1');
	 stream_set_blocking( $this->stream, true );
	 $this->resultado = fread( $this->stream, $length );
	 fclose($this->stream); 
	 
	 if($this->resultado) {
	 return $this->resultado;
	 } else {
	 echo "<span style='color: #FFCC00;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;'>Não foi possível executar o comando no servidor.<br>Comando: ".$comando."</span><br>";
	 }

  }
  
  function enviar_arquivo($origem,$destino,$permissao) {

	 $this->resultado = ssh2_scp_send($this->conexao,$origem,$destino,$permissao);
	 
	 if($this->resultado) {
	 return "ok";
	 } else {
	 echo "<span style='color: #FFCC00;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;'>Não foi possível enviar o arquivo para o servidor.</span><br>";
	 }

  }
  
  function baixar_arquivo($origem,$destino) {

	 $this->resultado = ssh2_scp_recv($this->conexao,$origem,$destino);
	 
	 if($this->resultado) {
	 return "ok";
	 } else {
	 echo "<span style='color: #FFCC00;font-family: Geneva, Arial, Helvetica, sans-serif;font-size:11px;font-weight:bold;'>Não foi possível enviar o arquivo para o servidor.</span><br>";
	 }

  }

}
?>