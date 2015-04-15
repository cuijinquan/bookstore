<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // get info of the logged-in user
    // args: n/a

    $auth_user_id = session_get('auth_user_id');

    if ($auth_user_id !== null) {
        // logged in

        $user_info = db_user_get($auth_user_id);

        echo ajax_gen(
            'get_success', true,

            'user_id', $user_info['user_id'],

            'mail', $user_info['mail'],
            'name', $user_info['name'],
            'image', $user_info['image'],
            'detail', $user_info['detail'],
            // 'password', $user_info['password'],
            'is_admin', $user_info['is_admin'],
            'location', $user_info['location'],
            'address', $user_info['address'],

            'bought_count', $user_info['bought_count'],
            'book_count', $user_info['book_count'],
            'sold_count', $user_info['sold_count'],

            'date_create', $user_info['date_create'],
            'date_login', $user_info['date_login']
        );
    } else {
        // not logged in

        echo ajax_gen(
            'get_success', false
        );
    }
?>
