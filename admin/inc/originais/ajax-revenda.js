////////////////////////////////////////////////////////
/////////// Fun��es Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Fun��o para ligar o streaming
function ligar_streaming( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/ligar_streaming/"+porta , true);
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

// Fun��o para desligar o streaming
function desligar_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/desligar_streaming/"+porta , true);
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

// Fun��o para ligar streamings em massa da revenda
function ligar_streamings_revenda() {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/ligar_streamings_revenda" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
  	

}

// Fun��o para ligar autodjs em massa da revenda
function ligar_autodjs_revenda() {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/ligar_autodjs_revenda" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;

}

// Fun��o para bloquear o streaming
function bloquear_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/bloquear_streaming/"+porta , true);
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

// Fun��o para desbloquear o streaming
function desbloquear_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/desbloquear_streaming/"+porta , true);
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

// Fun��o para remover o streaming
function remover_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/remover_streaming/"+porta , true);
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

// Fun��o para checar o status do streaming e autodj
function status_streaming( porta ) {
  
  document.getElementById( 'status_streaming' ).innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/status_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById( 'status_streaming' ).innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para checar o status do streaming e autodj na p�gina de resultados de busca avan�ada
function status_streaming_busca_avancada( porta ) {
  
  document.getElementById( porta ).innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/status_streaming_busca_avancada/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	status_final = resultado.split("|");
	
	if(status_final[0] == "manutencao") {
		
	document.getElementById( porta ).innerHTML = "Manuten��o";
	document.getElementById( porta ).style.backgroundColor = "#FFB3B3";
	
	} else {
	
	document.getElementById( porta ).innerHTML = status_final[0];
	document.getElementById( porta ).style.backgroundColor = status_final[1];
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para checar o status do streaming e autodj na p�gina de listagem de streamings da subrevenda
function status_streaming_subrevenda( porta ) {
  
  document.getElementById( porta ).innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/status_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado == "ligado") {
		
	document.getElementById( porta ).innerHTML = "Ligado";
	document.getElementById( porta ).style.backgroundColor = "#A8FFA8";
	
	} else if(resultado == "ligado-autodj") {
	
	document.getElementById( porta ).innerHTML = "AutoDJ";
	document.getElementById( porta ).style.backgroundColor = "#A8FFA8";
	
	} else if(resultado == "ligado-relay") {
	
	document.getElementById( porta ).innerHTML = "Relay";
	document.getElementById( porta ).style.backgroundColor = "#A8FFA8";
	
	} else if(resultado == "desligado") {
	
	document.getElementById( porta ).innerHTML = "Desligado";
	document.getElementById( porta ).style.backgroundColor = "#FFB3B3";
	
	} else if(resultado == "manutencao") {
	
	document.getElementById( porta ).innerHTML = "Manuten��o";
	document.getElementById( porta ).style.backgroundColor = "#FFB3B3";
	
	} else {
	
	document.getElementById( porta ).style.backgroundColor = "#FFFF97";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( porta,recurso,texto ) {
  
  if(recurso == "ouvintes") {
  document.getElementById('estatistica_uso_plano_ouvintes').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  } else {
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  }
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/estatistica_uso_plano/"+porta+"/"+recurso+"/"+texto , true);
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

// Fun��o para desconectar source do streaming(kick)
function kick_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/kick_streaming/"+porta , true);
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


// Fun��o para ativar prote��o contra ataques ao streaming
function ativar_desativar_protecao( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/ativar_desativar_protecao/"+porta , true);
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

// Fun��o para sincronizar streaming no servidor AAC+
function sincronizar_aacplus( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/sincronizar_aacplus/"+porta , true);
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

// Fun��o para sincronizar as playlists do streaming no servidor
function sincronizar_playlists( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/sincronizar_playlists/"+porta , true);
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

// Fun��o para exibir a m�sica atual tocando no streaming
function musica_atual( porta, local, caracteres ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById(local).innerHTML = "<img src='http://"+get_host()+"/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/musica_atual/"+porta+"/"+caracteres , true);
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

// Fun��o para carregar a lista de players
function carregar_players(porta) {
  
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/carregar_players/"+porta , true);
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

// Fun��o para configurar o encoder correto no streaming
function configurar_encoder( porta ) {

  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://cdn.srvstm.com/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/configurar_encoder/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para atualizar cache player facebook
function atualizar_cache_player_facebook( porta ) {

  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://cdn.srvstm.com/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/atualizar_cache_player_facebook/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para revenda acessar painel do streaming
function acessar_painel_streaming_revenda( porta ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/acessar_painel_streaming_revenda/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	dados = resultado.split("|");
	
	if(dados[0] == "1") {
	window.location = "http://"+get_host()+"/login-autentica-revenda/"+dados[1]+"";
	} else {
	document.getElementById("log-sistema-conteudo").innerHTML = dados[1];
	}
	
  }
  
  }
  http.send(null);
  delete http;
}

////////////////////////////////////////////////////////
///////////// Fun��es Gerenciamento AutoDJ /////////////
////////////////////////////////////////////////////////

// Fun��o para carregar lista de playlists do streaming
function carregar_playlists( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/carregar_playlists/"+porta , true);
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

// Fun��o para ligar o autodj
function ligar_autodj( porta,playlist,shuffle,bitrate,xfade ) {
	
  if(porta == "" || playlist == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/ligar_autodj/"+porta+"/"+playlist+"/"+shuffle+"/"+bitrate+"/"+xfade+"" , true);
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

// Fun��o para recarregar playlist no autodj
function recarregar_playlist( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/recarregar_playlist/"+porta , true);
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

// Fun��o para pular musica atual playlist no autodj
function pular_musica( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/pular_musica/"+porta , true);
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

// Fun��o para desligar o autodj
function desligar_autodj( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/desligar_autodj/"+porta , true);
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

// Fun��o para diagnosticar erros no AutoDJ
function diagnosticar_autodj( porta ) {
	
  if(porta == "") {
  alert("Error!\n\nPortugu�s: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspa�ol: Los datos que faltaban int�ntelo de nuevo o contacte con Atenci�n.");
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://cdn.srvstm.com/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/diagnosticar_autodj/"+porta , true);
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
///////////// Fun��es Gerenciamento Painel /////////////
////////////////////////////////////////////////////////

// Fun��o para exibir avisos
function exibir_aviso( codigo ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/exibir_aviso/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para marcar um aviso como vizualizado
function desativar_exibicao_aviso( codigo, area, codigo_usuario ) {
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/desativar_exibicao_aviso/"+codigo+"/"+area+"/"+codigo_usuario , true);
  http.send(null);
  delete http;
  
}

// Fun��o para desbloquear IP bloqueado no login
function desbloquear_ip_login( codigo ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/desbloquear_ip_login/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;
	
	removeRow(codigo);
	
  }
  
  }
  http.send(null);
  delete http;
}

////////////////////////////////////////////////////////
//////////// Fun��es Gerenciamento Revenda /////////////
////////////////////////////////////////////////////////

// Fun��o para admin/revenda acessar painel de streaming
function acessar_painel_streaming( porta ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/acessar_painel_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	window.location = "http://"+get_host()+"/login-autentica-revenda/"+resultado+"";
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para buscar um streaming diretamente pela porta
function buscar_streaming_revenda() {

  var porta = prompt("Informe a porta do streaming que deseja buscar:");

  if(porta) {

  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/buscar_streaming_revenda/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	window.location = "/admin/revenda/"+resultado+"";
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

////////////////////////////////////////////////////////
////////// Fun��es Gerenciamento Sub Revenda ///////////
////////////////////////////////////////////////////////

// Fun��o para bloquear uma sub revenda
function bloquear_subrevenda( codigo ) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/bloquear_subrevenda/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para desbloquear uma sub revenda
function desbloquear_subrevenda( codigo ) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/desbloquear_subrevenda/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para remover uma sub revenda
function remover_subrevenda( codigo ) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/remover_subrevenda/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para mover um streaming para a revenda principal
function mover_streaming_subrevenda_revenda( porta ) {

  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/mover_streaming_subrevenda_revenda/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;

	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Fun��o para mover um streaming para outra sub revenda
function mover_streaming_subrevenda_subrevenda( porta, id ) {
  
  if(id == "") {
  alert("Error!\n\nPt-BR: ID vazio!\nEn-US: ID empty!\nES: ID vac�o!");
  } else {
	  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/mover_streaming_subrevenda_subrevenda/"+porta+"/"+id , true);
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

// Fun��o para mover um streaming para outra sub revenda
function mover_streaming_revenda_subrevenda( porta, id ) {
  
  if(id == "") {
  alert("Error!\n\nPt-BR: ID vazio!\nEn-US: ID empty!\nES: ID vac�o!");
  } else {
	  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/mover_streaming_revenda_subrevenda/"+porta+"/"+id , true);
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

// Fun��o para buscar uma sub revenda diretamente pelo ID
function buscar_subrevenda() {

  var id = prompt("ID:");

  if(id) {

  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax-revenda/buscar_subrevenda/"+id , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	dados = resultado.split("|");
	
	if(dados[0] == "1") {
	window.location = "/admin/revenda/subrevenda/"+dados[1]+"";
	} else {
	document.getElementById("log-sistema-conteudo").innerHTML = dados[1];	
	}
	
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
	 alert("Esse browser n�o tem recursos para uso do Ajax");
	 req = null;
	}
 }
}

return req;
}