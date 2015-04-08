<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    $post_user_id = ajax_arg('user_id', FILTER_VALIDATE_REGEXP, $filter_number);

    $user_info = db_user_get($post_user_id);

    if ($user_info) {
        echo ajax_gen(
            'get_success', true,

            'user_id', $user_info['user_id'],

            'name', $user_info['name'],
            'image', $user_info['image']
            'detail', $user_info['detail'],
            'location', $user_info['location']
        );
    } else {
        echo ajax_gen(
            'get_success', false
        );
    }
?>
