<?php
    require_once 'db.php';

    // db actions of users

    function db_user_init() {
        global $db_conn;

        // users
        //     password: hash('sha256', $name . ':' . $password_raw)
        return $db_conn->query('
            create table user (
                user_id         bigint          auto_increment,

                    primary key (user_id),

                mail            varchar(64)     not null,
                name            varchar(64)     not null,
                image           varchar(64),
                detail          text            not null,
                password        char(64)        not null,
                is_admin        bool            not null,
                location        varchar(64)     not null,
                address         text            not null,

                    unique key (mail),
                    unique key (name),

                bought_count    bigint          not null,
                book_count      bigint          not null,
                sold_count      bigint          not null,

                    key (book_count),
                    key (sold_count),

                date_create     datetime        not null,
                date_login      datetime        not null,

                    key (date_create)
            ) ENGINE = InnoDB;
        ');
    }

    function db_user_drop() {
        global $db_conn;

        return $db_conn->query('
            drop table user;
        ');
    }

    function crypt_password($name, $password_raw) {
        return hash('sha256', $name . ':' . $password_raw);
    }

    function db_user_add(
        $mail, $name, $image, $detail, $password, $location, $address
    ) {
        return db_insert(
            'user',
            null,
            $mail, $name, $image, $detail, $password, false, $location, $address,
            0, 0, 0,
            date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
        );
    }

    function db_user_add_admin(
        $mail, $name, $image, $detail, $password, $location, $address
    ) {
        return db_insert(
            'user',
            null,
            $mail, $name, $image, $detail, $password, true, $location, $address,
            0, 0, 0,
            date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
        );
    }

    function db_user_get($user_id) {
        return db_select('user', 'user_id', $user_id)->fetch_assoc();
    }

    function db_user_get_name($name) {
        return db_select('user', 'name', $name)->fetch_assoc();
    }

    function db_user_list_all(
        $cond, $order, $desc, $begin, $count = 50
    ) {
        return db_select_cond(
            'user', $cond, array(),
            $order, $desc, $begin, $count
        );
    }

    function db_user_set_login($user_id) {
        return db_update(
            'user',
            'user_id', $user_id,
            'date_login = %s', array(date('Y-m-d H:i:s'))
        );
    }

    function db_user_set($data) {
        return db_write('user', $data, true);
    }
?>
