////////////////////////////////////////////////////////
/////////// Funções Gerenciamento Playlist /////////////
////////////////////////////////////////////////////////

// Função para criar uma nova playlist
function criar_playlist( porta ) {
  
  var playlist = prompt("Nome:\n(Não use caracteres especiais e acentos)");
	
  if(playlist != "" && playlist != null) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";	
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/criar_playlist/"+porta+"/"+playlist , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	resultado_partes = resultado.split("|");
	
	if(resultado_partes[0] == "ok") {

    window.open('/gerenciar-playlists/'+resultado_partes[1]+'','conteudo');
	
	} else {
	
	document.getElementById("log-sistema-conteudo").innerHTML = "<span class='texto_status_erro'>"+resultado_partes[1]+"</span>";
	document.getElementById("log-sistema-fundo").style.display = "block";
    document.getElementById("log-sistema").style.display = "block";	
	
	}
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as pastas
function carregar_pastas( porta ) {

  if(porta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Limpa a lista de playlist já carregadas
  document.getElementById("lista-pastas").innerHTML = "";
  
  document.getElementById("status_lista_pastas").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("status_lista_pastas").style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_pastas/"+porta , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_pastas = resultado.split(";");
	
	for(var cont = 0; cont < array_pastas.length; cont++) {	
	 
	if(array_pastas[cont]) {
	
	dados_pasta = array_pastas[cont].split("|");
	
	var nova_pasta = document.createElement("li");
	
	nova_pasta.innerHTML = "<img src='/admin/img/icones/img-icone-pasta.png' align='absmiddle' />&nbsp;<a href='javascript:carregar_musicas_pasta(\""+porta+"\",\""+dados_pasta[0]+"\");'>"+dados_pasta[0]+"&nbsp;("+dados_pasta[1]+")</a>";
  
    document.getElementById("lista-pastas").appendChild(nova_pasta);
	
	document.getElementById("status_lista_pastas").style.display = "none";
	
	}
	
	}
	
	}
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as músicas da pasta do FTP no gerenciamento de playlist
function carregar_musicas_pasta( porta,pasta ) {
	
  if(porta == "" || pasta == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Limpa a lista de músicas já carregadas
  document.getElementById("lista-musicas-pasta").innerHTML = "";
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  document.getElementById("msg_pasta").style.display = "none";
  
  if(document.getElementById("ordenar_musicas_pasta").checked) {
  var ordenar = "sim";
  }
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_musicas_pasta_playlists/"+porta+"/"+pasta+"/"+ordenar , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_musicas = resultado.split(";");
	
	for(var cont = 0; cont < array_musicas.length; cont++) {	
	 
	if(array_musicas[cont]) {
	
	dados_musica = array_musicas[cont].split("|");
	
	var nova_musica = document.createElement("li");
  
    nova_musica.innerHTML = "<input id='musicas_pasta' duracao='"+dados_musica[1]+"' duracao_segundos='"+dados_musica[2]+"' type='checkbox' value='"+pasta+"/"+dados_musica[0]+"' style='display:none' checked /><img src='/admin/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;<a href='javascript:adicionar_musica_playlist(\""+pasta+"/"+dados_musica[0]+"\",\""+dados_musica[0]+"\",\""+dados_musica[1]+"\",\""+dados_musica[2]+"\");'>["+dados_musica[1]+"] "+dados_musica[0]+"</a><a href='javascript:adicionar_vinheta(\""+pasta+"/"+dados_musica[0]+"\",\""+dados_musica[0]+"\",\""+dados_musica[1]+"\",\""+dados_musica[2]+"\");' title='Adicionar como Vinheta' style='float:right;'><img src='/admin/img/icones/img-icone-vinheta.png' width='16' height='16' alt='Adicionar como Vinheta' border='0' align='absmiddle' /></a>";
  
    document.getElementById("lista-musicas-pasta").appendChild(nova_musica);
	
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	
	}
	
	}
	
	} else {

	document.getElementById("msg_pasta").innerHTML = "A pasta selecionada não possui músicas. Você deve enviar as músicas usando FTP ou gerenciador de músicas.";
	document.getElementById("msg_pasta").style.display = "block";
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	
	}
  
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para carregar as músicas da playlist
function carregar_musicas_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  // Seleciona a playlist
  document.getElementById("playlist").value = playlist;
  
  // Limpa as músicas da última playlist selecionada
  limpar_lista_musicas('playlist');
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  document.getElementById("msg_playlist").style.display = "none";

  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/carregar_musicas_playlist/"+playlist , true);
  http.onreadystatechange = function() {
	
  if(http.readyState == 4) {
  
	resultado = http.responseText;
	
	if(resultado) {
	
	array_musicas = resultado.split(";");
	
	for(var cont = 0; cont < array_musicas.length; cont++) {	
	 
	if(array_musicas[cont]) {
	
	dados_musica = array_musicas[cont].split("|");
	
	var path = dados_musica[0];
	var musica = dados_musica[1];
	var duracao = dados_musica[2];
	var duracao_segundos = dados_musica[3];
	var tipo = dados_musica[4];
	
	document.getElementById("msg_playlist").style.display = "none";
  
  	var lista_musicas = document.getElementById("lista-musicas-playlist");
  
  	var total_musicas = 0;
  
  	for (var i = 0; i < lista_musicas.childNodes.length; i++) {
        if (lista_musicas.childNodes[i].nodeName == "LI") {
          total_musicas++;
        }
 	}
  
  	var novo_id = (total_musicas+1);
  
  	var nova_musica = document.createElement("li");
  
  	nova_musica.setAttribute("id",novo_id);
	nova_musica.setAttribute("class","drag");
	
	if(tipo == "musica") {
	
	nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|musica' style='display:none' checked /><img src='/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"<a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover/Remove' title='Remover' border='0' align='absmiddle' /></a>";
	
	} else if(tipo == "vinheta") {
	
	nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|vinheta' style='display:none' checked /><img src='/img/icones/img-icone-vinheta.png' border='0' align='absmiddle' />&nbsp;<span class='lista-musicas-playlist-vinheta'>["+duracao+"] "+path.replace("/", " » ")+"</span><a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover/Remove' border='0' align='absmiddle' /></a>";
	
	} else if(tipo == "comercial") {
	
	nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|comercial' style='display:none' checked /><img src='/img/icones/img-icone-vinheta.png' border='0' align='absmiddle' />&nbsp;<span class='lista-musicas-playlist-comercial'>["+duracao+"] "+path.replace("/", " » ")+"</span><a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover/Remove' border='0' align='absmiddle' /></a>";
	
	} else if(tipo == "intercalado") {
	
	nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|intercalado' style='display:none' checked /><img src='/img/icones/img-icone-musica-intercalado.png' border='0' align='absmiddle' />&nbsp;<span class='lista-musicas-playlist-intercalado'>["+duracao+"] "+path.replace("/", " » ")+"</span><a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover/Remove' border='0' align='absmiddle' /></a>";
	
	} else {
	
	nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|hc' style='display:none' checked /><img src='/img/icones/img-icone-hora-certa.png' border='0' align='absmiddle' />&nbsp;<span class='lista-musicas-playlist-hora-certa'>["+duracao+"] "+musica+"</span><a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover/Remove' border='0' align='absmiddle' /></a>";
	
	}  	
  
  	document.getElementById("lista-musicas-playlist").appendChild(nova_musica);
  
  	quantidade_musicas_playlist();
  
  	tempo_execucao_playlist( duracao_segundos, "adicionar" );
  
  	setListeners();
	
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	document.getElementById("msg_playlist_nova").style.display = "none";

	}
	
	}
	
	} else {
	
	document.getElementById("msg_playlist_nova").style.display = "block";
	document.getElementById("log-sistema-fundo").style.display = "none";
    document.getElementById("log-sistema").style.display = "none";
	
	}
	
  }
  
  }
  http.send(null);
  delete http;
  }
}

// Função para adicionar uma musica do FTP na playlist
function adicionar_musica_playlist( path,musica,duracao,duracao_segundos ) {
	
  if(path == "" && musica == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  var playlist = document.getElementById("playlist").value;
  
  document.getElementById("msg_playlist").style.display = "none";
  document.getElementById("msg_playlist_nova").style.display = "none";
  
  var lista_musicas = document.getElementById("lista-musicas-playlist");
  
  var total_musicas = 0;
  
  for (var i = 0; i < lista_musicas.childNodes.length; i++) {
        if (lista_musicas.childNodes[i].nodeName == "LI") {
          total_musicas++;
        }
  }
  
  var novo_id = (total_musicas+1);
  
  var nova_musica = document.createElement("li");
  
  nova_musica.setAttribute("id",novo_id);
  nova_musica.setAttribute("class","drag");
  
  nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|musica' style='display:none' checked /><img src='/admin/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;["+duracao+"] "+path.replace("/", " » ")+"<a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover' border='0' align='absmiddle' /></a>";
  
  document.getElementById("lista-musicas-playlist").appendChild(nova_musica);
  
  quantidade_musicas_playlist();
  
  tempo_execucao_playlist( duracao_segundos, "adicionar" );
  
  setListeners();
  
  }  
  
}

// Função para adicionar vinheta na playlist(intercalar uma música)
function adicionar_vinheta( path,musica,duracao,duracao_segundos ) {
  
  var frequencia = parseInt(prompt("Informe a cada quantas músicas será adicionada esta música/vinheta:\nExemplo: 5"));
  
  var lista_musicas = document.getElementById("lista-musicas-playlist");
	
  var total_musicas = 0;
  
  for (var i = 0; i < lista_musicas.childNodes.length; i++) {
    if (lista_musicas.childNodes[i].nodeName == "LI") {
      total_musicas++;
    }
  }
	
  var listafilhos = lista_musicas.getElementsByTagName("li");
	
  for(i=frequencia, x=1; i<=listafilhos.length; i+=frequencia+1, x++) {
		
    if(novo_id) {
      var novo_id = (novo_id+1);
    } else {
      var novo_id = (total_musicas+1);
    }
		
    var nova_musica = document.createElement("li");
		
  	nova_musica.setAttribute("id",novo_id);
	nova_musica.setAttribute("class","drag");
	
	nova_musica.innerHTML = "<input name='musicas_adicionadas[]' type='checkbox' value='"+path+"|"+musica+"|"+duracao+"|"+duracao_segundos+"|vinheta' style='display:none' checked /><img src='/admin/img/icones/img-icone-vinheta.png' border='0' align='absmiddle' />&nbsp;<span class='lista-musicas-playlist-vinheta'>["+duracao+"] "+path.replace("/", " » ")+"</span><a href='javascript:remover_musica(\""+novo_id+"\",\""+duracao_segundos+"\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover' border='0' align='absmiddle' /></a>";
		
    if (i == listafilhos.length) {
      lista_musicas.appendChild(nova_musica);
    } else {
      lista_musicas.insertBefore(nova_musica, listafilhos[i]);
    }
	
	quantidade_musicas_playlist();
  
	tempo_execucao_playlist( duracao_segundos, "adicionar" );
	
	setListeners();
	
  }
 
}

// Função para adicionar todas as musicas do FTP na playlist
function adicionar_tudo() {
	
  var playlist = document.getElementById("playlist").value;
  
  document.getElementById("msg_playlist_nova").style.display = "none";

  var lista_musicas_pasta = document.forms["gerenciador"].elements["musicas_pasta"];
  
  for (var i = 0; i < lista_musicas_pasta.length; i++) {
  
  var path = lista_musicas_pasta[i].value;
  
  var musica = path.split("/");
  
  var duracao = lista_musicas_pasta[i].getAttribute('duracao');
  var duracao_segundos = lista_musicas_pasta[i].getAttribute('duracao_segundos');
  
  adicionar_musica_playlist( path,musica[1],duracao,duracao_segundos );
  
  }
  
}

// Função para remover uma música de uma playlist
function remover_musica( id, duracao ) {
  
  // Remove a música da lista
  document.getElementById("lista-musicas-playlist").removeChild(document.getElementById(id));
  
  // Atualiza a quantidade de musicas da playlist
  quantidade_musicas_playlist();
  

  // Remove o tempo da musica
  tempo_execucao_playlist( duracao, "remover" );

}

// Função para remover uma playlist
function remover_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Error!\n\nPortuguês: Dados faltando, tente novamente ou contate o suporte.\n\nEnglish: Missing data try again or contact support.\n\nEspañol: Los datos que faltaban inténtelo de nuevo o contacte con Atención.");
  } else {
  
  if(window.confirm("Português: Deseja remover a playlist e todas as suas músicas?\n\nEnglish: Want to remove the playlist and all your songs?\n\nEspañol: ¿Quieres eliminar la lista de reproducción y todas sus canciones?")) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_playlist/"+playlist , true);
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

// Função para limpar a lista de músicas
function limpar_lista_musicas( local ) {

  if(local == "ftp") {
  
  document.getElementById("lista-musicas-pasta").innerHTML = "";
  document.getElementById("msg_pasta").style.display = "block";
  
  } else {
  
  document.getElementById("lista-musicas-playlist").innerHTML = "";
  document.getElementById("msg_playlist").style.display = "block";
    
  quantidade_musicas_playlist();
  
  document.getElementById("tempo").value = 0;
	
  document.getElementById("tempo_playlist").innerHTML = "00:00:00";
  
  }
  
}

// Função para contar a quantidade de músicas na playlist
function quantidade_musicas_playlist() {

  var lista_musicas = document.getElementById("lista-musicas-playlist");
  
  var total_musicas = 0;

  for (var i = 0; i < lista_musicas.childNodes.length; i++) {
        if (lista_musicas.childNodes[i].nodeName == "LI") {
          total_musicas++;
        }
  }
  
  document.getElementById("quantidade_musicas_playlist").innerHTML = total_musicas;
  
  if(total_musicas > 1000) {
  document.getElementById("quadro_quantidade_musicas_playlist").style.borderColor = "#FFCC00";
  } else {
  document.getElementById("quadro_quantidade_musicas_playlist").style.borderColor = "#CCCCCC";
  }

}

// Função para calcular o tempo de execução da playlist
function tempo_execucao_playlist( duracao, operacao ) {
	
	var tempo_atual = document.getElementById("tempo").value;
	
	if(operacao == "adicionar") {
	var novo_tempo = Number(tempo_atual)+Number(duracao);
	} else {
	var novo_tempo = Number(tempo_atual)-Number(duracao);
	}
	
	document.getElementById("tempo").value = novo_tempo;
	
	document.getElementById("tempo_playlist").innerHTML = s2time(novo_tempo);

}

// Função para iniciar transmissão de uma playlist pelo gerenciador de playlists
function iniciar_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Oops! Ocorreu um erro ao processar sua requisição!\n\nContate o suporte para maiores detalhes\n\nErro: Dados faltando.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/iniciar_playlist/"+playlist , true);
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

// Função para remover a configuração do Hora Certa
function remover_hora_certa_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Oops! Ocorreu um erro ao processar sua requisição!\n\nContate o suporte para maiores detalhes\n\nErro: Dados faltando.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_hora_certa_playlist/"+playlist , true);
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

// Função para remover a configuração de Vinhetas & Comerciais
function remover_vinhetas_comerciais_playlist( playlist ) {
	
  if(playlist == "") {
  alert("Oops! Ocorreu um erro ao processar sua requisição!\n\nContate o suporte para maiores detalhes\n\nErro: Dados faltando.");
  } else {
  
  document.getElementById('log-sistema-conteudo').innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById('log-sistema-fundo').style.display = "block";
  document.getElementById('log-sistema').style.display = "block";
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/remover_vinhetas_comerciais_playlist/"+playlist , true);
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

// Função para misturar as musicas da playlist
function shuffle(items)
{
    var cached = items.slice(0), temp, i = cached.length, rand;
    while(--i)
    {
        rand = Math.floor(i * Math.random());
        temp = cached[rand];
        cached[rand] = cached[i];
        cached[i] = temp;
    }
    return cached;
}
function misturar_musicas( local ) {

var list = document.getElementById(local);

var nodes = list.children, i = 0;
    nodes = Array.prototype.slice.call(nodes);
    nodes = shuffle(nodes);
    while(i < nodes.length)
    {
        list.appendChild(nodes[i]);
        ++i;
    }
	
}

// Função para salvar a playlist
function salvar_playlist() {
  
  var playlist = document.getElementById("playlist").value;
  
  if(playlist == "") {  
  alert("Ooops!\n\nVocê não selecionou uma playlist.\nYou did not select a playlist.\nNo ha seleccionado una lista de reproducción.");  
  } else {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";
  
  document.gerenciador.submit();
  }

}

// Função para duplicar(copiar) uma playlist
function duplicar_playlist( playlist ) {
  
  var playlist_nova = prompt("Nome/Name/Nombre:\n\n(Não use caracteres especiais e acentos)\n(Do not use special characters and accents)\n(No utilice caracteres especiales y acentos)");
	
  if(playlist_nova != "" && playlist_nova != null) {
  
  document.getElementById("log-sistema-conteudo").innerHTML = "<img src='http://"+get_host()+"/img/ajax-loader.gif' />";
  document.getElementById("log-sistema-fundo").style.display = "block";
  document.getElementById("log-sistema").style.display = "block";	
  
  var http = new Ajax();
  http.open("GET", "/funcoes-ajax/duplicar_playlist/"+playlist+"/"+playlist_nova+"" , true);
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

// Função Drag&Drop para organizar as músicas da playlist
var zxcMseX, zxcMseY;

function zxcMove(event, zxcobj){
    var tgt;
    if (!event) var event = window.event;
    if (event.target) tgt = event.target;
    else if (event.srcElement) tgt = event.srcElement;
    if (tgt.nodeType == 3) tgt = tgt.parentNode;
    if (tgt.tagName != 'A' && tgt.tagName != 'IMG')
    {
        var zxcels = zxcobj.parentNode.getElementsByTagName(zxcobj.tagName);

        zxcobj.ary = [];    
        for (var zxc0 = 0; zxc0 < zxcels.length; zxc0++)
        {
            zxcobj.ary.push(zxcels[zxc0]);
        }
    
        zxcMseDown(event, zxcobj);
    }
}

function zxcMseDown(event, obj)
{
    document.onmousemove = function(event)
    {
        zxcDrag(event);
    }
    document.onmouseup = function(event)
    {
        zxcMseUp(event);
    }
    document.onselectstart = function(event)
    {
        window.event.returnValue = false;
    }
    
    zxcObj = obj;
    zxcObj.style.zIndex = 1;
    
    zxcMse(event);
    zxcDragY = zxcMseY;
}

function zxcMseUp(event)
{
    zxcObj.style.zIndex = 0;
    
    document.onmousemove = null;
    document.onselectstart = null;
    
    zxcDragX = -1;
    zxcDragY = -1;
    
    zxcRePos();
}

function zxcDrag(event)
{
    zxcMse(event);
    zxcObj.style.top = ((zxcMseY - zxcDragY)) + 'px';
}

function zxcMse(event)
{
    if (!event)
        var event = window.event;

    if (document.all)
    {
        zxcMseX = event.clientX+zxcDocS()[0];
        zxcMseY = event.clientY+zxcDocS()[1];
    }
    else
    {
        zxcMseX = event.pageX;
        zxcMseY = event.pageY;
    }
}

function zxcDocS()
{
    var zxcsx, zxcsy;
    
    if (!document.body.scrollTop)
    {
        zxcsx = document.documentElement.scrollLeft;
        zxcsy = document.documentElement.scrollTop;
    }
    else
    {
        zxcsx = document.body.scrollLeft;
        zxcsy = document.body.scrollTop;
    }
    
    return [zxcsx,zxcsy];
}

function zxcRePos()
{
    if (zxcObj.parentNode)
    {
        var zxcpar = zxcObj.parentNode;
        var zxccloneary = [];
    
        for (var zxc0 = 0; zxc0 < zxcObj.ary.length; zxc0++)
        {
            zxccloneary.push([zxcObj.ary[zxc0].cloneNode(true), zxcObj.ary[zxc0].offsetTop]);
        }

        for (var zxc1 = 0; zxc1 < zxcObj.ary.length; zxc1++)
        {
            zxcpar.removeChild(zxcObj.ary[zxc1]);
        }
    
        zxccloneary = zxccloneary.sort(zxcSortPos);
    
        for (var zxc2 = 0; zxc2 < zxccloneary.length; zxc2++)
        {
            zxcpar.appendChild(zxccloneary[zxc2][0]);
            zxccloneary[zxc2][0].style.top = '0px';
        }
    
        setListeners();
    }
}

function zxcSortPos(zxca, zxcb)
{
    var zxcA = zxca[1];
    var zxcB = zxcb[1];
    
    if (zxcA < zxcB)
    {
        return -1;
    }
    
    if (zxcA > zxcB)
    {
        return 1;
    }
    
    return 0;
}

function setListeners()
{
    var item = document.getElementsByClassName("drag");
    
    for (var i = 0; i < item.length; i++)
    {
        if (item[i].addEventListener)
        {
            item[i].addEventListener ("mousedown", function (e) { zxcMove(e, this); }, false);
        }
        else if (item[i].attachEvent)
        {
            item[i].attachEvent ("onmousedown", function (e) { zxcMove(e, this); });
        }
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