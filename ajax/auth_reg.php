<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $post_mail = ajax_arg('mail', FILTER_UNSAFE_RAW, null);
    $post_name = ajax_arg('name', FILTER_UNSAFE_RAW, null);
    $post_password = ajax_arg('password', FILTER_UNSAFE_RAW, null);
    $post_location = ajax_arg('location', FILTER_UNSAFE_RAW, null);
    $post_address = ajax_arg('address', FILTER_UNSAFE_RAW, null);
    $post_about = ajax_arg('about', FILTER_UNSAFE_RAW, null);

    $auth_reg_error = null;

    // if (preg_match('/', $post_mail)) {
    //     $auth_reg_error = 'Bad mail format';
    // }

    // if ($post_name == '') {
    //     $auth_reg_error = 'Bad username';
    // }

    if ($auth_reg_error === null) {
        if (!db_user_add(
            $post_mail, $post_name, $post_password,
            $post_location, $post_address, $post_about
        )) {
            $auth_reg_error = 'User already exists';
        }
    }

    echo ajax_gen(
        'auth_reg_error', $auth_reg_error
    );
?>
