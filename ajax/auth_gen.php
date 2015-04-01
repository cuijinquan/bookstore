<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';

    // $auth_salt = openssl_random_pseudo_bytes(32, true);
    $auth_salt = bin2hex(openssl_random_pseudo_bytes(32));

    session_set('auth_salt', $auth_salt);

    echo gen_ajax(
        'auth_salt', $auth_salt
    );
?>
