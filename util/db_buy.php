<?php
    require_once 'db.php';
    // require_once 'db_user.php';
    require_once 'db_book.php';

    // db actions of orders

    function db_buy_init() {
        global $db_conn;

        // orders
        //     address: full shipping information
        return $db_conn->query('
            create table buy (
                buy_id          bigint          auto_increment  primary key,
                buyer_user_id   bigint          not null,
                buy_book_id     bigint          not null,

                seller_user_id  bigint          not null,
                book_name       varchar(64)     not null,

                foreign key (buyer_user_id) references user (user_id)
                                on delete cascade on update cascade,
                foreign key (buy_book_id, seller_user_id, book_name)
                                references book (book_id, owner_user_id, name)
                                on delete cascade on update cascade,

                address         text            not null,
                feedback        text,

                date_create     datetime        not null,
                date_accept     datetime,
                date_done       datetime
            ) ENGINE = InnoDB;
        ');
    }

    function db_buy_drop() {
        global $db_conn;

        return $db_conn->query('
            drop table buy;
        ');
    }

    function db_buy_add(
        $buyer_user_id, $buy_book_id,
        $address
    ) {
        $book_info = db_book_get($buy_book_id);

        return db_insert(
            'buy',
            null, $buyer_user_id, $buy_book_id,
            $book_info['owner_user_id'], $book_info['name'],
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

    function db_buy_set_accept($buy_id) {
        $buy_info = db_buy_get($buy_id);

        return db_update(
            'buy',
            'buy_id', $buy_id,
            'date_accept = %s', array(date('Y-m-d H:i:s'))
        ) && db_update(
            'user',
            'user_id', $buy_info['buyer_user_id'],
            'bought_count = bought_count + 1'
        ) && db_update(
            'book',
            'book_id', $buy_info['buy_book_id'],
            'inventory = greatest(inventory - 1, 0)'
        );
    }

    function db_buy_set_done($buy_id, $feedback) {
        $buy_info = db_buy_get($buy_id);

        return db_update(
            'buy',
            'buy_id', $buy_id,
            'date_done = %s, feedback = %s', array(date('Y-m-d H:i:s'), $feedback)
        ) && db_update(
            'user',
            'user_id', $buy_info['buyer_user_id'],
            'sold_count = sold_count + 1'
        ) && db_update(
            'book',
            'book_id', $buy_info['buy_book_id'],
            'sold_count = sold_count + 1'
        );
    }

    // function db_buy_set($data) {
    //     return db_write('buy', $data, true);
    // }
?>
