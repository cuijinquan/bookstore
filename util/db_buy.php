<?php
    require_once 'db.php';

    function db_buy_init() {
        global $db_conn;

        // orders
        //     address: full shipping information
        return $db_conn->query('
            create table buy (
                buy_id          bigint          auto_increment  primary key,
                buy_book_id     bigint          not null,
                buyer_user_id   bigint          not null,

                address         text,
                feedback        text,

                date_create     datetime,
                date_accept     datetime,
                date_done       datetime
            );
        ');
    }

    function db_buy_truncate() {
        global $db_conn;

        return $db_conn->query('
            truncate table buy;
        ');
    }

    function db_buy_add(
        $buy_book_id, $buyer_user_id,
        $address
    ) {
        return db_insert(
            'buy',
            null, $buy_book_id, $buyer_user_id,
            $address, null,
            time(), null, null
        );
    }

    function db_buy_get($buy_id) {
        return db_select('buy', 'buy_id', $buy_id)->fetch_assoc();
    }

    function db_buy_list_book(
        $book_id, $show_done,
        $begin, $count = 20, $desc = true
    ) {
        if ($show_done) {
            return db_select(
                'buy', 'buy_book_id', $book_id, $begin, $count, $desc
            );
        } else {
            return db_select(
                'buy', 'buy_book_id', $book_id, $begin, $count, $desc,
                'date_done is none'
            );
        }
    }

    function db_buy_list_buyer(
        $user_id, $show_done,
        $begin, $count = 20, $desc = true
    ) {
        if ($show_done) {
            return db_select(
                'buy', 'buyer_user_id', $user_id, $begin, $count, $desc
            );
        } else {
            return db_select(
                'buy', 'buyer_user_id', $user_id, $begin, $count, $desc,
                'date_done is none'
            );
        }
    }

    function db_buy_set($data) {
        return db_write('buy', $data, true);
    }
?>
