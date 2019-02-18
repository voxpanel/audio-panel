////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Streaming ////////////
////////////////////////////////////////////////////////

// Função para ligar o streaming
function ligar_streaming( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/ligar_streaming/"+porta , true);
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
function desligar_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/desligar_streaming/"+porta , true);
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

// Função para bloquear o streaming
function bloquear_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/bloquear_streaming/"+porta , true);
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

// Função para desbloquear o streaming
function desbloquear_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/desbloquear_streaming/"+porta , true);
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

// Função para remover o streaming
function remover_streaming( porta ) {
	
  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/remover_streaming/"+porta , true);
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
  
  document.getElementById( porta ).innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/status_streaming/"+porta , true);
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
	
	document.getElementById( porta ).innerHTML = "Manutenção";
	document.getElementById( porta ).style.backgroundColor = "#FFB3B3";
	
	} else {
	
	document.getElementById( porta ).style.backgroundColor = "#FFFF97";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para checar o status do streaming e autodj no painel do cliente final
function status_streaming_cliente( porta ) {
  
  document.getElementById( porta ).innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
	
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/status_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado == "ligado") {
		
	document.getElementById( porta ).innerHTML = "Ligado";
	document.getElementById( porta ).className = "texto_status_streaming_online";
	
	} else if(resultado == "ligado-autodj") {
	
	document.getElementById( porta ).innerHTML = "AutoDJ";
	document.getElementById( porta ).className = "texto_status_streaming_online";
	
	} else if(resultado == "desligado") {
	
	document.getElementById( porta ).innerHTML = "Desligado";
	document.getElementById( porta ).className = "texto_status_streaming_offline";
	
	} else {
	
	document.getElementById( porta ).innerHTML = "Erro";
	document.getElementById( porta ).className = "texto_status_streaming_offline";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para ativar proteção contra ataques ao streaming
function ativar_desativar_protecao( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/ativar_desativar_protecao/"+porta , true);
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

// Função para sincronizar streaming no servidor AAC+
function sincronizar_aacplus( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/sincronizar_aacplus/"+porta , true);
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

// Função para sincronizar as playlists do streaming no servidor
function sincronizar_playlists( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/sincronizar_playlists/"+porta , true);
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

// Função para gerar qr code do link do app no google play
function gerar_qr_code_app( pkg ) {

  if(pkg == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/gerar_qr_code_app/"+pkg , true);
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

// Função para alterar senha de um streaming
function alterar_senha_streaming( porta,tipo ) {
  
  var nova_senha = prompt('Informe a nova senha:');
	
  if(nova_senha != "" && nova_senha != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/alterar_senha_streaming/"+porta+"/"+tipo+"/"+nova_senha , true);
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
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/carregar_playlists/"+porta , true);
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
	
  if(porta == "" || playlist == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/ligar_autodj/"+porta+"/"+playlist+"/"+shuffle+"/"+bitrate+"/"+xfade+"" , true);
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
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/recarregar_playlist/"+porta , true);
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
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/pular_musica/"+porta , true);
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
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/desligar_autodj/"+porta , true);
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
//////////// Funções Gerenciamento Revenda /////////////
////////////////////////////////////////////////////////

// Função para bloquear revenda
function bloquear_revenda( codigo ) {
	
  if(codigo == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/bloquear_revenda/"+codigo , true);
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

// Função para desbloquear revenda
function desbloquear_revenda( codigo ) {
	
  if(codigo == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/desbloquear_revenda/"+codigo , true);
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

// Função para remover revenda
function remover_revenda( codigo ) {
	
  if(codigo == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/remover_revenda/"+codigo , true);
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

// Função para alterar senha de uma revenda
function alterar_senha_revenda( codigo ) {
  
  var nova_senha = prompt('Informe a nova senha:');
	
  if(nova_senha != "" && nova_senha != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/alterar_senha_revenda/"+codigo+"/"+nova_senha , true);
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
/////////// Funções Gerenciamento Servidor /////////////
////////////////////////////////////////////////////////

// Função para ligar todos os streaming do servidor
function ligar_streamings_servidor( servidor ) {
	
  if(servidor == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/listar_streamings_servidor/"+servidor , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	streamings = http.responseText;
	
	array_streamings = streamings.split("|");
  
  	for(var cont = 0; cont < array_streamings.length; cont++) {
  
  	var porta = array_streamings[cont];
	
  	ligar_streaming( porta );
  
  	}
	
	document.getElementById('log-sistema-conteudo').innerHTML = "<span class='texto_status_sucesso'>Streamings ligados com sucesso!</span>";
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para ligar todos os streamings em todos os servidores
function ligar_streamings_geral() {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/listar_streamings_geral" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	streamings = http.responseText;
	
	array_streamings = streamings.split("|");
  
  	for(var cont = 0; cont < array_streamings.length; cont++) {
  
  	var porta = array_streamings[cont];
	
  	ligar_streaming( porta );
  
  	}
	
	document.getElementById('log-sistema-conteudo').innerHTML = "<span class='texto_status_sucesso'>Streamings ligados com sucesso!</span>";
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para ligar todos os streaming do servidor
function ligar_autodjs_servidor( servidor ) {
	
  if(servidor == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/listar_streamings_autodj_servidor/"+servidor , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	streamings = http.responseText;
	
	array_streamings = streamings.split("|");
  
  	for(var cont = 0; cont < array_streamings.length; cont++) {
	
	var array_dados_porta = array_streamings[cont].split(",");
  
  	var porta = array_dados_porta[0];
	var playlist = array_dados_porta[1];
	var shuffle = array_dados_porta[2];
	var bitrate = array_dados_porta[3];
	var xfade = array_dados_porta[4];
	
	if(playlist != 0) {

	ligar_autodj( porta,playlist,shuffle,bitrate,xfade );
	
	}
	
  	}
	
	document.getElementById('log-sistema-conteudo').innerHTML = "<span class='texto_status_sucesso'>AutoDJs ligados com sucesso!</span>";
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para ligar todos os autodjs em todos os servidores
function ligar_autodjs_geral() {

  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/listar_streamings_autodj_geral" , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	streamings = http.responseText;
	
	array_streamings = streamings.split("|");
  
  	for(var cont = 0; cont < array_streamings.length; cont++) {
	
	var array_dados_porta = array_streamings[cont].split(",");
  
  	var porta = array_dados_porta[0];
	var playlist = array_dados_porta[1];
	var shuffle = array_dados_porta[2];
	var bitrate = array_dados_porta[3];
	var xfade = array_dados_porta[4];
	
	if(playlist != 0) {

	ligar_autodj( porta,playlist,shuffle,bitrate,xfade );
		
	}
	
  	}
	
	document.getElementById('log-sistema-conteudo').innerHTML = "<span class='texto_status_sucesso'>AutoDJs ligados com sucesso!</span>";
	
  }
  
  }
  http.send(null);
  delete http;

}

// Função para desligar todos os streaming do servidor
function desligar_streamings_servidor( servidor ) {
	
  if(servidor == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/listar_streamings_servidor/"+servidor , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	streamings = http.responseText;
	
	array_streamings = streamings.split("|");
  
  	for(var cont = 0; cont < array_streamings.length; cont++) {
  
  	var porta = array_streamings[cont];
	
  	desligar_streaming( porta );
  
  	}	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}


// Função para sincronizar streaming no servidor AAC+
function sincronizar_aacplus_servidor( servidor ) {

  if(servidor == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/sincronizar_aacplus_servidor/"+servidor , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	streamings = http.responseText;
	
	array_streamings = streamings.split("|");
  
  	for(var cont = 0; cont < array_streamings.length; cont++) {
  
  	var porta = array_streamings[cont];
	
  	sincronizar_aacplus( porta );
  
  	}	
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para sincronizar as playlists dos streamings no servidor
function sincronizar_playlists_servidor( servidor ) {

  if(servidor == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/sincronizar_playlists_servidor/"+servidor , true);
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

// Função para remover uma dica rápida
function remover_dica_rapida( codigo ) {
	
  if(codigo == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/remover_dica_rapida/"+codigo , true);
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

// Função para ativar/desativar manutenção em um servidor
function manutencao_servidor( codigo, acao, mensagem ) {
	
  if(codigo == "" && acao == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/manutencao_servidor/"+codigo+"/"+acao+"/"+mensagem , true);
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

// Função para exibir avisos
function exibir_aviso( codigo ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/exibir_aviso/"+codigo , true);
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
  http.open("GET", "/admin/funcoes-ajax/desativar_exibicao_aviso/"+codigo+"/"+area+"/"+codigo_usuario , true);
  http.send(null);
  delete http;
  
}

// Função para mudar o status de exibição de um aviso
function alterar_status_aviso( codigo ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/alterar_status_aviso/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para remover um aviso
function remover_aviso( codigo ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/remover_aviso/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;	
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para remover uma requisição de app android
function remover_app_android( codigo ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/remover_app_android/"+codigo , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById("log-sistema-conteudo").innerHTML = resultado;
	
  }
  
  }
  http.send(null);
  delete http;
}

// Função para admin/revenda acessar painel de streaming
function acessar_painel_streaming( porta ) {
	
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/admin/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/admin/funcoes-ajax/acessar_painel_streaming/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	window.location = "http://"+get_host()+"/login-autentica-admin/"+resultado+"";
	
  }
  
  }
  http.send(null);
  delete http;
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