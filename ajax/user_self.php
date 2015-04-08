<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $auth_user_id = session_get('auth_user_id');

    if ($auth_user_id !== null) {
        $user_info = db_user_get($auth_user_id);

        echo ajax_gen(
            'get_success', true,

            'user_id', $user_info['user_id'],

            'mail', $user_info['mail'],
            'name', $user_info['name'],
            'image', $user_info['image'],
            'detail', $user_info['detail'],
            'is_admin', $user_info['is_admin'],
            'location', $user_info['location'],
            'address', $user_info['address'],

            'date_create', $user_info['date_create'],
            'date_login', $user_info['date_login']
        );
    } else {
        echo ajax_gen(
            'get_success', false
        );
    }
?>
