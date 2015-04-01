<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';

    // $_SESSION['auth_salt'] = openssl_random_pseudo_bytes(32, true);
    $_SESSION['auth_salt'] = bin2hex(openssl_random_pseudo_bytes(32));

    echo gen_ajax(
        'auth_salt', $_SESSION['auth_salt']
    );
?>
