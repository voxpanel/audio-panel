// Funções Gerais
function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

}

function abrir_janela( url,largura,altura ) {

window.open( url, "","width="+largura+",height="+altura+",toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO" );

}

// Execurar as ações do menu principal do painel de streaming
function executar_acao_streaming_autodj( codigo,acao ) {
	
	// Streaming
	
	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "streaming-ligar") {
		ligar_streaming( codigo );
	} else if(acao == "streaming-desligar") {
		desligar_streaming( codigo );
	} else if(acao == "streaming-informacoes") {
		abrir_log_sistema();
		window.open("/informacoes","conteudo");
	} else if(acao == "streaming-configurar") {
		abrir_log_sistema();
		window.open("/configuracoes-streaming","conteudo");
	} else if(acao == "streaming-configurar-relay") {
		abrir_log_sistema();
		window.open("/configuracoes-relay","conteudo");
	} else if(acao == "streaming-players") {
		abrir_log_sistema();
		carregar_players();
	} else if(acao == "streaming-kick") {
		kick_streaming( codigo );
	} else if(acao == "streaming-app-android") {
		abrir_log_sistema();
		window.open("/app-android","conteudo");
	} else if(acao == "streaming-protecao") {		
		if(window.confirm("Atenção! Ative esta proteção somente se esta porta estiver sofrendo ataques.\n\nFlood Protection.")) {
			ativar_desativar_protecao( codigo );
		}		
	} else if(acao == "streaming-multipoint") {
		abrir_log_sistema();
		window.open("/gerenciar-multipoint","conteudo");
	} else if(acao == "streaming-logs-servidor") {
		abrir_log_sistema();
		window.open("/logs-shoutcast","conteudo");
	} else if(acao == "streaming-dados-conexao") {
		abrir_log_sistema();
		window.open("/dados-conexao","conteudo");
	
	// Ouvintes
	
	} else if(acao == "ouvintes-ouvintes-conectados") {
		abrir_log_sistema();
		window.open("/ouvintes-conectados","conteudo");
	} else if(acao == "ouvintes-estatisticas") {
		carregar_estatisticas_streaming( codigo );
	} else if(acao == "ouvintes-pedidos-musicais") {
		abrir_log_sistema();
		window.open("/pedidos","conteudo");
	} else if(acao == "ouvintes-chat") {
		abrir_log_sistema();
		window.open("/chat","conteudo");
	} else if(acao == "remover-pedido-musical") {
		remover_pedido_musical( codigo );
		
	// AutoDJ
	
	} else if(acao == "autodj-ligar") {
		carregar_playlists( codigo );
	} else if(acao == "autodj-desligar") {
		desligar_autodj( codigo );
	} else if(acao == "autodj-pular-musica") {
		pular_musica( codigo );
	} else if(acao == "autodj-recarregar-playlist") {
		recarregar_playlist( codigo );
	} else if(acao == "autodj-trocar-playlist") {
		trocar_playlist( codigo, 'carregar_playlists','0' );
	} else if(acao == "autodj-gerenciar-musicas") {
		abrir_log_sistema();
		window.open("/gerenciar-musicas","conteudo");
	} else if(acao == "autodj-gerenciar-playlists") {
		abrir_log_sistema();
		window.open("/playlists","conteudo");
	} else if(acao == "autodj-gerenciar-djs") {
		abrir_log_sistema();
		window.open("/gerenciar-djs","conteudo");
	} else if(acao == "autodj-gerenciar-agendamentos") {
		abrir_log_sistema();
		window.open("/gerenciar-agendamentos","conteudo");
	} else if(acao == "autodj-gerenciar-hora-certa") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists-hora-certa","conteudo");
	} else if(acao == "autodj-gerenciar-vinhetas-comerciais") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists-vinhetas-comerciais","conteudo");
	} else if(acao == "autodj-remover-dj") {
		remover_dj( codigo );
	} else if(acao == "autodj-remover-dj-restricao") {
		remover_dj_restricao( codigo );
	} else if(acao == "autodj-remover-agendamento") {
		remover_agendamento( codigo );
	} else if(acao == "autodj-configurar") {
		abrir_log_sistema();
		window.open("/configuracoes-autodj","conteudo");
	} else if(acao == "autodj-logs-servidor") {
		abrir_log_sistema();
		window.open("/logs-autodj","conteudo");
	
		
	// Painel de Controle
	} else if(acao == "painel-logs") {
		abrir_log_sistema();
		window.open("/logs","conteudo");
	} else if(acao == "painel-ajuda") {
		abrir_log_sistema();
		window.open("/ajuda","conteudo");
	} else if(acao == "painel-api") {
		abrir_log_sistema();
		window.open("/streaming-api","conteudo");	
	} else if(acao == "painel-configurar") {
		abrir_log_sistema();
		window.open("/configuracoes-painel","conteudo");
	} else if(acao == "painel-downloads") {
		abrir_log_sistema();
		window.open("/downloads","conteudo");
		
	// Utilitários 
	
	} else if(acao == "utilitario-download-youtube") {
		abrir_log_sistema();
		window.open("/utilitario-youtube","conteudo");
	} else if(acao == "utilitario-download-mp3") {
		abrir_log_sistema();
		window.open("/utilitario-download-mp3","conteudo");
	} else if(acao == "utilitario-download-soundcloud") {
		abrir_log_sistema();
		window.open("/utilitario-download-soundcloud","conteudo");
	} else if(acao == "utilitario-gravador") {
		abrir_log_sistema();
		window.open("/utilitario-gravador","conteudo");
	} else if(acao == "utilitario-migrar-musicas") {
		abrir_log_sistema();
		window.open("/utilitario-migrar-musicas","conteudo");
	} else if(acao == "utilitario-renomear-musicas") {
		abrir_log_sistema();
		window.open("/utilitario-renomear-musicas","conteudo");
	
	// Solução de Problemas
	
	} else if(acao == "solucao-problemas-sincronizar-aacplus") {
		sincronizar_aacplus( codigo );
	} else if(acao == "solucao-problemas-player-facebook") {
		atualizar_cache_player_facebook( codigo );
	} else if(acao == "solucao-problemas-encoder") {
		configurar_encoder( codigo );
	} else if(acao == "solucao-problemas-diagnosticar-autodj") {
		diagnosticar_autodj( codigo );
	} else if(acao == "solucao-problemas-player-cache") {
		atualizar_cache_players( codigo );
		
	// Diversos
	
	} else if(acao == "remover-app-android") {
		remover_app_android( codigo );
		
	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";

}

