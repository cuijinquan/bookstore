<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';

    // do logout
    // common args: login_user_id
    // args: n/a

    $auth_user_id = intval(ajax_arg('login_user_id', FILTER_VALIDATE_REGEXP, $filter_number));
    session_check('auth_user_id', $auth_user_id);

    // if success
    session_delete('auth_user_id');
    session_delete('auth_sudo');

    echo ajax_gen();
?>
