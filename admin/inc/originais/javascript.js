// Funções Gerais
function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

}

function abrir_janela( url,largura,altura ) {

window.open( url, "","width="+largura+",height="+altura+",toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO" );

}

// VELHO
function executar_acao_streaming_admin( codigo,acao ) {
	
	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "ligar") {
		ligar_streaming( codigo );
	} else if(acao == "desligar") {
		desligar_streaming( codigo );
	} else if(acao == "configurar") {
		window.location = "/admin/admin-configurar-streaming/"+codigo+"";
	} else if(acao == "player") {
		window.location = "/admin/admin-gerenciar-players/"+codigo+"";
	} else if(acao == "ouvintes-conectados") {
		abrir_janela( "/ouvintes-conectados/"+codigo+"",720,600 );
	} else if(acao == "kick") {
		kick_streaming( codigo );
	} else if(acao == "ativar-protecao") {
		
		if(window.confirm("Atenção! Ative esta proteção somente se esta porta estiver sofrendo ataques.\n\nDeseja realmente ativar a proteção para este streaming?")) {
			ativar_desativar_protecao( codigo );
		} else {
			return false;
		}
	} else if(acao == "sincronizar-aacplus") {
		sincronizar_aacplus( codigo );	
	} else if(acao == "sincronizar-playlists") {
		sincronizar_playlists( codigo );
	} else if(acao == "acessar-painel-streaming") {
	    acessar_painel_streaming( codigo );
	} else if(acao == "ligar-autodj") {
		carregar_playlists( codigo );
	} else if(acao == "pular-musica") {
		pular_musica( codigo );
	} else if(acao == "recarregar-playlist") {
		recarregar_playlist( codigo );
	} else if(acao == "desligar-autodj") {
		desligar_autodj( codigo );
	} else if(acao == "bloquear") {
		bloquear_streaming( codigo );
	} else if(acao == "desbloquear") {
		desbloquear_streaming( codigo );
	} else if(acao == "enviar-email") {
		enviar_email( codigo );
	} else if(acao == "logs") {
		window.location = "/admin/admin-streaming-logs/"+codigo+"";
	} else if(acao == "alterar-senha") {
		alterar_senha_streaming( codigo, "dj" );
	} else if(acao == "alterar-senha-admin") {
		alterar_senha_streaming( codigo, "shoutcast" );
	} else if(acao == "remover") {

		if(window.confirm("Deseja realmente remover este streaming?")) {
			remover_streaming( codigo );
		} else {
			return false;
		}

	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";

}

function selecionar_streaming_gerenciamento( porta, painel ) {
	
	if(painel == "admin") {
	window.location = "/admin/admin/"+porta+"";
	} else {
	window.location = "/admin/revenda/"+porta+"";
	}

}

function selecionar_subrevenda_gerenciamento( subrevenda ) {
	
	window.location = "/admin/revenda/subrevenda/"+subrevenda+"";

}

