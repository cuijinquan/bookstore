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
        $user_info = db_user_get_name($post_name);

        $auth_success = true;
        $auth_user_id = $user_info['user_id'];
        $auth_name = $post_name;

        session_set('auth_user_id', $auth_user_id);

        $user_info['date_create'] = time();
        $user_info['date_login'] = time();
        db_user_set($user_info);
    } else {
        $auth_success = false;
        $auth_user_id = null;
        $auth_name = null;

    }

    echo ajax_gen(
        'auth_success', $auth_success,
        'auth_user_id', $auth_user_id,
        'auth_name', $auth_name
    );
?>
