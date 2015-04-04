<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $post_user_id = ajax_arg('user_id', FILTER_UNSAFE_RAW, null);

    $user_info = db_user_get($post_user_id);

    if ($user_info) {
        echo ajax_gen(
            'get_success', true,

            'user_id', $user_info['user_id'],

            'name', $user_info['name'],
            'location', $user_info['location'],
            'about', $user_info['about']
        );
    } else {
        echo ajax_gen(
            'get_success', false
        );
    }
?>
