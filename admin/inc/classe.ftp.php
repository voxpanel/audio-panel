<?php
class FTP {

  function conectar($ip) {

     $this->conexao = ftp_connect($ip);
     if(!$this->conexao) {
       return "Não foi possível se conectar-se ao FTP.";
     }

  }

  function autenticar($usuario,$senha) {

     if(!ftp_login($this->conexao,$usuario,$senha)) {
       return "Não foi possível autenticar-se ao FTP.";
     }
	 ftp_pasv($this->conexao, true);

  }
  
  function abrir_pasta($pasta) {

     if(!ftp_chdir($this->conexao,$pasta)) {
       return "Não foi possível abrir a pasta no FTP.";
     }

  }
  
  function verificar_pasta($pasta){
  
     if(ftp_size($this->conexao, $pasta) == '-1'){
       return true;
     } else {
       return false;
     }
	 
  }
  
  function listar_pastas($pasta) {

     $this->lista = ftp_nlist($this->conexao,$pasta);
	 sort($this->lista);
	 
       foreach ($this->lista as $objeto) {
	 
         if($this->verificar_pasta($objeto)) {
          $pastas[] = $objeto;
         }

       }
	   
	   return  $pastas;

  }

  function listar_arquivos($pasta,$tipo) {

     $this->lista = ftp_nlist($this->conexao,$pasta);
	 sort($this->lista);
	 
       foreach ($this->lista as $objeto) {
		 
          if(preg_match("/\.(".$tipo.")$/i",$objeto)) {
             $arquivos[] = $objeto;
		   }

       }
	   
	   return  $arquivos;

  }
  
  function total_arquivos($pasta,$tipo) {

     $this->lista = ftp_nlist($this->conexao,$pasta);
	 
       foreach ($this->lista as $objeto) {
	 
         if(preg_match("/\.(".$tipo.")$/i",$objeto)) {
             $arquivos[] = $objeto;
		 }

       }
	   
	   return  count($arquivos);

  }
  
  function criar_pasta($pasta) {

     if(ftp_mkdir($this->conexao,$pasta)) {
       return true;
     } else {
       return false;
     }

  }
  
  function remover_pasta($pasta) {

     $this->arquivos = @ftp_nlist($this->conexao,$pasta);
	   
     if (!empty($this->arquivos)) {
		 
       foreach($this->arquivos as $arquivo) {
	   
         @ftp_delete($this->conexao,$pasta."/".$arquivo);
		 
       }
		   
     }
	 
     if(ftp_rmdir($this->conexao,$pasta)) {
       return true;
     } else {
       return false;
     }

  }
    
  function renomear($antigo,$novo) {

     if(ftp_rename($this->conexao,$antigo,$novo)) {
       return true;
     } else {
       return false;
     }

  }
  
  function tamanho_arquivo($arquivo) {
  
  	 $tamanho = ftp_size($this->conexao,$arquivo);

     if($tamanho != -1) {
       return $tamanho;
     } else {
       return false;
     }

  }
  
  function remover_arquivo($arquivo) {

     if(ftp_delete($this->conexao,$arquivo)) {
       return true;
     } else {
       return false;
     }

  }
  
  function enviar_arquivo($arquivo_local,$arquivo_ftp) {

     if(ftp_put($this->conexao,$arquivo_ftp,$arquivo_local,FTP_BINARY)) {
       return true;
     } else {
       return false;
     }

  }
  
  function desconectar() {
  
     ftp_close($this->conexao);
	 
  }
}
?>