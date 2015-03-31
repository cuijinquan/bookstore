<?php
    require_once 'db.php';

    function db_buy_init() {
        global $db_conn;

        // orders
        //     address: full shipping information
        return $db_conn->query('
            create table buy (
                buy_id          int             primary key,
                buy_book_id     int,
                buyer_user_id   int,

                address         text,
                feedback        text,

                date_create     datetime,
                date_accept     datetime,
                date_done       datetime
            );
        ');
    }
?>
