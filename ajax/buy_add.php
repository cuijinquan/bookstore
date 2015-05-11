<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // buy a book
    // common args: login_user_id
    // args: book_id, address

    $auth_user_id = intval(ajax_arg('login_user_id', FILTER_VALIDATE_REGEXP, $filter_number));
    session_check('auth_user_id', $auth_user_id);

    $post_book_id = intval(ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_address = ajax_arg('address', FILTER_UNSAFE_RAW, null);

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
