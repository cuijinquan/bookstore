<?php
    require_once 'config.php';

    $db_conn = mysql_connect($db, $db_username, $db_password);
    mysql_select_db($db_name, $db_conn)
?>
