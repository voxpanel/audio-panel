<?php
// Como configurar seu modulo:
//
// Acesse admin do WHMCS e clique em Servidores no menu correspondente
// Cadastre/Edite o servidor desejado definindo um nome para ele no campo Name
// no campo Hostname voce colocar a URL do painel incluindo o http:// no inicio
// no campo IP Address voce deve colocar a chave API obtida no painel de controle
// Neste arquivo voce nao deve modificar nada para que nao ocorram erros
  
  function subrevendastm_configoptions ()
  {
    $configarray = array ('Streamings' =>	array ('Type' => 'text', 'Size' => '10', 'Description' => '<br>(Limite de streamings/contas. Ex.: 100)'),
						  'Ouvintes' =>	array ('Type' => 'text', 'Size' => '10', 'Description' => '<br>(Limite de ouvintes. Ex.: 1000)'),
						  'Bitrate' => array ('Type' => 'dropdown', 'Options' => '24,32,48,64,128,256,320', 'Description' => '<br>(Verifique limite de seu plano)'),
						  'Espaco AutoDJ' => array ('Type' => 'text', 'Size' => '10', 'Description' => '<br>(Espaco para FTP do autodj, valor em megabytes. Ex.: 1000)'),
						  'AAC+ RTMP' => array ('Type' => 'dropdown', 'Options' => 'sim,nao', 'Description' => '<br>(Verifique se esta ativado em seu plano)'),
						  'Idioma' => array ('Type' => 'dropdown', 'Options' => 'pt-br,en-us,es', 'Description' => '<br>(Idioma Painel - Portugues/English/Espanol)'),
						  'Subrevendas' =>	array ('Type' => 'text', 'Size' => '10', 'Description' => '<br>(Limite de sub revendas. Ex.: 5)')
						  );
    return $configarray;
  }
  
  function subrevendastm_adminlink ($params)
  {
	
    $code = '<input type="button" value="Acessar Painel" onclick="window.location = \''.$params['serverhostname'].'/admin\';">';
    return $code;
  }

  function subrevendastm_createaccount ($params)
  {
    global $debug;
	
    $CustomFieldQuery = mysql_query ('SELECT	id FROM tblcustomfields WHERE fieldname=\'ID\' AND relid=' . $params['packageid']);
    if (mysql_num_rows ($CustomFieldQuery) == 0)
    {
      $return = 'O campo adicional "ID" não existe.<br>Crie um campo adicional com o nome "ID" em Products/Services';
      return $return;
    }

    $CustomFieldID = sub_mysql_result ($CustomFieldQuery, 0, 'id');
    $IDBaseQuery = mysql_query ('' . 'SELECT	value
										FROM	tblcustomfieldsvalues
										WHERE	fieldid=' . $CustomFieldID . '
										AND		relid=' . $params['accountid']);
    echo mysql_error ();
    if (mysql_num_rows ($IDBaseQuery) == 0)
    {
      mysql_query ('' . 'INSERT INTO tblcustomfieldsvalues (fieldid, relid, value)
							VALUES(' . $CustomFieldID . ', ' . $params['accountid'] . ', \'\')');
    }

    $ID = sub_mysql_result ($IDBaseQuery, 0, 'value');
    if (!empty ($ID))
    {
      $return = 'Esta sub revenda já esta criada.';
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
	  
	  if ($optionname == 'Streamings') {
        $params['configoption1'] = $optionvalue;
        continue;
      } else {
	  	if ($optionname == 'Ouvintes') {
        $params['configoption2'] = $optionvalue;
        continue;
      	} else {
		  if ($optionname == 'Bitrate') {
          $params['configoption3'] = $optionvalue;
          continue;
      	  } else {
		    if ($optionname == 'Espaço AutoDJ') {
      	    $params['configoption4'] = $optionvalue;
        	continue;
      	    } else {
			  if ($optionname == 'AAC+ RTMP') {
        	  $params['configoption5'] = $optionvalue;
	          continue;
      	      } else {
			    if ($optionname == 'Idioma') {
        		$params['configoption6'] = $optionvalue;
        		continue;
				} else {
				  if ($optionname == 'Subrevendas') {
        		  $params['configoption7'] = $optionvalue;
        		  continue;
				  }
			    }
			  } 
			}
		  }
		}
        continue;
      }
    }
	
    $api['acao'] = 'cadastrar_subrevenda';
    $api['streamings'] = $params['configoption1'];
    $api['ouvintes'] = $params['configoption2'];
    $api['bitrate'] = $params['configoption3'];
    $api['espaco'] = $params['configoption4'];
	$api['aacplus'] = $params['configoption5'];
	$api['idioma'] = $params['configoption6'];
	$api['email_subrevenda'] = $params['clientsdetails']['email'];
	$api['senha'] = $params['password'];
	$api['subrevendas'] = $params['configoption7'];

    $response = api_subrevendastm ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
	  
	  $id_subrevenda = $response['returned'];
	  
	  if(!empty($id_subrevenda)) {

      mysql_query ('UPDATE tblhosting 	SET	username=\'' . $params['clientsdetails']['email'] . '\',
											domain=\'' . $id_subrevenda . '\'
										WHERE id=\'' . $params['accountid'] . '\'');
      
	  $dados_customfield = mysql_fetch_array(mysql_query('SELECT id FROM tblcustomfields WHERE fieldname=\'ID\' AND relid=' . $params['packageid']));
										
      mysql_query ('UPDATE tblcustomfieldsvalues 	SET	value=\'' . $id_subrevenda . '\'
														WHERE fieldid=\'' . $dados_customfield['id'] . '\' AND relid=\'' . $params['accountid'] . '\'');
      return 'success';
	  } else {
	  return 'ID faltando.';
	  }
    }

    return $response['error'];
  }

  function subrevendastm_terminateaccount ($params)
  {
    global $debug;
	
    $IDBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'ID\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($IDBaseQuery) == 0)
    {
      $return = 'A sub revenda não esta criada.';
      return $return;
    }

    $ID = sub_mysql_result ($IDBaseQuery, 0, 'value');
    $api['acao'] = 'remover_subrevenda';
    $api['id'] = $ID;
	
    $response = api_subrevendastm ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
      
	  $dados_customfield = mysql_fetch_array(mysql_query('SELECT id FROM tblcustomfields WHERE fieldname=\'ID\' AND relid=' . $params['packageid']));
	  
	  mysql_query ('DELETE FROM tblcustomfieldsvalues WHERE fieldid=\'' . $dados_customfield['id'] . '\' AND relid=\'' . $params['accountid'] . '\'');
	  
	  mysql_query ('UPDATE tblhosting 	SET	username=\'\',
												domain=\'\'
											WHERE id=\'' . $params['accountid'] . '\'');
											
      return 'success';
    }

    return $response['error'];
  }

  function subrevendastm_suspendaccount ($params)
  {
    global $debug;
	
    $IDBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'ID\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($IDBaseQuery) == 0)
    {
      $return = 'A sub revenda não esta criada.';
      return $return;
    }

    $ID = sub_mysql_result ($IDBaseQuery, 0, 'value');
    $api['acao'] = 'bloquear_subrevenda';
    $api['id'] = $ID;

    $response = api_subrevendastm ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
      return 'success';
    }

    return $response['error'];
  }

  function subrevendastm_unsuspendaccount ($params)
  {
    global $debug;
	
    $IDBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'ID\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($IDBaseQuery) == 0)
    {
      $return = 'A sub revenda não esta criada, por favor clique no botão "Create" primeiro.';

      return $return;
    }

    $ID = sub_mysql_result ($IDBaseQuery, 0, 'value');
    $api['acao'] = 'desbloquear_subrevenda';
    $api['id'] = $ID;
	
    $response = api_subrevendastm ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
      return 'success';
    }

    return $response['error'];
  }
  
  function subrevendastm_changepassword ($params)
  {
    global $debug;
	
    $IDBaseQuery = mysql_query ('SELECT 	tblcustomfieldsvalues.value
										FROM 	tblcustomfieldsvalues, tblcustomfields
										WHERE
												tblcustomfields.fieldname=\'ID\'
										AND		tblcustomfieldsvalues.fieldid = tblcustomfields.id
										AND		tblcustomfieldsvalues.relid=' . $params['accountid']);
    if (mysql_num_rows ($IDBaseQuery) == 0)
    {
      $return = 'A sub revenda não esta criada.';
      return $return;
    }

    $ID = sub_mysql_result ($IDBaseQuery, 0, 'value');
    $api['acao'] = 'alterar_senha_subrevenda';
    $api['id'] = $ID;
	$api['nova_senha'] = $params['password'];

    $response = api_subrevendastm ($params['serverhostname'],$params['serverip'],$api);
	
    if ($response['command'] == 'success')
    {
	  
	  mysql_query ('UPDATE tblhosting SET password=\'' . encrypt ($params['password']). '\' WHERE id=\'' . $params['accountid'] . '\'');
	  
      return 'success';
    }

    return $response['error'];
  }

  function api_subrevendastm ($serverhostname,$serverip,$api)
  {
	
	$serverhostname = str_replace("/admin/","",$serverhostname);
	$serverhostname = str_replace("/admin","",$serverhostname);
	$serverhostname = (substr($serverhostname, -1) == '/') ? substr($serverhostname, 0, -1) : $serverhostname;
	
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
	curl_setopt($ch, CURLOPT_USERAGENT, 'Modulo Sub Revenda WHMCS 2.1 ('.$_SERVER['HTTP_HOST'].')');
	$resultado = curl_exec($ch);
	curl_close($ch);

	if($resultado === false) {
      return array ('command' => 'failed', 'error' => 'Erro! Problemas de conexao. / Connection problems.');
	} else {

    list ($status, $retorno, $msg) = explode ('|', $resultado);
	
    if ($status == '0')
    {
      return array ('command' => 'failed', 'error' => ''.$msg.'');
    }

    return array ('command' => 'success', 'returned' => ''.$retorno.'');
	
	}
  }

  
  function sub_mysql_result($result, $iRow, $field = 0)
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