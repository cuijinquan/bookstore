<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db.php';

    $post_name = $_POST['auth_name'];
    $auth_name = session_get('auth_name');

    if ($post_name == $auth_name) {
        $auth_success = true;

        session_delete('auth_name');
    } else {
        $auth_success = false;
    }

    echo gen_ajax(
        'auth_success', $auth_success
    );
?>
