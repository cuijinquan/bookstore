<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // do user registration
    // common args: n/a
    // args: mail, name, image, detail, password, location, address

    $post_mail = ajax_arg('mail', FILTER_VALIDATE_EMAIL, null);
    $post_name = ajax_arg('name', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_image = or_null(ajax_arg('image', FILTER_VALIDATE_REGEXP, $filter_imghash));
    $post_detail = ajax_arg('detail', FILTER_UNSAFE_RAW, null);
    $post_password = ajax_arg('password', FILTER_VALIDATE_REGEXP, $filter_hash);
    $post_location = ajax_arg('location', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_address = ajax_arg('address', FILTER_UNSAFE_RAW, null);

    if (db_user_add(
        $post_mail, $post_name, $post_image, $post_detail, $post_password,
        $post_location, $post_address
    )) {
        // reg ok

        $user_info = db_user_get_name($post_name);

        $auth_success = true;
        $auth_user_id = intval($user_info['user_id']);
        $auth_name = $user_info['name'];
        $auth_sudo = intval($user_info['is_admin']) > 0;

        session_set('auth_user_id', $auth_user_id);
        session_set('auth_sudo', $auth_sudo);
    } else {
        // reg fail

        $auth_success = false;
        $auth_user_id = null;
        $auth_name = null;
        $auth_sudo = false;
    }

    echo ajax_gen(
        'auth_success', $auth_success,
        'auth_user_id', $auth_user_id,
        'auth_name', $auth_name,
        'auth_sudo', $auth_sudo
    );
?>
