<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // accept an order
    // args: buy_id

    $post_buy_id = intval(ajax_arg('buy_id', FILTER_VALIDATE_REGEXP, $filter_number));

    $auth_user_id = session_get_force('auth_user_id');

    $buy_info = db_buy_get($post_buy_id);

    if (
        $buy_info['seller_user_id'] === $auth_user_id
        && $buy_info['date_accept'] === null
        && $buy_info['date_done'] === null
        && db_buy_set_accept($post_buy_id)
    ) {
        // accepted

        $set_success = true;
    } else {
        // wrong user or fail

        $set_success = false;
    }

    echo ajax_gen(
        'set_success', $set_success
    );
?>
