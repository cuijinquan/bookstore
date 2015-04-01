<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db.php';

    $post_user_id = ajax_arg('user_id');

    $auth_user_id = session_get('auth_user_id');

    if ($post_user_id == $auth_user_id) {
        // logout ok

        $auth_success = true;

        session_delete('auth_user_id');
    } else {
        // wrong id

        $auth_success = false;
    }

    echo ajax_gen(
        'auth_success', $auth_success
    );
?>
