////////////////////////////////////////////////////////
//////////// Funções Gerenciamento Músicas /////////////
////////////////////////////////////////////////////////

// Função para carregar as pastas
function carregar_pastas( porta ) {

  if(porta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  // Limpa a lista de playlist já carregadas
  document.getElementById("lista-pastas").innerHTML = "";
  
  document.getElementById("status_lista_pastas").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("status_lista_pastas").style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_lista_pastas/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_pastas = resultado.split(";");
	
	for(var cont = 0; cont < array_pastas.length; cont++) {	
	 
	if(array_pastas[cont]) {
	
	dados_pasta = array_pastas[cont].split("|");
	
	var nova_pasta = document.createElement("li");
	
	nova_pasta.innerHTML = "<img src='/admin/img/icones/img-icone-pasta.png' align='absmiddle' />&nbsp;<a href='javascript:carregar_musicas_pasta(\""+porta+"\",\""+dados_pasta[0]+"\");'>"+dados_pasta[0]+"&nbsp;("+dados_pasta[1]+")</a><a href='javascript:remover_pasta(\""+porta+"\",\""+dados_pasta[0]+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover' border='0' align='absmiddle' /></a><a href='javascript:renomear_pasta(\""+porta+"\",\""+dados_pasta[0]+"\")' style='float:right;padding-right:5px;'><img src='/admin/img/icones/img-icone-renomear.png' alt='Renomear' title='Renomear' border='0' align='absmiddle' /></a>";
  
    document.getElementById("lista-pastas").appendChild(nova_pasta);
	
	document.getElementById("status_lista_pastas").style.display = "none";
	
	document.getElementById('log-sistema-fundo').style.display = "none";
    document.getElementById('log-sistema').style.display = "none";
	
	}
	
	}
	
	} else {
	
	document.getElementById("status_lista_playlists").innerHTML = "Nenhuma pasta encontrada.";
	
	}
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as músicas da pasta do FTP no gerenciamento de musicas
function carregar_musicas_pasta( porta,pasta ) {
	
  if(porta == "" || pasta == "") {
  alert("Erro! Tente novamente ou contate o suporte.");
  } else {
  
  // Limpa a lista de músicas já carregadas
  document.getElementById("lista-musicas-pasta").innerHTML = "";
  
  // Seleciona a pasta para uploads
  document.getElementById("pasta_selecionada").value = pasta;
  document.getElementById("msg_pasta_selecionada").innerHTML = pasta;
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  document.getElementById('msg_pasta').style.display = "none";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_musicas_pasta/"+porta+"/"+pasta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_musicas = resultado.split(";");
	
	for(var cont = 0; cont < array_musicas.length; cont++) {	
	 
	if(array_musicas[cont]) {
	
	dados_musica = array_musicas[cont].split("|");
	
	var nova_musica = document.createElement("li");
	
	var nome = dados_musica[1];
	var duracao = dados_musica[2];
	var path_musica = dados_musica[0];
	
	if (/[^a-z0-9_\-\. ]/gi.test(nome)) {
	
	nova_musica.innerHTML = "<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' title='Arquivo inválido com caracteres especiais.\n\nInvalid file with special chars.\n\nNombre de archivo no válido con caracteres especiales.' />&nbsp;["+duracao+"]&nbsp;"+nome+"<span style='float:right;'><a href='javascript:renomear_musica_ftp(\""+porta+"\",\""+path_musica+"\");' title='Renomear "+nome+"'><img src='/admin/img/icones/img-icone-renomear.png' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_musica_ftp(\""+porta+"\",\""+path_musica+"\")'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover "+nome+"' title='Remover "+nome+"' border='0' align='absmiddle' /></a></span>";
	
	nova_musica.style.backgroundColor = "#FFBFBF";
	
	} else {
	
    nova_musica.innerHTML = "<img src='/admin/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;["+duracao+"]&nbsp;"+nome+"<span style='float:right;'><a href='javascript:play_musica(\""+porta+"\",\""+path_musica+"\");' title='Play "+nome+"'><img src='/img/icones/img-icone-relay.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:renomear_musica_ftp(\""+porta+"\",\""+path_musica+"\");' title='Renomear "+nome+"'><img src='/admin/img/icones/img-icone-renomear.png' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_musica_ftp(\""+porta+"\",\""+path_musica+"\")'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover "+nome+"' title='Remover "+nome+"' border='0' align='absmiddle' /></a></span>";
	
	}
  
    document.getElementById("lista-musicas-pasta").appendChild(nova_musica);
	
	}
	
	}
	
	}
	
  document.getElementById('log-sistema-fundo').style.display = "none";
  document.getElementById('log-sistema').style.display = "none";
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para criar uma nova pasta no FTP
function criar_pasta( porta ) {
  
  var pasta = prompt('Informe um nome para a nova pasta:\n(Não use caracteres especiais e acentos)');
	
  if(pasta != "" && pasta != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/criar_pasta/"+porta+"/"+pasta , true);
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

// Função para renomear uma musica no FTP
function renomear_pasta( porta,pasta ) {

  if(pasta == "") {  
  alert("Atenção! Você deve clicar na pasta que deseja renomear.");  
  } else {
	  
  novo = prompt ("Informe o novo nome para a pasta:\n(Não use caracteres especiais e acentos)");
  
  if(novo != "" && novo != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/renomear_pasta/"+porta+"/"+pasta+"/"+novo , true);
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
}

// Função para remover uma pasta
function remover_pasta( porta,pasta ) {
  
  if(window.confirm("Deseja realmente remover esta pasta e todas as suas músicas?")) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_pasta/"+porta+"/"+pasta , true);
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

// Função para renomear uma musica no FTP
function renomear_musica_ftp( porta,musica,novo ) {

  if(musica == "") {  
  alert("Atenção! Você deve clicar na música que deseja renomear.");  
  } else {
	  
  novo = prompt ("Informe o novo nome para a música:\n(Não use caracteres especiais e acentos)");
  
  if(novo != "" && novo != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var musica = musica.replace("/", "|");
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/renomear_musica_ftp/"+porta+"/"+musica+"/"+novo , true);
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
}

// Função para remover uma musica no FTP
function remover_musica_ftp( porta,musica ) {

  if(musica == "") {  
  alert("Atenção! Você deve selecionar a música que deseja remover.");  
  } else {
  
  if(window.confirm("Deseja realmente remover esta música?")) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var musica = musica.replace("/", "|");
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_musica_ftp/"+porta+"/"+musica , true);
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
}

// Função para iniciar envio de músicas
function enviar_musicas( pasta ) {
  
  if(pasta == "") {
  
  alert("Atenção! Você deve clicar em uma pasta para onde serão enviadas as músicas para seleciona-la.\n\nAttention! You must click a folder where the song will be uploaded to select it.\n\n¡Atención! Debe hacer clic en una carpeta donde se enviará la música para seleccionarlo.");  
  
  } else {
	  
  LeftPosition = (screen.width) ? (screen.width-400)/2 : 0;
  TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
  
  window.open( "/gerenciar-musicas-upload/"+pasta+"", "","width=400,height=500,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO,top="+TopPosition+",left="+LeftPosition+"" );
  
  }

}

// Função para checar a estatistica de uso do plano e criar barra de porcentagem de uso
function estatistica_uso_plano( porta,recurso,texto ) {
  
  if(recurso == "ouvintes") {
  document.getElementById('estatistica_uso_plano_ouvintes').innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
  } else {
  document.getElementById('estatistica_uso_plano_ftp').innerHTML = "<img src='http://"+get_host()+"/admin/img/spinner.gif' />";
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

// Função para carregar player da musica(previa)
function play_musica( porta, musica ) {
	
  if(porta != "" && musica != null) {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/play_musica/"+porta+"/"+musica , true);
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

// Função para obter o host
function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

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