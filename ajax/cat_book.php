<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    $cat_id = ajax_arg('cat_id');
    $cat_begin = ajax_arg('begin');

    $cat_data = array();

    $data_all = db_book_list_cat($cat_id, $cat_begin);

    while ($book_info = $data_all->fetch_assoc()) {
        $cat_data[] = array(
            'book_id'=> $book_info['book_id'],
            'name'=> $book_info['name'],
            'detail'=> $book_info['detail']
        );
    }

    echo ajax_gen(
        'data', $cat_data
    );
?>
