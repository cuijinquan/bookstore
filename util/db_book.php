<?php
    require_once '../util/db.php';

    function db_book_init() {
        // books to sell
        mysql_query('
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
