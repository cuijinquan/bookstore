<?php
    require_once 'config.php';

    // connect to the database

    $db_conn = new mysqli($db_ip, $db_username, $db_password, $db_name, $db_port);

    if ($db_conn->connect_error) {
        die('DB error: ' . $mysqli->connect_error);
    }

    $db_conn->query('set character set "utf8";');
    $db_conn->query('set names "utf8";');

    // escape null, int or string
    function db_escape($value) {
        global $db_conn;

        if ($value === null) {
            return 'null';
        } else if (is_int($value)) {
            return strval($value);
        } else {
            return '"' . $db_conn->escape_string($value) . '"';
        }
    }

    // select rows in the database by a simple rule
    function db_select(
        $table, $cond_column, $cond_value,
        $order = null, $desc = false, $begin = 0, $count = 50
    ) {
        global $db_conn;

        if ($order === null) {
            $order = $cond_column;
        }

        return $db_conn->query('
            select * from `' . $db_conn->escape_string($table) . '`
            where (
                `' . $db_conn->escape_string($cond_column) . '`
                = ' . db_escape($cond_value) . '
            )
            order by `' . $db_conn->escape_string($order)
                . '` ' . ($desc ? 'desc' : 'asc') . '
            limit ' . intval($begin) . ', ' . intval($count) . ';
        ');
    }

    // select rows in the database by customized rules
    function db_select_cond(
        $table, $cond /* raw */, $cond_values,
        $order, $desc = false, $begin = 0, $count = 50
    ) {
        global $db_conn;

        // if ($order === null) {
        //     $order = $column;
        // }

        $args = array();

        foreach ($cond_values as $key => $value) {
            $args[$key] = db_escape($value);
        }

        return $db_conn->query('
            select * from `' . $db_conn->escape_string($table) . '`
            where (
                ' . vsprintf($cond, $args) . '
            )
            order by `' . $db_conn->escape_string($order) . '` '
                . ($desc ? 'desc' : 'asc') . '
            limit ' . intval($begin) . ', ' . intval($count) . ';
        ');
    }

    // add a row to the database
    function db_insert($table /* var args */) {
        global $db_conn;

        $data_str = '';

        for ($i = 1; $i < func_num_args(); $i += 1) {
            $value = func_get_arg($i);

            if ($i === 1) {
                $data_str = db_escape($value);
            } else {
                $data_str = $data_str . ', ' . db_escape($value);
            }
        }

        return $db_conn->query('
            insert into `' . $db_conn->escape_string($table) . '`
            values (' . $data_str . ');
        ');
    }

    // update an exist row
    function db_update(
        $table, $cond_column, $cond_value,
        $expr /* raw */, $values = array()
    ) {
        global $db_conn;

        $args = array();

        foreach ($values as $key => $value) {
            $args[$key] = db_escape($values[$key]);
        }

        return $db_conn->query('
            update `' . $db_conn->escape_string($table) . '`
            set ' . vsprintf($expr, $args) . '
            where (
                `' . $db_conn->escape_string($cond_column) . '`
                = ' . db_escape($cond_value) . '
            );
        ');
    }

    // write (insert or replace) data to the database
    function db_write($table, $data, $replace) {
        global $db_conn;

        if ($replace) {
            $command = 'replace';
        } else {
            $command = 'insert ignore';
        }

        $column_str = '';
        $data_str = '';

        foreach ($data as $key => $value) {
            if ($value !== null) {
                if ($column_str === '') {
                    $column_str = '`' . $db_conn->escape_string($key) . '`';
                    $data_str = db_escape($value);
                } else {
                    $column_str = $column_str . ', `' . $db_conn->escape_string($key) . '`';
                    $data_str = $data_str . ', ' . db_escape($value);
                }
            }
        }

        return $db_conn->query('
            ' . $command . ' into `' . $db_conn->escape_string($table) . '` (' . $column_str . ')
            values (' . $data_str . ');
        ');
    }

    // remove rows in the database
    function db_delete($table, $cond_column, $cond_value) {
        global $db_conn;

        return $db_conn->query('
            delete from `' . $db_conn->escape_string($table) . '`
            where (
                `' . $db_conn->escape_string($cond_column) . '`
                = ' . db_escape($cond_value) . '
            );
        ');
    }
?>
