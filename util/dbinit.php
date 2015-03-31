<?php
    require_once 'db.php';

    // user accounts
    //     password: hash('sha256', '...')
    //     is_admin: 'Y' or 'N'
    mysql_query('
        create table user (
            user_id         int             primary key,

            mail            varchar(64)     unique,
            name            varchar(64)     unique,
            password        char(64),
            is_admin        char(1),
            location        varchar(64),
            about           text,

            date_create     datetime,
            date_login      datetime
        );
    ');

    // catalogs
    mysql_query('
        create table cat (
            cat_id          int             primary key,

            parent_cat_id   int,
            name            varchar(64)     unique,
            detail          text
        );
    ');

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
?>
