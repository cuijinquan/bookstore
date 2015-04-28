<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // get info of all books
    // args: mode, begin

    $post_mode = ajax_arg('mode', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    switch ($post_mode) {
        case 'new':
            $cond = 'true';
            $order = 'date_create';
            $desc = true;
            break;

        case 'sold':
            $cond = 'true';
            $order = 'sold_count';
            $desc = true;
            break;

        case 'newsold':
            $cond = 'to_days(now()) - to_days(date_create) <= 30';
            $order = 'sold_count';
            $desc = true;
            break;

        default:
            ajax_err();
            break;
    }

    $data_all = db_book_list_all(
        $cond, $order, $desc, $post_begin
    );

    // note: see also user_book.php, cat_book.php

    $book_data = array();

    while ($book_info = $data_all->fetch_assoc()) {
        $book_data[] = array(
            'book_id' => $book_info['book_id'],
            // 'owner_user_id' => intval($book_info['owner_user_id']),
            // 'parent_cat_id' => intval($book_info['parent_cat_id']),

            'name' => $book_info['name'],
            // 'image' => $book_info['image'],
            'detail' => $book_info['detail'],
            'price' => $book_info['price'],
            'inventory' => $book_info['inventory'],

            'sold_count' => $book_info['sold_count']

            // 'date_create' => $book_info['date_create']
        );
    }

    echo ajax_gen(
        'data', $book_data
    );
?>
