<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    $book_id = ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number);

    $book_info = db_book_get($book_id);

    if ($book_info) {
        echo ajax_gen(
            'get_success', true,

            'book_id', $book_info['book_id'],

            'name', $book_info['name'],
            'image', $book_info['image'],
            'detail', $book_info['detail'],
            'price', $book_info['price'],
            'sold', $book_info['sold'],
            'inventory', $book_info['inventory'],

            'date_add', $book_info['date_add']

            // TODO: order history & comments?
            // TODO: catalog path?
        );
    } else {
        echo ajax_gen(
            'get_success', false
        );
    }
?>
