<?php
    require_once 'db.php';

    // db actions of orders

    function db_buy_init() {
        global $db_conn;

        // orders
        //     address: full shipping information
        return $db_conn->query('
            create table buy (
                buy_id          bigint          auto_increment  primary key,
                buy_book_id     bigint          not null,
                buyer_user_id   bigint          not null,

                foreign key (buy_book_id) references book (book_id)
                                on delete cascade on update cascade,
                foreign key (buyer_user_id) references user (user_id)
                                on delete cascade on update cascade,

                address         text            not null,
                feedback        text,

                date_create     datetime        not null,
                date_accept     datetime,
                date_done       datetime
            );
        ');
    }

    function db_buy_drop() {
        global $db_conn;

        return $db_conn->query('
            drop table buy;
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
            date('Y-m-d H:i:s'), null, null
        );
    }

    function db_buy_get($buy_id) {
        return db_select('buy', 'buy_id', $buy_id)->fetch_assoc();
    }

    function db_buy_list_all(
        $cond, $order, $desc, $begin, $count = 50
    ) {
        return db_select_cond(
            'buy', $cond, array(),
            $order, $desc, $begin, $count
        );
    }

    function db_buy_list_book(
        $book_id, $show_done,
        $begin, $count = 50
    ) {
        if ($show_done) {
            return db_select(
                'buy', 'buy_book_id', $book_id,
                null, true, $begin, $count
            );
        } else {
            return db_select(
                'buy', 'buy_book_id', $book_id,
                null, true, $begin, $count, 'date_done is none' // TODO: db_select_cond
            );
        }
    }

    function db_buy_list_buyer(
        $user_id, $show_done,
        $begin, $count = 50
    ) {
        if ($show_done) {
            return db_select(
                'buy', 'buyer_user_id', $user_id,
                null, true, $begin, $count
            );
        } else {
            return db_select(
                'buy', 'buyer_user_id', $user_id,
                null, true, $begin, $count, 'date_done is none' // TODO: db_select_cond
            );
        }
    }

    // function db_buy_set($data) {
    //     return db_write('buy', $data, true);
    // }
?>
