<?php
    require_once 'config.php';

    $db_conn = new mysqli($db_ip, $db_username, $db_password, $db_name, $db_port);

    if ($db_conn->connect_error) {
        die('DB error: ' . $mysqli->connect_error);
    }

    function db_select($table, $column, $value) {
        global $db_conn;

        return $db_conn->query('
            select * from `' . $db_conn->escape_string($table) . '`
            where `' . $db_conn->escape_string($column) . '` = "' . $db_conn->escape_string($value) . '"
        ');
    }

    // example: db_insert('some_table', '1', '2', '3', '4', '5', '6', '7', '8');
    function db_insert($table) {
        global $db_conn;

        $data_str = '';

        for ($i = 1; $i < func_num_args(); $i += 1) {
            $item = func_get_arg($i);

            if ($i == 1) {
                $data_str = '"' . $db_conn->escape_string($item) . '"';
            } else {
                $data_str = $data_str . ', "' . $db_conn->escape_string($item) . '"';
            }
        }

        return $db_conn->query('
            replace into `' . $db_conn->escape_string($table) . '`
            values (' . $data_str . ')
        ');
    }
?>
