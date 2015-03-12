



<?php

define('PUN_ROOT', './');
require PUN_ROOT.'/config2.php';
require PUN_ROOT.'/include/fonctions.php';

 backup_tables($db_host, $db_username, $db_password, $db_name );
 

?>

