<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $post_name = ajax_arg('name');
    $post_password = ajax_arg('password');

    $auth_salt = session_delete('auth_salt');

    $user_info = db_user_get_name($post_name);

    if ($user_info) {
        $auth_password = $hash('sha256', $user_info['password'] . $auth_salt);

        if ($post_password == $auth_password) {
            // login ok

            $auth_user_id = $user_info['user_id'];
            $auth_name = $post_name;
            $auth_success = true;

            session_set('auth_user_id', $auth_user_id);

            $user_info['date_login'] = time();
            db_user_set($user_info);
        } else {
            // wrong password

            $auth_user_id = null;
            $auth_name = $post_name;
            $auth_success = false;

            session_delete('auth_user_id');
        }
    } else {
        // wrong name

        $auth_user_id = null;
        $auth_name = null;
        $auth_success = false;
    }

    echo ajax_gen(
        'auth_user_id', $auth_user_id,
        'auth_name', $auth_name,
        'auth_success', $auth_success
    );
?>
