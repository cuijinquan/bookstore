<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // do login
    // common args: n/a
    // args: name, password (salted hash)

    $post_name = ajax_arg('name', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_password = ajax_arg('password', FILTER_VALIDATE_REGEXP, $filter_hash);

    $auth_salt = session_delete('auth_salt');

    $user_info = db_user_get_name($post_name);

    if ($user_info) {
        $auth_password = hash('sha256', $user_info['password'] . $auth_salt);

        if ($post_password === $auth_password) {
            // login ok

            $auth_success = true;
            $auth_user_id = intval($user_info['user_id']);
            $auth_name = $user_info['name'];
            $auth_sudo = intval($user_info['is_admin']) > 0;

            session_set('auth_user_id', $auth_user_id);
            session_set('auth_sudo', $auth_sudo);

            db_user_set_login($auth_user_id);
        } else {
            // wrong password

            $auth_success = false;
            $auth_user_id = null;
            $auth_name = $user_info['name'];
            $auth_sudo = false;
        }
    } else {
        // wrong name

        $auth_success = false;
        $auth_user_id = null;
        $auth_name = null;
        $auth_sudo = false;
    }

    echo ajax_gen(
        'auth_success', $auth_success,
        'auth_user_id', $auth_user_id,
        'auth_name', $auth_name,
        'auth_sudo', $auth_sudo
    );
?>