function executar_acao_streaming_revenda( codigo,acao ) {
	
	// Streaming
	
	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "streaming-informacoes") {
		abrir_log_sistema();
		window.open("/admin/revenda-streaming-informacoes/"+codigo+"","conteudo");
	} else if(acao == "streaming-ligar") {
		ligar_streaming( codigo );
	} else if(acao == "streaming-desligar") {
		desligar_streaming( codigo );
	} else if(acao == "streaming-players") {
		abrir_log_sistema();
		carregar_players(codigo);
	} else if(acao == "streaming-ouvintes-conectados") {
		abrir_log_sistema();
		window.open("/ouvintes-conectados/"+codigo+"","conteudo");
	} else if(acao == "streaming-kick") {
		kick_streaming( codigo );
	} else if(acao == "streaming-ativar-protecao") {
		
		if(window.confirm("Pt-BR: Continuar?\nEn-US: Continue?\nES: Continuar?")) {
			ativar_desativar_protecao( codigo );
		} else {
			return false;
		}
	} else if(acao == "streaming-logs") {
		abrir_log_sistema();
		window.open("/admin/revenda-streaming-logs/"+codigo+"","conteudo");
	} else if(acao == "streaming-logs-servidor") {
		abrir_log_sistema();
		window.open("/admin/revenda-logs-shoutcast/"+codigo+"","conteudo");
		
	// AutoDJ
	
	} else if(acao == "autodj-ligar") {
		carregar_playlists( codigo );
	} else if(acao == "autodj-pular-musica") {
		pular_musica( codigo );
	} else if(acao == "autodj-recarregar-playlist") {
		recarregar_playlist( codigo );
	} else if(acao == "autodj-desligar") {
		desligar_autodj( codigo );
	} else if(acao == "autodj-logs-servidor") {
		abrir_log_sistema();
		window.open("/admin/revenda-logs-autodj/"+codigo+"","conteudo");
		
	// Solução de Problemas
	
	} else if(acao == "solucao-problemas-sincronizar-aacplus") {
		sincronizar_aacplus( codigo );	
	} else if(acao == "solucao-problemas-sincronizar-playlists") {
		sincronizar_playlists( codigo );
	} else if(acao == "solucao-problemas-player-facebook") {
		atualizar_cache_player_facebook( codigo );
	} else if(acao == "solucao-problemas-encoder") {
		configurar_encoder( codigo );
	} else if(acao == "solucao-problemas-diagnosticar-autodj") {
		diagnosticar_autodj( codigo );
	
	// Administração
	} else if(acao == "admin-acessar-painel-streaming") {
		acessar_painel_streaming_revenda( codigo );
	} else if(acao == "admin-configurar") {
		abrir_log_sistema();
		window.open("/admin/revenda-configurar-streaming/"+codigo+"","conteudo");
	} else if(acao == "admin-mover-streaming") {
		
		var id = prompt('Pt-BR: Informe o ID da Sub Revenda destino, ex: abcdef123\n\nEn-US: Enter the Sub Reseller destination ID, example: abcdef123\n\nES: Introduzca el Sub Reseller ID de destino, por ejemplo: abcdef123');
		
		if(id) {

		if(window.confirm("Pt-BR: Continua?\nEn-US: Continue?\nES: Continuar?")) {
			mover_streaming_revenda_subrevenda( codigo,id );
		} else {
			return false;
		}
		
		} else {
			return false;
		}
	} else if(acao == "admin-bloquear") {
		bloquear_streaming( codigo );
	} else if(acao == "admin-desbloquear") {
		desbloquear_streaming( codigo );
	} else if(acao == "admin-enviar-email") {
		abrir_log_sistema();
		window.open("/admin/revenda-enviar-email/"+codigo+"","conteudo");
	} else if(acao == "admin-remover") {

		if(window.confirm("Pt-BR: Continuar?\nEn-US: Continue?\nES: Continuar?")) {
			remover_streaming( codigo );
		} else {
			return false;
		}

	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";

}

function executar_acao_revenda( codigo,acao ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "bloquear") {
		bloquear_revenda( codigo );
	} else if(acao == "desbloquear") {
		desbloquear_revenda ( codigo );
	} else if(acao == "configurar") {
		window.location = "/admin/admin-configurar-revenda/"+codigo+"";
	} else if(acao == "listar-streamings") {
		window.location = "/admin/admin-streamings/resultado-revenda/"+codigo+"";
	} else if(acao == "alterar-servidor") {
		window.location = "/admin/admin-alterar-servidor-streaming-revenda/"+codigo+"";
	} else if(acao == "alterar-revenda") {
		window.location = "/admin/admin-alterar-revenda-streaming/"+codigo+"";
	} else if(acao == "exportar-lista-streamings") {
		window.location = "/admin/admin-exportar-streamings-revenda/"+codigo+"";
	} else if(acao == "alterar-senha") {
		alterar_senha_revenda( codigo );
	} else if(acao == "remover") {

		if(window.confirm("Deseja realmente remover esta revenda?")) {
			remover_revenda( codigo );
		} else {
			return false;
		}

	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";
}

