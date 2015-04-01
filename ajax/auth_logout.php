<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db.php';

    $post_name = get_arg('auth_name');

    $auth_name = session_get('auth_name');

    if ($post_name == $auth_name) {
        // logout ok

        $auth_success = true;

        session_delete('auth_name');
    } else {
        // wrong name

        $auth_success = false;
    }

    echo gen_ajax(
        'auth_success', $auth_success
    );
?>
