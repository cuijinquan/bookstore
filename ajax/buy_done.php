<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // finish an order and add feedback
    // args: buy_id, feedback

    $post_buy_id = intval(ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_feedback = ajax_arg('feedback', FILTER_UNSAFE_RAW, null);

    $auth_user_id = session_get_force('auth_user_id');

    $buy_info = db_buy_get($post_buy_id);

    if (
        $buy_info['buyer_user_id'] === intval($auth_user_id)
        && $buy_info['date_accept'] !== null
        && $buy_info['date_done'] === null
        && db_buy_set_done($post_buy_id, $feedback)
    ) {
        // done

        $set_success = true;
    } else {
        // wrong user or fail

        $set_success = false;
    }

    echo ajax_gen(
        'set_success', $set_success
    );
?>
