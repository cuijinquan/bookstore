<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $auth_user_id = session_get('auth_user_id');

    if ($auth_user_id !== null) {
        $user_info = db_user_get($auth_user_id);

        $auth_name = $user_info['name'];
    } else {
        $auth_name = null;
    }

    echo ajax_gen(
        'auth_user_id', $auth_user_id,
        'auth_name', $auth_name
    );
?>
