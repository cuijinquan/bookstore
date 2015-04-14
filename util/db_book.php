<?php
    require_once 'db.php';

    // db actions of books

    function db_book_init() {
        global $db_conn;

        // books to sell
        //     inventory: auto decrease + edit by user
        return $db_conn->query('
            create table book (
                book_id         bigint          auto_increment  primary key,
                owner_user_id   bigint          not null,
                parent_cat_id   bigint          not null,

                foreign key (owner_user_id) references user (user_id)
                                on delete cascade on update cascade,
                foreign key (parent_cat_id) references cat (cat_id)
                                on delete cascade on update cascade,

                name            varchar(64)     not null,
                image           varchar(64),
                detail          text,
                price           varchar(64)     not null,
                inventory       bigint          not null,

                sold_count      bigint          not null,

                date_create     datetime        not null
            );
        ');
    }

    function db_book_drop() {
        global $db_conn;

        return $db_conn->query('
            drop table book;
        ');
    }

    function db_book_add(
        $owner_user_id, $parent_cat_id,
        $name, $image, $detail, $price, $inventory
    ) {
        return db_insert(
            'book',
            null, $owner_user_id, $parent_cat_id,
            $name, $image, $detail, $price, $inventory,
            0,
            date('Y-m-d H:i:s')
        ) && db_update(
            'user',
            'user_id', $owner_user_id,
            'book_count = book_count + 1'
        ) && db_update( // $parent_cat_id should not be 0
            'cat',
            'cat_id', $parent_cat_id,
            'book_count = book_count + 1'
        );
    }

    function db_book_get($book_id) {
        return db_select('book', 'book_id', $book_id)->fetch_assoc();
    }

    function db_book_list_all(
        $cond, $order, $desc, $begin, $count = 50
    ) {
        return db_select_cond(
            'book', $cond, array(),
            $order, $desc, $begin, $count
        );
    }

    function db_book_list_owner(
        $user_id,
        $begin, $count = 50
    ) {
        return db_select(
            'book', 'owner_user_id', $user_id,
            null, true, $begin, $count
        );
    }

    function db_book_list_cat(
        $cat_id,
        $begin, $count = 50
    ) {
        return db_select(
            'book', 'parent_cat_id', $cat_id,
            null, true, $begin, $count
        );
    }

    // function db_book_set($data) {
    //     return db_write('book', $data, true);
    // }
?>