function executar_acao_multipoint( codigo,acao ) {
	
	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "remover-ponto") {

		if(window.confirm("Remover?\nRemove?\nRetire?")) {
			remover_multipoint( codigo );
		}
	}

	document.getElementById(codigo).value = "";
}

function executar_acao_playlist( codigo,acao ) {
	
	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "iniciar") {
		iniciar_playlist( codigo );
	} else if(acao == "programetes") {
		abrir_log_sistema();
		window.open("/gerenciar-programets/"+codigo+"","conteudo");
	} else if(acao == "gerenciar") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists/"+codigo+"","conteudo");
	} else if(acao == "gerenciar-basico") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists-basico/"+codigo+"","conteudo");
	} else if(acao == "duplicar") {
		duplicar_playlist( codigo );
	} else if(acao == "intercalar-musicas") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists-intercalar-musicas/"+codigo+"","conteudo");
	} else if(acao == "hora-certa-configurar") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists-hora-certa/"+codigo+"","conteudo");
	} else if(acao == "hora-certa-remover") {
		
		if(window.confirm("Remover?\nRemove?\nBorrar?")) {
			remover_hora_certa_playlist( codigo );
		}
	} else if(acao == "vinhetas-comerciais-configurar") {
		abrir_log_sistema();
		window.open("/gerenciar-playlists-vinhetas-comerciais/"+codigo+"","conteudo");
	} else if(acao == "vinhetas-comerciais-remover") {
		
		if(window.confirm("Remover?\nRemove?\nBorrar?")) {
			remover_vinhetas_comerciais_playlist( codigo );
		}
	} else if(acao == "remover") {
			remover_playlist( codigo );
	}

	document.getElementById(codigo).value = "";
}

