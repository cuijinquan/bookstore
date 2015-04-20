<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_user.php';

    // get info of all users
    // args: mode, begin

    $post_mode = ajax_arg('mode', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    switch ($post_mode) {
        case 'new':
            $cond = 'true';
            $order = 'date_create';
            $desc = true;
            break;

        case 'book':
            $cond = 'true';
            $order = 'book_count';
            $desc = true;
            break;

        case 'sold':
            $cond = 'true';
            $order = 'sold_count';
            $desc = true;
            break;

        default:
            header("HTTP/1.1 403 Forbidden");
            die('bad call');
            break;
    }

    $data_all = db_user_list_all(
        $cond, $order, $desc, $post_begin
    );

    $user_data = array();

    while ($user_info = $data_all->fetch_assoc()) {
        $user_data[] = array(
            'user_id' => $user_info['user_id'],

            'name' => $user_info['name'],
            // 'image' => $user_info['image'],
            'detail' => $user_info['detail'],
            'location' => $user_info['location'],

            'book_count' => $user_info['book_count'],
            'sold_count' => $user_info['sold_count']
        );
    }

    echo ajax_gen(
        'data', $user_data
    );
?>
