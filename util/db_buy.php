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

    function db_buy_get($buy_id) {
        return db_select('buy', 'buy_id', $buy_id)->fetch_assoc();
    }

    function db_buy_set($data) {
        return db_write('buy', $data, true);
    }
?>
