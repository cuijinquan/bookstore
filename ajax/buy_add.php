<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // buy a book
    // args: book_id, address

    $post_book_id = intval(ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_address = ajax_arg('address', FILTER_UNSAFE_RAW, null);

    $auth_user_id = session_get_force('auth_user_id');

    // TODO: check owner !== buyer
    if (db_buy_add($auth_user_id, $post_book_id, $post_address)) {
        // add ok

        $set_success = true;
    } else {
        // add fail

        $set_success = false;
    }

    echo ajax_gen(
        'set_success', $set_success
    );
?>
