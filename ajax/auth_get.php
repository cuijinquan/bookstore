<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // get login status
    // common args: n/a
    // args: n/a

    $auth_user_id = session_get('auth_user_id');

    if ($auth_user_id !== null) {
        $user_info = db_user_get($auth_user_id);

        $auth_name = $user_info['name'];
        $auth_sudo = $user_info['is_admin'];
    } else {
        $auth_name = null;
        $auth_sudo = false;
    }

    echo ajax_gen(
        'auth_user_id', $auth_user_id,
        'auth_name', $auth_name,
        'auth_sudo', $auth_sudo
    );
?>
