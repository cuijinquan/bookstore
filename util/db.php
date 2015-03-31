<?php
    require_once 'config.php';

    $db_conn = new mysqli($db_ip, $db_username, $db_password, $db_name, $db_port);

    if ($db_conn->connect_error) {
        die('DB error: ' . $mysqli->connect_error);
    }

?>