function executar_acao_subrevenda( codigo,acao ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "informacoes") {
		abrir_log_sistema();
		window.open("/admin/revenda-subrevenda-informacoes/"+codigo+"","conteudo");
	} else if(acao == "bloquear") {
		bloquear_subrevenda ( codigo );
	} else if(acao == "desbloquear") {
		desbloquear_subrevenda ( codigo );
	} else if(acao == "configurar") {
		abrir_log_sistema();
		window.open("/admin/revenda-subrevenda-configurar/"+codigo+"","conteudo");
	} else if(acao == "listar-streamings") {
		abrir_log_sistema();
		window.open("/admin/revenda-subrevenda-streamings/"+codigo+"","conteudo");
	} else if(acao == "remover") {

		if(window.confirm("Pt-BR: Continuar?\nEn-US: Continue?\nES: Continuar?")) {
			remover_subrevenda ( codigo );
		} else {
			return false;
		}

	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";
}

function executar_acao_streaming_subrevenda( codigo,acao ) {
	
	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "informacoes") {
		abrir_log_sistema();
		window.open("/admin/revenda-subrevenda-streaming-informacoes/"+codigo+"","conteudo");
	} else if(acao == "mover-streaming") {
		
		if(window.confirm("Pt-BR: Continuar?\nEn-US: Continue?\nES: Continuar?")) {
			mover_streaming_subrevenda_revenda( codigo );
		} else {
			return false;
		}
		
	} else if(acao == "mover-streaming-subrevenda") {
		
		var id = prompt('Pt-BR: Informe o ID da Sub Revenda destino, ex: abcdef123\n\nEn-US: Enter the Sub Reseller destination ID, example: abcdef123\n\nES: Introduzca el Sub Reseller ID de destino, por ejemplo: abcdef123');
		
		if(id) {

		if(window.confirm("Pt-BR: Continua?\nEn-US: Continue?\nES: Continuar?")) {
			mover_streaming_subrevenda_subrevenda( codigo,id );
		} else {
			return false;
		}
		
		} else {
			return false;
		}
		
	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";

}

function executar_acao_servidor( codigo,acao ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "ligar") {
		ligar_streamings_servidor( codigo );
	} else if(acao == "ligar-autodjs") {
		ligar_autodjs_servidor( codigo );
	} else if(acao == "ativar-manutencao") {
		var mensagem = prompt('Informe a mensagem de manutenção a ser exibida no painel de streaming:','Seu servidor esta offline neste momento. Nossa equipe já esta trabalhando na solução do problema, por favor aguarde.');
		manutencao_servidor( codigo, "ativar", mensagem  );
	} else if(acao == "desativar-manutencao") {
		manutencao_servidor( codigo, "desativar", "" );
	} else if(acao == "desligar") {
		
		if(window.confirm("Deseja realmente desligar todos os streamings deste servidor?")) {
			desligar_streamings_servidor( codigo );
		}		
		
	} else if(acao == "listar-streamings") {
		window.location = "/admin/admin-streamings/resultado-servidor/"+codigo+"";
	} else if(acao == "sincronizar-aacplus") {
		sincronizar_aacplus_servidor( codigo );
	} else if(acao == "sincronizar-playlists") {
		sincronizar_playlists_servidor( codigo );
	} else if(acao == "exportar-lista-streamings") {
		window.location = "/admin/admin-exportar-streamings-servidor/"+codigo+"";
	} else if(acao == "alterar-servidor") {
		window.location = "/admin/admin-alterar-servidor-streaming/"+codigo+"";
	} else if(acao == "configurar") {
		window.location = "/admin/admin-configurar-servidor/"+codigo+"";
	} else if(acao == "firewall") {
		window.location = "/admin/admin-firewall/"+codigo+"";
	} else if(acao == "remover") {

		if(window.confirm("Deseja realmente remover este servidor?")) {
			window.location = "/admin/admin-remover-servidor/"+codigo+"";
		}

	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";
}


