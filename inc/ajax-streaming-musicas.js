var _$_9e61=["","Erro! Tente novamente ou contate o suporte.","innerHTML","lista-pastas","getElementById","status_lista_pastas","<img src='http://","/img/ajax-loader.gif' />","display","style","block","GET","/funcoes-ajax/carregar_lista_pastas/","open","onreadystatechange","readyState","responseText",";","split","length","|","li","createElement","<img src='/admin/img/icones/img-icone-pasta.png' align='absmiddle' />&nbsp;<a href='javascript:carregar_musicas_pasta(\"","\",\"","\");'>","&nbsp;(",")</a><a href='javascript:remover_pasta(\"","\")' style='float:right;'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover' title='Remover' border='0' align='absmiddle' /></a><a href='javascript:renomear_pasta(\"","\")' style='float:right;padding-right:5px;'><img src='/admin/img/icones/img-icone-renomear.png' alt='Renomear' title='Renomear' border='0' align='absmiddle' /></a>","appendChild","none","log-sistema-fundo","log-sistema","status_lista_playlists","Nenhuma pasta encontrada.","send","lista-musicas-pasta","value","pasta_selecionada","msg_pasta_selecionada","log-sistema-conteudo","msg_pasta","/funcoes-ajax/carregar_musicas_pasta/","/","test","<img src='/img/icones/img-icone-bloqueado.png' width='16' height='16' border='0' align='absmiddle' title='Arquivo inv\xE1lido com caracteres especiais.\x0A\x0AInvalid file with special chars.\x0A\x0ANombre de archivo no v\xE1lido con caracteres especiales.' />&nbsp;[","]&nbsp;","<span style='float:right;'><a href='javascript:renomear_musica_ftp(\"","\");' title='Renomear ","'><img src='/admin/img/icones/img-icone-renomear.png' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:remover_musica_ftp(\"","\")'><img src='/admin/img/icones/img-icone-fechar.png' width='16' height='16' alt='Remover ","' title='Remover ","' border='0' align='absmiddle' /></a></span>","backgroundColor","#FFBFBF","<img src='/admin/img/icones/img-icone-arquivo-musica.png' border='0' align='absmiddle' />&nbsp;[","<span style='float:right;'><a href='javascript:play_musica(\"","\");' title='Play ","'><img src='/img/icones/img-icone-relay.png' width='16' height='16' border='0' style='padding-right:5px;' align='absmiddle' /></a><a href='javascript:renomear_musica_ftp(\"","Informe um nome para a nova pasta:\x0A(N\xE3o use caracteres especiais e acentos)","/funcoes-ajax/criar_pasta/","Aten\xE7\xE3o! Voc\xEA deve clicar na pasta que deseja renomear.","Informe o novo nome para a pasta:\x0A(N\xE3o use caracteres especiais e acentos)","/funcoes-ajax/renomear_pasta/","Deseja realmente remover esta pasta e todas as suas m\xFAsicas?","confirm","/funcoes-ajax/remover_pasta/","Aten\xE7\xE3o! Voc\xEA deve clicar na m\xFAsica que deseja renomear.","Informe o novo nome para a m\xFAsica:\x0A(N\xE3o use caracteres especiais e acentos)","replace","/funcoes-ajax/renomear_musica_ftp/","Aten\xE7\xE3o! Voc\xEA deve selecionar a m\xFAsica que deseja remover.","Deseja realmente remover esta m\xFAsica?","/funcoes-ajax/remover_musica_ftp/","Aten\xE7\xE3o! Voc\xEA deve clicar em uma pasta para onde ser\xE3o enviadas as m\xFAsicas para seleciona-la.\x0A\x0AAttention! You must click a folder where the song will be uploaded to select it.\x0A\x0A\xA1Atenci\xF3n! Debe hacer clic en una carpeta donde se enviar\xE1 la m\xFAsica para seleccionarlo.","width","height","/gerenciar-musicas-upload/","width=400,height=500,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=NO,top=",",left=","ouvintes","estatistica_uso_plano_ouvintes","/admin/img/spinner.gif' />","estatistica_uso_plano_ftp","/funcoes-ajax/estatistica_uso_plano/","/funcoes-ajax/play_musica/","href","Microsoft.XMLHTTP","Msxml2.XMLHTTP","Esse browser n\xE3o tem recursos para uso do Ajax"];function carregar_pastas(_0x18259){if(_0x18259== _$_9e61[0]){alert(_$_9e61[1])}else {document[_$_9e61[4]](_$_9e61[3])[_$_9e61[2]]= _$_9e61[0];document[_$_9e61[4]](_$_9e61[5])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[5])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[12]+ _0x18259,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];if(resultado){array_pastas= resultado[_$_9e61[18]](_$_9e61[17]);for(var _0x18291=0;_0x18291< array_pastas[_$_9e61[19]];_0x18291++){if(array_pastas[_0x18291]){dados_pasta= array_pastas[_0x18291][_$_9e61[18]](_$_9e61[20]);var _0x183A9=document[_$_9e61[22]](_$_9e61[21]);_0x183A9[_$_9e61[2]]= _$_9e61[23]+ _0x18259+ _$_9e61[24]+ dados_pasta[0]+ _$_9e61[25]+ dados_pasta[0]+ _$_9e61[26]+ dados_pasta[1]+ _$_9e61[27]+ _0x18259+ _$_9e61[24]+ dados_pasta[0]+ _$_9e61[28]+ _0x18259+ _$_9e61[24]+ dados_pasta[0]+ _$_9e61[29];document[_$_9e61[4]](_$_9e61[3])[_$_9e61[30]](_0x183A9);document[_$_9e61[4]](_$_9e61[5])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[31];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[31];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[31]}}}else {document[_$_9e61[4]](_$_9e61[34])[_$_9e61[2]]= _$_9e61[35]}}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}function carregar_musicas_pasta(_0x18259,_0x18221){if(_0x18259== _$_9e61[0]|| _0x18221== _$_9e61[0]){alert(_$_9e61[1])}else {document[_$_9e61[4]](_$_9e61[37])[_$_9e61[2]]= _$_9e61[0];document[_$_9e61[4]](_$_9e61[39])[_$_9e61[38]]= _0x18221;document[_$_9e61[4]](_$_9e61[40])[_$_9e61[2]]= _0x18221;document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[42])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[31];var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[43]+ _0x18259+ _$_9e61[44]+ _0x18221,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];if(resultado){array_musicas= resultado[_$_9e61[18]](_$_9e61[17]);for(var _0x18291=0;_0x18291< array_musicas[_$_9e61[19]];_0x18291++){if(array_musicas[_0x18291]){dados_musica= array_musicas[_0x18291][_$_9e61[18]](_$_9e61[20]);var _0x18339=document[_$_9e61[22]](_$_9e61[21]);var _0x18301=dados_musica[1];var _0x182C9=dados_musica[2];var _0x18371=dados_musica[0];if(/[^a-z0-9_\-\. ]/gi[_$_9e61[45]](_0x18301)){_0x18339[_$_9e61[2]]= _$_9e61[46]+ _0x182C9+ _$_9e61[47]+ _0x18301+ _$_9e61[48]+ _0x18259+ _$_9e61[24]+ _0x18371+ _$_9e61[49]+ _0x18301+ _$_9e61[50]+ _0x18259+ _$_9e61[24]+ _0x18371+ _$_9e61[51]+ _0x18301+ _$_9e61[52]+ _0x18301+ _$_9e61[53];_0x18339[_$_9e61[9]][_$_9e61[54]]= _$_9e61[55]}else {_0x18339[_$_9e61[2]]= _$_9e61[56]+ _0x182C9+ _$_9e61[47]+ _0x18301+ _$_9e61[57]+ _0x18259+ _$_9e61[24]+ _0x18371+ _$_9e61[58]+ _0x18301+ _$_9e61[59]+ _0x18259+ _$_9e61[24]+ _0x18371+ _$_9e61[49]+ _0x18301+ _$_9e61[50]+ _0x18259+ _$_9e61[24]+ _0x18371+ _$_9e61[51]+ _0x18301+ _$_9e61[52]+ _0x18301+ _$_9e61[53]};document[_$_9e61[4]](_$_9e61[37])[_$_9e61[30]](_0x18339)}}};document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[31];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[31]}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}function criar_pasta(_0x18259){var _0x18221=prompt(_$_9e61[60]);if(_0x18221!= _$_9e61[0]&& _0x18221!= null){document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[61]+ _0x18259+ _$_9e61[44]+ _0x18221,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= resultado}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}function renomear_pasta(_0x18259,_0x18221){if(_0x18221== _$_9e61[0]){alert(_$_9e61[62])}else {novo= prompt(_$_9e61[63]);if(novo!= _$_9e61[0]&& novo!= null){document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[64]+ _0x18259+ _$_9e61[44]+ _0x18221+ _$_9e61[44]+ novo,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= resultado}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}}function remover_pasta(_0x18259,_0x18221){if(window[_$_9e61[66]](_$_9e61[65])){document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[67]+ _0x18259+ _$_9e61[44]+ _0x18221,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= resultado}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}function renomear_musica_ftp(_0x18259,_0x18489,_0x184C1){if(_0x18489== _$_9e61[0]){alert(_$_9e61[68])}else {_0x184C1= prompt(_$_9e61[69]);if(_0x184C1!= _$_9e61[0]&& _0x184C1!= null){document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x18489=_0x18489[_$_9e61[70]](_$_9e61[44],_$_9e61[20]);var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[71]+ _0x18259+ _$_9e61[44]+ _0x18489+ _$_9e61[44]+ _0x184C1,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= resultado}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}}function remover_musica_ftp(_0x18259,_0x18489){if(_0x18489== _$_9e61[0]){alert(_$_9e61[72])}else {if(window[_$_9e61[66]](_$_9e61[73])){document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x18489=_0x18489[_$_9e61[70]](_$_9e61[44],_$_9e61[20]);var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[74]+ _0x18259+ _$_9e61[44]+ _0x18489,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= resultado}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}}function enviar_musicas(_0x18221){if(_0x18221== _$_9e61[0]){alert(_$_9e61[75])}else {LeftPosition= (screen[_$_9e61[76]])?(screen[_$_9e61[76]]- 400)/ 2:0;TopPosition= (screen[_$_9e61[77]])?(screen[_$_9e61[77]]- 500)/ 2:0;window[_$_9e61[13]](_$_9e61[78]+ _0x18221+ _$_9e61[0],_$_9e61[0],_$_9e61[79]+ TopPosition+ _$_9e61[80]+ LeftPosition+ _$_9e61[0])}}function estatistica_uso_plano(_0x18259,_0x183E1,_0x18419){if(_0x183E1== _$_9e61[81]){document[_$_9e61[4]](_$_9e61[82])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[83]}else {document[_$_9e61[4]](_$_9e61[84])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[83]};var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[85]+ _0x18259+ _$_9e61[44]+ _0x183E1+ _$_9e61[44]+ _0x18419,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];if(_0x183E1== _$_9e61[81]){document[_$_9e61[4]](_$_9e61[82])[_$_9e61[2]]= resultado}else {document[_$_9e61[4]](_$_9e61[84])[_$_9e61[2]]= resultado}}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}function play_musica(_0x18259,_0x18489){if(_0x18259!= _$_9e61[0]&& _0x18489!= null){document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= _$_9e61[6]+ get_host()+ _$_9e61[7];document[_$_9e61[4]](_$_9e61[32])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];document[_$_9e61[4]](_$_9e61[33])[_$_9e61[9]][_$_9e61[8]]= _$_9e61[10];var _0x181E9= new Ajax();_0x181E9[_$_9e61[13]](_$_9e61[11],_$_9e61[86]+ _0x18259+ _$_9e61[44]+ _0x18489,true);_0x181E9[_$_9e61[14]]= function(){if(_0x181E9[_$_9e61[15]]== 4){resultado= _0x181E9[_$_9e61[16]];document[_$_9e61[4]](_$_9e61[41])[_$_9e61[2]]= resultado}};_0x181E9[_$_9e61[36]](null);delete _0x181E9}}function get_host(){var _0x18451=location[_$_9e61[87]];_0x18451= _0x18451[_$_9e61[18]](_$_9e61[44]);return _0x18451[2]}function Ajax(){var _0x181B1;try{_0x181B1=  new ActiveXObject(_$_9e61[88])}catch(e){try{_0x181B1=  new ActiveXObject(_$_9e61[89])}catch(ex){try{_0x181B1=  new XMLHttpRequest()}catch(exc){alert(_$_9e61[90]);_0x181B1= null}}};return _0x181B1}