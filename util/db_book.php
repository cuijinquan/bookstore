<?php
    require_once 'db.php';

    function db_book_init() {
        global $db_conn;

        // books to sell
        $db_conn->query('
            create table book (
                book_id         int             primary key,
                owner_user_id   int,
                parent_cat_id   int,

                name            varchar(64),
                detail          text,
                price           varchar(64),
                inventory       int,

                date_add        datetime
            );
        ');
    }
?>