function checar_status_streamings( streamings ) {
  
  array_streamings = streamings.split("|");
  
  for(var cont = 0; cont < array_streamings.length; cont++) {
  
  var porta = array_streamings[cont];
  
  if(porta) {
  
  status_streaming( porta );
  
  }
  
  }

}

function tipo_estatistica( tipo ) {
	
  document.getElementById("tabela_data").style.display = "block";
  
  if(tipo == "3" || tipo == "4") {  
  document.getElementById("tabela_data").style.display = "none";  
  }  
  
}

function valida_opcoes_frequencia( frequencia ) {
	
document.getElementById("data").disabled = true;

for(var cont = 0; cont < document.agendamentos.dias.length; cont++) {
document.agendamentos.dias[cont].disabled = true;
}

if(frequencia == "1") {
document.getElementById("data").disabled = false;
}

if(frequencia == "3") {

for(var cont = 0; cont < document.agendamentos.dias.length; cont++) {
document.agendamentos.dias[cont].disabled = false;
}

}

}

// Função para mascarar campos
function txtBoxFormat(objeto, sMask, evtKeyPress) {
    var i, nCount, sValue, fldLen, mskLen,bolMask, sCod, nTecla;

if(document.all) { // Internet Explorer
    nTecla = evtKeyPress.keyCode;
} else if(document.layers) { // Nestcape
    nTecla = evtKeyPress.which;
} else {
    nTecla = evtKeyPress.which;
    if (nTecla == 8) {
        return true;
    }
}

    sValue = objeto.value;

    // Limpa todos os caracteres de formatação que
    // já estiverem no campo.
    sValue = sValue.toString().replace( "-", "" );
    sValue = sValue.toString().replace( "-", "" );
    sValue = sValue.toString().replace( ".", "" );
    sValue = sValue.toString().replace( ".", "" );
    sValue = sValue.toString().replace( "/", "" );
    sValue = sValue.toString().replace( "/", "" );
    sValue = sValue.toString().replace( ":", "" );
    sValue = sValue.toString().replace( ":", "" );
    sValue = sValue.toString().replace( "(", "" );
    sValue = sValue.toString().replace( "(", "" );
    sValue = sValue.toString().replace( ")", "" );
    sValue = sValue.toString().replace( ")", "" );
    sValue = sValue.toString().replace( " ", "" );
    sValue = sValue.toString().replace( " ", "" );
    fldLen = sValue.length;
    mskLen = sMask.length;

    i = 0;
    nCount = 0;
    sCod = "";
    mskLen = fldLen;

    while (i <= mskLen) {
      bolMask = ((sMask.charAt(i) == "-") || (sMask.charAt(i) == ".") || (sMask.charAt(i) == "/") || (sMask.charAt(i) == ":"))
      bolMask = bolMask || ((sMask.charAt(i) == "(") || (sMask.charAt(i) == ")") || (sMask.charAt(i) == " "))

      if (bolMask) {
        sCod += sMask.charAt(i);
        mskLen++; }
      else {
        sCod += sValue.charAt(nCount);
        nCount++;
      }

      i++;
    }

    objeto.value = sCod;

    if (nTecla != 8) { // backspace
      if (sMask.charAt(i-1) == "9") { // apenas números...
        return ((nTecla > 47) && (nTecla < 58)); }
      else { // qualquer caracter...
        return true;
      }
    }
    else {
      return true;
    }
}

