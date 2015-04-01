<?php
    require_once 'db.php';

    function db_book_init() {
        global $db_conn;

        // books to sell
        return $db_conn->query('
            create table book (
                book_id         bigint          auto_increment  primary key,
                owner_user_id   bigint          not null,
                parent_cat_id   bigint          not null,

                name            varchar(64),
                detail          text,
                price           varchar(64),
                inventory       bigint,

                date_add        datetime
            );
        ');
    }
?>
