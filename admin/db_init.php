<?php
    require_once '../util/db_user.php';
    require_once '../util/db_cat.php';
    require_once '../util/db_book.php';
    require_once '../util/db_buy.php';

    // create all tables in the database

    db_user_init();
    db_cat_init();
    db_book_init();
    db_buy_init();
?>
