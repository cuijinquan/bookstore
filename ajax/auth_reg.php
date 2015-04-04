<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $post_mail = ajax_arg('mail', FILTER_VALIDATE_EMAIL, null);
    $post_name = ajax_arg('name', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_password = ajax_arg('password', FILTER_VALIDATE_REGEXP, $filter_hash);
    $post_location = ajax_arg('location', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_address = ajax_arg('address', FILTER_UNSAFE_RAW, null);
    $post_about = ajax_arg('about', FILTER_UNSAFE_RAW, null);

    if (db_user_add(
        $post_mail, $post_name, $post_password,
        $post_location, $post_address, $post_about
    )) {
        $auth_success = true;
    } else {
        $auth_success = false;
    }

    echo ajax_gen(
        'auth_success', $auth_success
    );
?>
