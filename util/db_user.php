<?php
    require_once '../util/db.php';

    function db_user_init() {
        // user accounts
        //     password: hash('sha256', '...')
        //     is_admin: 'Y' or 'N'
        mysql_query('
            create table user (
                user_id         int             primary key,

                mail            varchar(64)     unique,
                name            varchar(64)     unique,
                password        char(64),
                is_admin        char(1),
                location        varchar(64),
                about           text,

                date_create     datetime,
                date_login      datetime
            );
        ');
    }

?>
