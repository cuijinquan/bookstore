<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';

    // generate a salt value for authorization
    // common args: n/a
    // args: n/a

    // stronger version: openssl_random_pseudo_bytes(32, true)
    $auth_salt = bin2hex(openssl_random_pseudo_bytes(32));

    session_set('auth_salt', $auth_salt);

    echo ajax_gen(
        'auth_salt', $auth_salt
    );
?>
