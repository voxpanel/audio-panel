<?php
// Como configurar seu modulo:
//
// Acesse admin do WHMCS e clique em Servidores no menu correspondente
// Cadastre/Edite o servidor desejado definindo um nome para ele no campo Name
// no campo Hostname voce colocar a URL do painel incluindo o http:// no inicio
// no campo IP Address voce deve colocar a chave API obtida no painel de controle
// Neste arquivo voce nao deve modificar nada para que nao ocorram erros
  
  function stmadvance_configoptions ()
  {
    $configarray = array ('Ouvintes' => array ('Type' => 'text', 'Size' => '10', 'Description' => '<br>(Numero maximo de ouvintes. Ex.: 100)'),
						  'Bitrate' => array ('Type' => 'dropdown', 'Options' => '24,32,48,64,128,256,320', 'Description' => '<br>(Verifique limite de seu plano)'),
						  'Espaco AutoDJ' => array ('Type' => 'text', 'Size' => '10', 'Description' => '<br>(Espaco para FTP do autodj em megabytes. Zero desativa o autodj)'),
						  'AAC+ RTMP' => array ('Type' => 'dropdown', 'Options' => 'sim,nao', 'Description' => '<br>(Verifique se esta ativado em seu plano)'),
						  'Idioma' => array ('Type' => 'dropdown', 'Options' => 'pt-br,en-us,es', 'Description' => '<br>(Idioma Painel - Portugues/English/Espanol)'),
						  'App Android' => array ('Type' => 'dropdown', 'Options' => 'sim,nao', 'Description' => '<br>(App Android)'),
						  'Encoder MP3' => array ('Type' => 'yesno', 'Description' => '(Ativar/desativar uso de MP3)'),
						  'Encoder AACPlus' => array ('Type' => 'yesno', 'Description' => '(Ativar/desativar uso de AACPlus)')
						 );
    return $configarray;
  }
  
  function stmadvance_adminlink ($params)
  {
	
    $code = '<input type="button" value="Acessar Painel" onclick="window.location = \''.$params['serverhostname'].'/admin\';">';
    return $code;
  }

  function stmadvance_createaccount ($params)
  {
    global $debug;
	
    $CustomFieldQuery = mysql_query ('SELECT	id FROM tblcustomfields WHERE fieldname=\'Porta\' AND relid=' . $params['packageid']);
    if (mysql_num_rows ($CustomFieldQuery) == 0)
    {
      $return = 'O campo adicional "Porta" não existe.<br>Crie um campo adicional com o nome "Porta" em Products/Services';
      return $return;
    }

    $CustomFieldID = _mysql_result ($CustomFieldQuery, 0, 'id');
    $PortBaseQuery = mysql_query ('' . 'SELECT	value
										FROM	tblcustomfieldsvalues
										WHERE	fieldid=' . $CustomFieldID . '
										AND		relid=' . $params['accountid']);
    echo mysql_error ();
    if (mysql_num_rows ($PortBaseQuery) == 0)
    {
      mysql_query ('' . 'INSERT INTO tblcustomfieldsvalues (fieldid, relid, value)
							VALUES(' . $CustomFieldID . ', ' . $params['accountid'] . ', \'\')');
    }

    $PortBase = _mysql_result ($PortBaseQuery, 0, 'value');
    if (!empty ($PortBase))
    {
      $return = 'Este streaming já esta criado.';
      return $return;
    }
	
    $query3 = 'SELECT * FROM tblhostingconfigoptions WHERE relid=\'' . $params['accountid'] . '\'';
    $result3 = mysql_query ($query3);
    while ($data3 = mysql_fetch_array ($result3))
    {
      $optionid = $data3['optionid'];
      $configid = $data3['configid'];
      $query2 = '' . 'SELECT * FROM tblproductconfigoptions WHERE id=\'' . $configid . '\'';
      $result2 = mysql_query ($query2);
      $data2 = mysql_fetch_array ($result2);
      $optionname = $data2['optionname'];
      $query2 = '' . 'SELECT * FROM tblproductconfigoptionssub WHERE id=\'' . $optionid . '\'';
      $result2 = mysql_query ($query2);
      $data2 = mysql_fetch_array ($result2);
      $optionvalue = $data2['optionname'];
      $optionvalue = trim ($optionvalue);

      if ($optionname == 'Ouvintes') {
        $params['configoption1'] = $optionvalue;
        continue;
      } else {
	  	if ($optionname == 'Bitrate') {
        $params['configoption2'] = $optionvalue;
        continue;
      	} else {
		  if ($optionname == 'Espaço AutoDJ') {
          $params['configoption3'] = $optionvalue;
          continue;
      	  } else {
		    if ($optionname == 'AAC+ RTMP') {
      	    $params['configoption4'] = $optionvalue;
        	continue;
      	    } else {
			  if ($optionname == 'Idioma') {
        	  $params['configoption5'] = $optionvalue;
	          continue;
      	      } else {
			    if ($optionname == 'App Android') {
        		$params['configoption6'] = $optionvalue;
        		continue;
				} else {
				if ($optionname == 'Encoder MP3') {
        		$params['configoption7'] = $optionvalue;
        		continue;
				} else {
				if ($optionname == 'Encoder AACPlus') {
        		$params['configoption8'] = $optionvalue;
        		continue;
				}
			    }
				}
			  }
			}
		  }
		}
        continue;
      }
    }

    $api['acao'] = 'cadastrar';
    $api['ouvintes'] = $params['configoption1'];
    $api['bitrate'] = $params['configoption2'];
    $api['espaco'] = $params['configoption3'];
    $api['senha'] = substr(md5("acegikmoqsuxywz".time()),0,12);
	$api['aacplus'] = $params['configoption4'];
	$api['idioma'] = $params['configoption5'];
	$api['app_android'] = $params['configoption6'];
	$api['encoder_mp3'] = $params['configoption7'];
	$api['encoder_aacplus'] = $params['configoption8'];
	
    $response = api ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
	  
	  list ($ip, $porta) = explode (':', $response['returned']);

      mysql_query ('UPDATE tblhosting 	SET	username=\'' . $porta . '\',
												password=\'' . encrypt ($api['senha']). '\',
												domain=\'' . $ip . ':' . $porta . '\',
												dedicatedip=\'' . $ip . '\'
											WHERE id=\'' . $params['accountid'] . '\'');
	  
	  $dados_customfield = mysql_fetch_array(mysql_query('SELECT id FROM tblcustomfields WHERE fieldname=\'Porta\' AND relid=' . $params['packageid']));
	  
      mysql_query ('UPDATE tblcustomfieldsvalues 	SET	value=\'' . $porta . '\'
														WHERE fieldid=\'' . $dados_customfield['id'] . '\' AND relid=\'' . $params['accountid'] . '\'');
      return 'success';
    }

    return $response['error'];
  }

  function stmadvance_terminateaccount ($params)
  {
    global $debug;
	
    $PortBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'Porta\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($PortBaseQuery) == 0)
    {
      $return = 'O streaming não esta criado.';
      return $return;
    }

    $PortBase = _mysql_result ($PortBaseQuery, 0, 'value');
    $api['acao'] = 'remover';
    $api['porta'] = $PortBase;
	
    $response = api ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
      
	  $dados_customfield = mysql_fetch_array(mysql_query('SELECT id FROM tblcustomfields WHERE fieldname=\'Porta\' AND relid=' . $params['packageid']));
	  
	  mysql_query ('DELETE FROM tblcustomfieldsvalues WHERE fieldid=\'' . $dados_customfield['id'] . '\' AND relid=\'' . $params['accountid'] . '\'');
	  
	  mysql_query ('UPDATE tblhosting 	SET	username=\'\',
												password=\'\',
												domain=\'\',
												dedicatedip=\'\'
											WHERE id=\'' . $params['accountid'] . '\'');
											
      return 'success';
    }

    return $response['error'];
  }

  function stmadvance_suspendaccount ($params)
  {
    global $debug;
	
    $PortBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'Porta\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($PortBaseQuery) == 0)
    {
      $return = 'O streaming não esta cadastrado.';
      return $return;
    }

    $PortBase = _mysql_result ($PortBaseQuery, 0, 'value');
    $api['acao'] = 'bloquear';
    $api['porta'] = $PortBase;

    $response = api ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
      return 'success';
    }

    return $response['error'];
  }

  function stmadvance_unsuspendaccount ($params)
  {
    global $debug;
	
    $PortBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'Porta\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($PortBaseQuery) == 0)
    {
      $return = 'O streaming não esta criado, por favor clique no botão "Create" primeiro.';
      return $return;
    }

    $PortBase = _mysql_result ($PortBaseQuery, 0, 'value');
    $api['acao'] = 'desbloquear';
    $api['porta'] = $PortBase;
	
    $response = api ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
      return 'success';
    }

    return $response['error'];
  }

  function api ($serverhostname,$serverip,$api)
  {
	
    $requisicao = '';
    foreach ($api as $option => $setting)
    {
      if (is_array ($setting))
      {
        $setting = serialize ($setting);
      }

      $requisicao .= $setting."/";
    }
	
	$url_requisicao = "".$serverhostname."/admin/api/".$serverip."/".$requisicao."";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url_requisicao);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Modulo Streaming WHMCS 2.4 ('.$_SERVER['HTTP_HOST'].')');
	$resultado = curl_exec($ch);
	curl_close($ch);

	if($resultado === false) {
      return array ('command' => 'failed', 'error' => 'Não foi possível se conectar ao painel de controle. Debug: '.$url_requisicao.'');
	} else {

    list ($status, $porta, $msg) = explode ('|', $resultado);
	
    if ($status == '0')
    {
      return array ('command' => 'failed', 'error' => ''.$msg.'');
    }

    return array ('command' => 'success', 'returned' => ''.$porta.'');
	
	}
  }
  
  function _mysql_result($result, $iRow, $field = 0)
{
    if(!mysql_data_seek($result, $iRow))
        return false;
    if(!($row = mysql_fetch_array($result)))
        return false;
    if(!array_key_exists($field, $row))
        return false;
    return $row[$field];
}
?>