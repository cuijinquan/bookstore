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

    function db_book_get($book_id) {
        return db_select('book', 'book_id', $book_id)->fetch_assoc();
    }

    function db_book_select_owner(
        $user_id, 
        $begin = 0, $count = 20, $desc = false
    ) {
        return db_select('book', 'owner_user_id', $user_id, $begin, $count, $desc);
    }

    function db_book_select_cat(
        $cat_id,
        $begin = 0, $count = 20, $desc = false
    ) {
        return db_select('book', 'parent_cat_id', $cat_id, $begin, $count, $desc);
    }
?>
