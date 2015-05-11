<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // finish an order and add feedback
    // common args: login_user_id
    // args: buy_id, feedback

    $auth_user_id = intval(ajax_arg('login_user_id', FILTER_VALIDATE_REGEXP, $filter_number));
    session_check('auth_user_id', $auth_user_id);

    $post_buy_id = intval(ajax_arg('buy_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_feedback = ajax_arg('feedback', FILTER_UNSAFE_RAW, null);

    $buy_info = db_buy_get($post_buy_id);

    if (
        $buy_info
        && intval($buy_info['buyer_user_id']) === $auth_user_id
        && $buy_info['date_accept'] !== null
        && $buy_info['date_done'] === null
        && db_buy_set_done($post_buy_id, $post_feedback)
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
