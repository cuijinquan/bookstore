<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // get info of a book
    // args: book_id

    $book_id = ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number);

    $book_info = db_book_get($book_id);

    if ($book_info) {
        // book exists

        echo ajax_gen(
            'get_success', true,

            'book_id', $book_info['book_id'],

            'name', $book_info['name'],
            'image', $book_info['image'],
            'detail', $book_info['detail'],
            'price', $book_info['price'],
            'inventory', $book_info['inventory'],

            'date_create', $book_info['date_create']

            // TODO: order history & comments?
            // TODO: catalog path?
        );
    } else {
        // book not found

        echo ajax_gen(
            'get_success', false
        );
    }
?>
