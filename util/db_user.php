<?php
    require_once 'db.php';

    function db_user_init() {
        global $db_conn;

        // user accounts
        //     password: hash('sha256', $name . ':' . $password_raw)
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

    function db_user_truncate() {
        global $db_conn;

        return $db_conn->query('
            truncate table user;
        ');
    }

    function crypt_password($name, $password_raw) {
        return hash('sha256', $name . ':' . $password_raw);
    }

    function db_user_add(
        $mail, $name, $password, $location, $address, $about
    ) {
        return db_insert(
            'user',
            null,
            $mail, $name, $password, false, $location, $address, $about,
            time(), time()
        );
    }

    function db_user_add_admin(
        $mail, $name, $password, $location, $address, $about
    ) {
        return db_insert(
            'user',
            null,
            $mail, $name, $password, true, $location, $address, $about,
            time(), time()
        );
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
