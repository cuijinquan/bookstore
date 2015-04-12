<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // get info of books in a catalog
    // args: cat_id, begin

    $post_cat_id = ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number);
    $post_begin = ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number);

    $data_all = db_book_list_cat($post_cat_id, $post_begin);

    // note: see also list_book.php

    $book_data = array();

    while ($book_info = $data_all->fetch_assoc()) {
        $book_data[] = array(
            'book_id' => $book_info['book_id'],

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
