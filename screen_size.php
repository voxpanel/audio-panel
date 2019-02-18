<?php

mysql_query("INSERT INTO screen_size (width,height,data) VALUES ('".$_POST['width']."','".$_POST['height']."',NOW())") or die(mysql_error());

?>