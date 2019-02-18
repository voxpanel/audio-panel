////////////////////////////////////////
/////////// Funções Players ////////////
////////////////////////////////////////

// Função para exibir a música atual tocando no streaming
function musica_atual_players( servidor, porta, local, caracteres ) {
	
  if(porta == "") {
  document.getElementById(local).innerHTML = "Error!";
  } else {
  
  document.getElementById(local).innerHTML = "<img src='//"+get_host()+"/img/spinner.gif' />";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax-players/musica_atual/"+servidor+"/"+porta+"/"+caracteres , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById(local).innerHTML = "<img src='https://player.srvstm.com/img/icones/img-icone-arquivo-musica.png' width='16' height='16' border='0' align='absmiddle' />&nbsp;"+resultado;
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para exibir a música atual tocando no streaming
function capa_musica_atual( servidor, porta, local, altura ) {
	
  if(porta == "") {
  document.getElementById(local).innerHTML = "Error!";
  } else {
  
  document.getElementById(local).src = "//"+get_host()+"/img/spinner.gif";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax-players/capa_musica_atual/"+servidor+"/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	document.getElementById(local).src = resultado;
	document.getElementById(local).height = altura;
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

}

function abrir_janela( url,largura,altura ) {
window.open( url, "","width="+largura+",height="+altura+",toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO" );
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