// Função para bloquear acentos
function bloquear_acentos( texto ) {
	
	if(texto.value.match(['[-@!#$%¨&*+_´`^~;:?áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ|\?,./{}"<>()]'])) {
		alert( "Atenção! Nâo use acentos ou carácteres especiais." );
		texto.value = texto.value.substring( 0 , ( texto.value.length - 1 ) );
	}
}

// Função para contar a quantidade de caracteres digitados num campo
function contar_caracteres( campo, maximo ) {
	
	var total_digitado = document.getElementById(campo).value.length;	
	document.getElementById('total_caracteres').innerHTML = maximo - total_digitado;

}

// Função para calcular tamanho do iframe
function calcular_altura_iframe( iframe ) {
	
if (window.innerHeight){ 
   //navegadores baseados em mozilla 
   espaco_iframe = window.innerHeight - 130 
}else{ 
   if (document.body.clientHeight){ 
      	//Navegadores baseados em IExplorer, pois nao tenho innerheight 
      	espaco_iframe = document.body.clientHeight - 130 
   }else{ 
      	//outros navegadores 
      	espaco_iframe = 500 
   } 
}

document.getElementById( iframe ).height = espaco_iframe;

}

// Função para abrir a tela de log do sistema
function abrir_log_sistema() {
	
	window.parent.document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
	window.parent.document.getElementById('log-sistema-fundo').style.display = "block";
	window.parent.document.getElementById('log-sistema').style.display = "block";

}

// Função para abrir a tela de log do sistema
function fechar_log_sistema() {

	window.parent.document.getElementById('log-sistema-fundo').style.display = "none";
	window.parent.document.getElementById('log-sistema').style.display = "none";

}

// Função para bloquear teclacapslock
function bloquear_capslock(ev,campo) {

	var e = ev || window.event;
	codigo_tecla = e.keyCode?e.keyCode:e.which;
	tecla_shift = e.shiftKey?e.shiftKey:((codigo_tecla == 16)?true:false);
	if(((codigo_tecla >= 65 && codigo_tecla <= 90) && !tecla_shift) || ((codigo_tecla >= 97 && codigo_tecla <= 122) && tecla_shift)) {
		alert("Ooops!\n\nA tecla CapsLock esta ativada!\n\nVocê deve desativar esta tecla para escrever corretamente os dados neste campo.");
		document.getElementById(campo).value = "";
	}

}

function abreviar(str, size) {

    if(str.length >= size){
        shortText = str.substring(0, size);
		shortText = shortText+"...";
    } else {
		shortText = str;
	}
	
    return shortText;
} 

function s2time(seconds){
  
  	var sec_num = parseInt(seconds);
    var hours   = Math.floor(sec_num / 3600);
  	var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
  	var seconds = sec_num - (hours * 3600) - (minutes * 60);        
  	if (hours   < 10) {hours   = "0"+hours;}
  	if (minutes < 10) {minutes = "0"+minutes;}
  	if (seconds < 10) {seconds = "0"+seconds;}
  	var time    = hours+':'+minutes+':'+seconds;
  	return time;
  
}

function hide_show( local ) {
	
	if (document.getElementById(local).style.display=="block") {
		document.getElementById(local).style.display="none";
		setCookie(local, 'none', 7);
	} else {
		document.getElementById(local).style.display="block";
		setCookie(local, 'block', 7);
	}
	
}

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

function removeRow(id) {
  var tr = document.getElementById(id);
  if (tr) {
    if (tr.nodeName == 'TR') {
      var tbl = tr; // Look up the hierarchy for TABLE
      while (tbl != document && tbl.nodeName != 'TABLE') {
        tbl = tbl.parentNode;
      }

      if (tbl && tbl.nodeName == 'TABLE') {
        while (tr.hasChildNodes()) {
          tr.removeChild( tr.lastChild );
        }
      tr.parentNode.removeChild( tr );
      }
    }
  }
}

function youtube_parser(url) {
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = url.match(regExp);
    return (match&&match[7].length==11)? match[7] : false;
}