function executar_acao_diversa( codigo,acao ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "remover-dica-rapida") {
		remover_dica_rapida( codigo );
	} else if(acao == "remover-dica-rapida") {
		remover_dica_rapida( codigo );
	} else if(acao == "alterar-status-aviso") {
		alterar_status_aviso( codigo );
	} else if(acao == "remover-aviso") {
		remover_aviso( codigo );
	} else if(acao == "editar-tutorial") {
		window.location = "/admin/admin-editar-tutorial/"+codigo+"";
	} else if(acao == "remover-tutorial") {
		remover_tutorial( codigo );
	} else if(acao == "app-android-detalhes") {
		window.location = "/admin/admin-app-detalhes/"+codigo+"";
	} else if(acao == "app-android-remover") {
		
		if(window.confirm("Pt-BR: Continuar?\nEn-US: Continue?\nES: Continuar?")) {
			remover_app_android( codigo );
		} else {
			return false;
		}
	} else if(acao == "desbloquear-ip-login") {
		desbloquear_ip_login( codigo );
	} else {
		abrir_janela( acao,720,500 );
	}

	document.getElementById(codigo).value = "";
}

function executar_acao_firewall( codigo,acao,servidor ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else {
		remover_regra_firewall( servidor,codigo );	
	}

	document.getElementById(codigo).value = "";
}

function mover_musica(listID, direction) {

	var listbox = document.getElementById(listID);
	var selIndex = listbox.selectedIndex;

	if(-1 == selIndex) {
		alert("Por favor selecione uma música!");
		return;
	}

	var increment = -1;
	if(direction == 'up')
		increment = -1;
	else
		increment = 1;

	if((selIndex + increment) < 0 ||
		(selIndex + increment) > (listbox.options.length-1)) {
		return;
	}

	var selValue = listbox.options[selIndex].value;
	var selText = listbox.options[selIndex].text;
	listbox.options[selIndex].value = listbox.options[selIndex + increment].value
	listbox.options[selIndex].text = listbox.options[selIndex + increment].text

	listbox.options[selIndex + increment].value = selValue;
	listbox.options[selIndex + increment].text = selText;

	listbox.selectedIndex = selIndex + increment;
}

function selecionar_tudo(campo) {

	var tamanho = document.getElementById( campo ).length;

	for(var i = 0; i < tamanho; i++) {
		document.getElementById( campo ).options[i].selected=true;
	}

}

