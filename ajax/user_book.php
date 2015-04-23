<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // get info of books owned by a user
    // args: user_id, begin

    $post_user_id = intval(ajax_arg('user_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    $data_all = db_book_list_owner($post_user_id, $post_begin);

    // note: see also list_book.php, cat_book.php

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
