// Funções Gerais
function get_host() {

var url = location.href;
url = url.split("/");

return url[2];

}

function abrir_janela( url,largura,altura ) {

window.open( url, "","width="+largura+",height="+altura+",toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO" );

}

function executar_acao_streaming_dj( codigo,acao ) {

	if(acao == "") {
		alert("Escolha a ação a ser executada!");
	} else if(acao == "ligar") {
		ligar_streaming( codigo );
	} else if(acao == "desligar") {
		desligar_streaming( codigo );
	} else if(acao == "kick") {
		kick_streaming( codigo );
	} else if(acao == "ligar-autodj") {
		carregar_playlists( codigo );
	} else if(acao == "pular-musica") {
		pular_musica( codigo );
	} else if(acao == "recarregar-playlist") {
		recarregar_playlist( codigo );
	} else if(acao == "desligar-autodj") {
		desligar_autodj( codigo );
	} else if(acao == "ouvintes-conectados") {
		window.location = "/dj/ouvintes-conectados";
	} else if(acao == "pedidos-musicais") {
		window.location = "/dj/pedidos";
	} else if(acao == "remover-pedido-musical") {
		remover_pedido_musical( codigo );
	}

	document.getElementById(codigo).value = "";

}