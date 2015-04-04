<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    $book_id = ajax_arg('book_id', FILTER_UNSAFE_RAW, null);
    $book_begin = ajax_arg('begin', FILTER_UNSAFE_RAW, null);

    $book_data = array();

    $data_all = db_book_list_cat($book_id, $book_begin);

    while ($book_info = $data_all->fetch_assoc()) {
        $book_data[] = array(
            'book_id'=> $book_info['book_id'],
            'name'=> $book_info['name'],
            'detail'=> $book_info['detail'],
            'price'=> $book_info['price'],
            'sold'=> $book_info['sold'],
            'inventory'=> $book_info['inventory']
        );
    }

    echo ajax_gen(
        'data', $book_data
    );
?>
