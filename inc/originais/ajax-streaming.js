////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar o streaming/autodj
function ligar_streaming_autodj( porta ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/status_streaming_interno/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado == "ligado") {
		
	// Streaming ligado -> ligar autodj
	carregar_playlists(porta);
	
	} else if(resultado == "ligado-relay") {
	
	// Streaming com relay ligado -> exibir alerta
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming já esta ligado com relay ativado. <br />Para ligar AutoDJ você deve desativar o relay.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "22px";
	
	} else if(resultado == "desligado") {
	
	// Streaming desligado -> ligar streaming
	ligar_streaming(porta);	
	
	} else if(resultado == "ligado-autodj") {
		
	// Streaming e autodj ligados -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming e AutoDJ já estão em ligados.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
	} else if(resultado == "ligado-autodj-desativado") {
		
	// Streaming ligado e autodj desativado -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming já esta em ligado.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
	} else if(resultado == "manutencao") {
	
	// Streaming em manutenção -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming esta em manutenção, por favor tente novamente mais tarde.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
	} else {
	
	// Erro desconhecido -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>Erro desconhecido ao executar esta ação, favor entrar em contato com o suporte.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "22px";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}  
  
}

// Função para ligar o streaming
function ligar_streaming( porta ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/ligar_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para desligar o streaming/autodj
function desligar_streaming_autodj( porta ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/status_streaming_interno/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado == "ligado") {
		
	// Streaming ligado -> desligar streaming
	desligar_streaming( porta );
	
	} else if(resultado == "ligado-autodj-desativado") {
		
	// Streaming ligado autodj desativado -> desligar streaming
	desligar_streaming( porta );	
	
	} else if(resultado == "ligado-relay") {
	
	// Streaming com relay ligado -> desligar streaming
	desligar_streaming( porta );
	
	} else if(resultado == "desligado") {
	
	// Streaming desligado -> exibir alerta
	//document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming e AutoDJ já estão em desligados.</span>";
	//document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	desligar_streaming( porta );
	
	} else if(resultado == "ligado-autodj") {
		
	// Streaming e autodj ligados -> desligar autodj
	desligar_autodj( porta );
	
	} else if(resultado == "manutencao") {
	
	// Streaming em manutenção -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming esta em manutenção, por favor tente novamente mais tarde.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
	} else {
	
	// Erro desconhecido -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>Erro desconhecido ao executar esta ação, favor entrar em contato com o suporte.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "22px";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}  
  
}

// Função para desligar o streaming
function desligar_streaming( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/desligar_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para reiniciar o streaming/autodj
function reiniciar_streaming_autodj( porta ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/status_streaming_interno/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado == "ligado") {
		
	// Streaming ligado -> desligar e ligar streaming
	desligar_streaming( porta );
	ligar_streaming( porta );
	
	} else if(resultado == "ligado-autodj-desativado") {
		
	// Streaming ligado autodj desativado -> desligar e ligar streaming
	desligar_streaming( porta );
	ligar_streaming( porta );
	
	} else if(resultado == "ligado-relay") {
	
	// Streaming com relay ligado -> desligar e ligar streaming
	desligar_streaming( porta );
	ligar_streaming( porta );
	
	} else if(resultado == "desligado") {
	
	// Streaming desligado -> exibir alerta
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming e AutoDJ não estão ligados.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
	} else if(resultado == "ligado-autodj") {
		
	// Streaming e autodj ligados -> desligar e ligar streaming
	desligar_streaming( porta );
	ligar_streaming( porta );
	carregar_playlists(porta);
	
	} else if(resultado == "manutencao") {
	
	// Streaming em manutenção -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>O Streaming esta em manutenção, por favor tente novamente mais tarde.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "25px";
	
	} else {
	
	// Erro desconhecido -> exibir alerta	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>Erro desconhecido ao executar esta ação, favor entrar em contato com o suporte.</span>";
	document.getElementById("log-sistema-conteudo").style.fontSize = "22px";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}  
  
}

// Função para checar o status do streaming e autodj
function status_streaming( porta ) {
  
  document.getElementById( 'status_streaming' ).innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/status_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById( 'status_streaming' ).innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( porta,recurso,texto ) {
  
  if(recurso == "ouvintes") {
  document.getElementById('estatistica_uso_plano_ouvintes').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  } else {
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  }
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/estatistica_uso_plano/"+porta+"/"+recurso+"/"+texto , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(recurso == "ouvintes") {
  	document.getElementById('estatistica_uso_plano_ouvintes').innerHTML = resultado;
  	} else {
  	document.getElementById('estatistica_uso_plano_ftp').innerHTML = resultado;
  	}
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para desconectar source do streaming(kick)
function kick_streaming( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/kick_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para desconectar source do streaming(kick)
function kick_streaming_multipoint( porta, ponto ) {
	
  if(porta == "" || ponto == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/kick_streaming_multipoint/"+porta+"/"+ponto+"" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar a lista de players
function carregar_players() {
	
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_players" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para carregar o formulário para geração das estatísticas do streaming
function carregar_estatisticas_streaming( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_estatisticas_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para ativar proteção contra ataques ao streaming
function ativar_desativar_protecao( porta ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/ativar_desativar_protecao/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para exibir a música atual tocando no streaming
function musica_atual( porta, local, caracteres ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById(local).innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/musica_atual/"+porta+"/"+caracteres , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById(local).innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para exibir a música atual tocando no streaming
function musica_atual_players( ip, porta, local, caracteres ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById(local).innerHTML = "<img src='http://player.srvstm.com/admin/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/musica_atual/"+porta+"/"+caracteres , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById(local).innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para exibir a música atual tocando no streaming
function capa_musica_atual( porta, local ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById(local).innerHTML = "<img src='http://player.srvstm.com/admin/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/capa_musica_atual/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById(local).src = resultado;
	document.getElementById(local).height = 126;
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover um ponto do multipoint
function remover_multipoint( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_multipoint/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover um pedido de musica
function remover_pedido_musical( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_pedido_musical/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para mudar a música atual no shoutcast para um texto qualquer
function definir_nome_musica( porta, msg ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  var musica = prompt(msg);
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/definir_nome_musica/"+porta+"/"+musica , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
	  
	resultado = http.responseText;
  
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

////////////////////////////////////////////////////////
///////////// Funções Gerenciamento AutoDJ /////////////
////////////////////////////////////////////////////////

// Função para carregar lista de playlists do streaming
function carregar_playlists( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_playlists/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para ligar o autodj
function ligar_autodj( porta,playlist,shuffle,bitrate,xfade,samplerate,canal,encoder ) {
	
  if(porta == "" || playlist == "" || shuffle == "" || bitrate == "" || xfade == "" || samplerate == "" || canal == "" || encoder == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/ligar_autodj/"+porta+"/"+playlist+"/"+shuffle+"/"+bitrate+"/"+xfade+"/"+samplerate+"/"+canal+"/"+encoder+"" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;		
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para recarregar playlist no autodj
function recarregar_playlist( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/recarregar_playlist/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para trocar playlist no autodj sem reiniciar
function trocar_playlist( porta,acao,playlist ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/trocar_playlist/"+porta+"/"+acao+"/"+playlist , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para pular musica atual playlist no autodj
function pular_musica( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/pular_musica/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para desligar o autodj
function desligar_autodj( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/desligar_autodj/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover um DJ do AutoDJ
function remover_dj( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_dj/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover uma restrição de um DJ
function remover_dj_restricao( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_dj_restricao/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para diagnosticar erros no AutoDJ
function diagnosticar_autodj( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/diagnosticar_autodj/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para remover um agendamento de playlist
function remover_agendamento( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_agendamento/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para desligar o streaming
function remover_app_android( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_app_android/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

////////////////////////////////////////////////////////
///////////// Funções Gerenciamento Painel /////////////
////////////////////////////////////////////////////////

// Função para exibir avisos
function exibir_aviso( codigo ) {
	
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/exibir_aviso/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para marcar um aviso como vizualizado
function desativar_exibicao_aviso( codigo, area, codigo_usuario ) {
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/desativar_exibicao_aviso/"+codigo+"/"+area+"/"+codigo_usuario , true);
  http.send(null);
  delete http;
  
}

// Função para sincronizar streaming no servidor AAC+
function sincronizar_aacplus( porta ) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/sincronizar_aacplus/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para atualizar cache player facebook
function atualizar_cache_player_facebook( porta ) {

  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/atualizar_cache_player_facebook/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para atualizar cache dos players
function atualizar_cache_players( porta ) {

  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/atualizar_cache_players/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para configurar o encoder correto no streaming
function configurar_encoder( porta ) {

  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/configurar_encoder/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para gerar qr code do link do app no google play
function gerar_qr_code_app( chave ) {

  if(chave == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/gerar_qr_code_app/"+chave , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
	  
	resultado = http.responseText;
  
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para migrar músicas de um FTP remoto
function migrar_musicas_ftp_remoto( servidor_stm, porta_stm, servidor_ftp, usuario_ftp, senha_ftp ) {

  if(servidor_stm == "" || porta_stm == "" || servidor_ftp == "" || usuario_ftp == "" || senha_ftp == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById("log_migracao").innerHTML = "<center><br><br><br><img src='http://"+get_host()+"/img/ajax-loader.gif' /><br><br><br></center>";
  
  var http = new Ajax();
  http.open("GET", "http://"+servidor_stm+":555/migrar-musicas-ftp.php?porta="+porta_stm+"&servidor="+servidor_ftp+"&usuario="+usuario_ftp+"&senha="+senha_ftp+"" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
	  
	resultado = http.responseText;
  
	document.getElementById("log_migracao").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para executar o script de download de videos do youtube
function youtube_downloader( servidor, porta, url ) {
  
  if(url == "") {
  alert("Error!\n\nPortuguês: URL do vídeo inválida.\n\nEnglish: Invalid video URL.\n\nEspañol: URL del vídeo no es válido.");
  document.getElementById("quadro_requisicao").style.display = "none";
  } else {
  
  var id_youtube = youtube_parser(url);
  
  if(id_youtube == "") {
  alert("Error!\n\nPortuguês: URL do vídeo inválida.\n\nEnglish: Invalid video URL.\n\nEspañol: URL del vídeo no es válido.");
  document.getElementById("quadro_requisicao").style.display = "none";
  } else {

  document.getElementById("img_loader").style.display = "block";
  document.getElementById("quadro_requisicao").style.display = "block";
  document.getElementById("resultado_requisicao").innerHTML = "";
  
  var http = new Ajax();
  http.open("GET", "http://"+servidor+":555/youtube.php?porta="+porta+"&video="+id_youtube+"" , true);
  http.onreadystatechange = function() {
  
  document.getElementById("resultado_requisicao").innerHTML = http.responseText;
  
  // Auto scroll
  var elem = document.getElementById('resultado_requisicao');
  elem.scrollTop = elem.scrollHeight;
  
  if(http.readyState == 4) {
  document.getElementById("img_loader").style.display = "none";
  }
  
  }
  http.send(null);
  delete http;
  }
  }
}

// Função para executar o script de download de musicas pela URL
function mp3_downloader( servidor, porta) {
	
  var nome = document.getElementById('nome').value;
  var tipo = document.getElementById("download_mp3").elements["tipo"].value;
  var pasta = document.getElementById('pasta').value;
  var url = document.getElementById('url').value;
  
  
  if( pasta == "" | url == "") {
	  
  alert("Error!\n\nPortuguês: Dados faltando.\n\nEnglish: Missing filds data.\n\nEspañol: Datos no válidos.");
  document.getElementById("quadro_requisicao").style.display = "none";

  } else {

  document.getElementById("img_loader").style.display = "block";
  document.getElementById("quadro_requisicao").style.display = "block";
  document.getElementById("resultado_requisicao").innerHTML = "";
  
  var http = new Ajax();
  http.open("GET", "http://"+servidor+":555/download-mp3.php?porta="+porta+"&nome="+nome+"&tipo="+tipo+"&pasta="+pasta+"&url="+url+"" , true);
  http.onreadystatechange = function() {
  
  document.getElementById("resultado_requisicao").innerHTML = http.responseText;
  
  // Auto scroll
  var elem = document.getElementById('resultado_requisicao');
  elem.scrollTop = elem.scrollHeight;
  
  if(http.readyState == 4) {
  document.getElementById("img_loader").style.display = "none";
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para executar o script de download de musicas do soundcloud
function soundcloud_downloader( servidor, porta) {
	
  var pasta = document.getElementById('pasta').value;
  var url = document.getElementById('url').value;
  
  if( pasta == "" | url == "") {
	  
  alert("Error!\n\nPortuguês: Dados faltando.\n\nEnglish: Missing filds data.\n\nEspañol: Datos no válidos.");
  document.getElementById("quadro_requisicao").style.display = "none";

  } else {

  document.getElementById("img_loader").style.display = "block";
  document.getElementById("quadro_requisicao").style.display = "block";
  document.getElementById("resultado_requisicao").innerHTML = "Obtendo dados... Getting data...";
  
  var http = new Ajax();
  http.open("GET", "http://"+servidor+":555/download-soundcloud.php?porta="+porta+"&pasta="+pasta+"&url="+url+"" , true);
  http.onreadystatechange = function() {
  
  document.getElementById("resultado_requisicao").innerHTML = http.responseText;
  
  // Auto scroll
  var elem = document.getElementById('resultado_requisicao');
  elem.scrollTop = elem.scrollHeight;
  
  if(http.readyState == 4) {
  document.getElementById("img_loader").style.display = "none";
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Rotina AJAX
function Ajax() {
var req;

try {
 req = new ActiveXObject("Microsoft.XMLHTTP");
} catch(e) {
 try {
	req = new ActiveXObject("Msxml2.XMLHTTP");
 } catch(ex) {
	try {
	 req = new XMLHttpRequest();
	} catch(exc) {
	 alert("Esse browser não tem recursos para uso do Ajax");
	 req = null;
	}
 }
}

return req;
}