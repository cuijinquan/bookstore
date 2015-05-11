<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // get info of an user
    // common args: n/a
    // args: user_id

    $post_user_id = intval(ajax_arg('user_id', FILTER_VALIDATE_REGEXP, $filter_number));

    $user_info = db_user_get($post_user_id);

    if ($user_info) {
        // user exists

        echo ajax_gen(
            'get_success', true,

            'user_id', $user_info['user_id'],

            'name', $user_info['name'],
            'image', $user_info['image'],
            'detail', $user_info['detail'],
            'location', $user_info['location'],

            'book_count', $user_info['book_count'],
            'sold_count', $user_info['sold_count']
        );
    } else {
        // user not found

        echo ajax_gen(
            'get_success', false
        );
    }
?>
