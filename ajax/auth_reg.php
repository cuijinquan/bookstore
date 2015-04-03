<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $post_mail = ajax_arg('mail');
    $post_name = ajax_arg('name');
    $post_password = ajax_arg('password');
    $post_location = ajax_arg('location');
    $post_address = ajax_arg('address');
    $post_about = ajax_arg('about');

    $auth_reg_error = null;

    if ($post_mail == '') {
        $auth_reg_error = 'Bad mail format';
    }

    if ($post_name == '') {
        $auth_reg_error = 'Bad username';
    }

    if (!$auth_reg_error) {
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