function remover_tudo(campo) {

	var tamanho = document.getElementById( campo ).length;

	for(var i = 0; i < tamanho; i++) {
		document.getElementById(campo ).remove(document.getElementById( campo ).options[i]);
	}

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

function checar_status_streamings_busca_avancada( streamings ) {
  
  array_streamings = streamings.split("|");
  
  for(var cont = 0; cont < array_streamings.length; cont++) {
  
  var porta = array_streamings[cont];
  
  if(porta) {
  
  status_streaming_busca_avancada( porta );
  
  }
  
  }

}

function checar_status_streamings_subrevenda( streamings ) {
  
  array_streamings = streamings.split("|");
  
  for(var cont = 0; cont < array_streamings.length; cont++) {
  
  var porta = array_streamings[cont];
  
  if(porta) {
  
  status_streaming_subrevenda( porta );
  
  }
  
  }

}

// Função para carregar a configuração do plano de streaming/revenda pré-definido
function configuracao_plano( configuracoes,tipo ) {
  
  if(configuracoes) {
  
  array_configuracoes = configuracoes.split("|");
  
  if(tipo == "streaming") {
	  
  document.getElementById("ouvintes").value = array_configuracoes[0];
  document.getElementById("bitrate").value = array_configuracoes[1];
  document.getElementById("espaco").value = array_configuracoes[2];
  
  } else {
  
  document.getElementById("subrevendas").value = array_configuracoes[0];
  document.getElementById("streamings").value = array_configuracoes[1];
  document.getElementById("ouvintes").value = array_configuracoes[2];
  document.getElementById("bitrate").value = array_configuracoes[3];
  document.getElementById("espaco").value = array_configuracoes[4];
  
  }
  
  }
  
}

// Função para carregar a configuração do cliente(revenda)
function configuracao_revenda( configuracoes ) {
  
  if(configuracoes) {
  
  array_configuracoes = configuracoes.split("|");
  
  document.getElementById("nome").value = array_configuracoes[0];
  document.getElementById("email").value = array_configuracoes[1];
  document.getElementById("senha").value = array_configuracoes[2];
  
  }
  
}

function tipo_estatistica( tipo ) {
	
  document.getElementById("tabela_data").style.display = "block";
  
  if(tipo == "3" || tipo == "4") {  
  document.getElementById("tabela_data").style.display = "none";  
  }  
  
}

function adicionar_musica(ctrlSource, ctrlTarget) {
	
  var Source = document.getElementById(ctrlSource);
  var Target = document.getElementById(ctrlTarget);
  
  if (Target.selectedIndex >= 0) {
	var curOption = Target.options[Target.selectedIndex+1]; 
  } else {
	var curOption = Target.options[Target.length+1]; 
  }
  
  for(var i = 0; i < Source.length; i++) {
	  
    if(Source.options[i].selected === true) {
		
    var newOption = document.createElement('option');
    newOption.text = Source.options[i].text;
    newOption.value = Source.options[i].value;

    try {
      Target.add(newOption, curOption); // standards compliant; doesn't work in IE
    }
    catch(ex) {
      Target.add(newOption, Target.selectedIndex); // IE only
    }
	
	}
  }
  
}

function buscar_streaming(porta,painel) {

window.location = "/admin/"+painel+"-streamings/resultado/"+porta;

}

function buscar_revenda(chave) {

window.location = "/admin/admin-revendas/resultado/"+chave;

}

function buscar_servidor(chave) {

window.location = "/admin/admin-servidores/resultado/"+chave;

}

function buscar_app(porta) {

window.location = "/admin/admin-apps/resultado/"+porta;

}

function selecionar_servidor(codigo_servidor) {
	
for(var x=0; x < document.frm.servidor_novo.length; x++) {

	for(var y=0; y < document.frm.servidor_novo[x].length; y++) {

		if(document.frm.servidor_novo[x].options[y].value == codigo_servidor) {
			document.frm.servidor_novo[x].options[y].selected = true;
		}
	}
}

}

function selecionar_revenda(codigo_revenda) {
	
for(var x=0; x < document.frm.revenda_nova.length; x++) {

	for(var y=0; y < document.frm.revenda_nova[x].length; y++) {

		if(document.frm.revenda_nova[x].options[y].value == codigo_revenda) {
			document.frm.revenda_nova[x].options[y].selected = true;
		}
	}
}

}

function configurar_aacplus_streaming( opcao, servidor_aacplus ) {
	
if(opcao == "sim") {

document.getElementById("servidor_aacplus").value = servidor_aacplus;
document.getElementById("encoder").value = "aacp";

} else {

document.getElementById("encoder").value = "mp3";
document.getElementById("servidor_aacplus").value = "0";
	
}

}

function configurar_aacplus_revenda( opcao ) {
	
if(opcao == "sim") {

document.getElementById("encoder").value = "aacp";

} else {

document.getElementById("encoder").value = "mp3";
	
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
	
    var NaoPode = new RegExp( /\W/gi );
    var resultado = NaoPode.exec( texto.value );
    if ( resultado ) {
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

function getRandomNum(lbound, ubound) {
return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
}
function getRandomChar() {
var numberChars = "0123456789";
var lowerChars = "abcdefghijklmnopqrstuvwxyz";
var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//var otherChars = "!@#$%&*()-_=+[{]}\\|;:\<.>/?";
var charSet;

charSet += numberChars;
charSet += lowerChars;
charSet += upperChars;
//charSet += otherChars;
return charSet.charAt(getRandomNum(0, charSet.length));
}

function gerar_senha(campo) {
var rc = "";
var length = 10;
if (length > 0)
rc = rc + getRandomChar();
for (var idx = 1; idx < length; ++idx) {
rc = rc + getRandomChar();
}
document.getElementById(campo).value = rc;
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