<?php
    require_once 'db.php';

    function db_user_init() {
        global $db_conn;

        // user accounts
        //     password: hash('sha256', '...')
        //     is_admin: 'Y' or 'N'
        return $db_conn->query('
            create table user (
                user_id         bigint          auto_increment  primary key,

                mail            varchar(64)     unique,
                name            varchar(64)     unique,
                password        char(64),
                is_admin        char(1),
                location        varchar(64),
                address         text,
                about           text,

                date_create     datetime,
                date_login      datetime
            );
        ');
    }

    function db_user_get($user_id) {
        return db_select('user', 'user_id', $user_id)->fetch_assoc();
    }

    function db_user_get_name($name) {
        return db_select('user', 'name', $name)->fetch_assoc();
    }

    function db_user_set($data) {
        return db_write('user', $data, true);
    }
?>
