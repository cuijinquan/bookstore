<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // edit user's information
    // common args: login_user_id
    // args: login_name, login_password, mail, name, image, detail, password, location, address

    $auth_user_id = intval(ajax_arg('login_user_id', FILTER_VALIDATE_REGEXP, $filter_number));
    session_check('auth_user_id', $auth_user_id);

    $post_login_name = ajax_arg('login_name', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_login_password = ajax_arg('login_password', FILTER_VALIDATE_REGEXP, $filter_hash);
    $post_mail = ajax_arg('mail', FILTER_VALIDATE_EMAIL, null);
    $post_name = ajax_arg('name', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_image = or_null(ajax_arg('image', FILTER_VALIDATE_REGEXP, $filter_imghash));
    $post_detail = ajax_arg('detail', FILTER_UNSAFE_RAW, null);
    $post_password = ajax_arg('password', FILTER_VALIDATE_REGEXP, $filter_hash);
    $post_location = ajax_arg('location', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_address = ajax_arg('address', FILTER_UNSAFE_RAW, null);

    $auth_salt = session_delete('auth_salt');

    $user_info = db_user_get_name($post_login_name);

    if ($user_info && intval($user_info['user_id']) === $auth_user_id) {
        $auth_password = hash('sha256', $user_info['password'] . $auth_salt);

        if ($post_login_password === $auth_password) {
            // login ok

            $user_info['mail'] = $post_mail;
            $user_info['name'] = $post_name;
            $user_info['image'] = $post_image;
            $user_info['detail'] = $post_detail;
            $user_info['password'] = $post_password;
            $user_info['location'] = $post_location;
            $user_info['address'] = $post_address;

            if (db_user_set($user_info)) {
                // edit ok

                $auth_success = true;
                $auth_user_id = $user_info['user_id'];
                $auth_name = $user_info['name'];
                $auth_sudo = intval($user_info['is_admin']) > 0;
            } else {
                // edit fail

                $auth_success = false;
                $auth_user_id = $user_info['user_id'];
                $auth_name = $user_info['name'];
                $auth_sudo = intval($user_info['is_admin']) > 0;
            }
        } else {
            // wrong password

            $auth_success = false;
            $auth_user_id = null;
            $auth_name = $user_info['name'];
            $auth_sudo = false;
        }
    } else {
        // wrong name

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
