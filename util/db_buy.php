<?php
    require_once '../util/db.php';

    function db_buy_init() {
        // orders
        //     address: full shipping information
        mysql_query('
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
