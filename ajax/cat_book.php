<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // get info of book in a catalog
    // args: book_id, begin

    $book_id = ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number);
    $book_begin = ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number);

    $book_data = array();

    $data_all = db_book_list_cat($book_id, $book_begin);

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
