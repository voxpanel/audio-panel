////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////
 
// Função para ligar o streaming
function ligar_streaming( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/ligar_streaming/"+porta , true);
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

// Função para desligar o streaming
function desligar_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/desligar_streaming/"+porta , true);
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

// Função para checar o status do streaming e autodj
function status_streaming( porta ) {
  
  document.getElementById( porta ).innerHTML = "<img src='http://"+get_host()+"/movel/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/status_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById( porta ).innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( porta,recurso,texto ) {
  
  if(recurso == "ouvintes") {
  document.getElementById('estatistica_uso_plano_ouvintes').innerHTML = "<img src='http://"+get_host()+"/movel/img/spinner.gif' />";
  } else {
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/movel/img/spinner.gif' />";
  }
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/estatistica_uso_plano/"+porta+"/"+recurso+"/"+texto , true);
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
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/kick_streaming/"+porta , true);
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
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/carregar_playlists/"+porta , true);
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
function ligar_autodj( porta,playlist,shuffle,bitrate,xfade ) {
	
  if(porta == "" || playlist == "" || shuffle == "" || bitrate == "" || xfade == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/ligar_autodj/"+porta+"/"+playlist+"/"+shuffle+"/"+bitrate+"/"+xfade+"" , true);
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
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/recarregar_playlist/"+porta , true);
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
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/pular_musica/"+porta , true);
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
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/movel/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/desligar_autodj/"+porta , true);
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
  alert("Oops! Ocorreu um erro ao processar sua requisição!\n\nContate o suporte para maiores detalhes\n\nErro: Dados faltando.");
  } else {
  
  document.getElementById(local).innerHTML = "<img src='http://cdn.srvstm.com/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/movel/funcoes-ajax/musica_atual/"+porta+"/"+caracteres , true);
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

// Função para remover um pedido de musica
function remover_pedido_musical( codigo ) {
	
  if(codigo == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/dj/funcoes-ajax/remover_pedido_musical/"+codigo , true);
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