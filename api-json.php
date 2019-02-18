<?php
$api_xml = @simplexml_load_file("http://audiocast.ml/api/".query_string('1')."");

header('Content-type: text/javascript');

print_r(json_encode($api_xml));

?>