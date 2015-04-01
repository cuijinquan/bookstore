<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db.php';

    $post_name = get_arg('auth_name');
    $post_password = get_arg('auth_password');

    $auth_salt = session_delete('auth_salt');

    $user_info = db_select('user', 'name', $post_name)->fetch_assoc();
    if ($user_info) {
        $auth_password = $hash('sha256', $user_info['password'] . $auth_salt);
        if ($post_password == $auth_password) {
            // login ok

            $auth_name = $post_name;
            $auth_success = true;

            session_set('auth_name', $auth_name);
        } else {
            // wrong password

            $auth_name = $post_name;
            $auth_success = false;

            session_delete('auth_name');
        }
    } else {
        // wrong name

        $auth_name = null;
        $auth_success = false;
    }

    echo gen_ajax(
        'auth_name', $auth_name,
        'auth_success', $auth_success
    );
?>
