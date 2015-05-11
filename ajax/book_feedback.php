<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // get comments (feedbacks) of a book
    // common args: n/a
    // args: book_id, begin

    $post_book_id = intval(ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    $data_all = db_buy_list_book($post_book_id, 'd', $post_begin);

    $buy_data = array();

    while ($buy_info = $data_all->fetch_assoc()) {
        $buy_data[] = array(
            // 'buy_id' => $buy_info['buy_id'],
            // 'buyer_user_id' => intval($buy_info['buyer_user_id']),

            // 'buy_book_id' => intval($buy_info['buy_book_id']),
            // 'seller_user_id' => $buy_info['seller_user_id'],
            // 'book_name' => $buy_info['book_name'],

            // 'address' => $buy_info['address'],
            'feedback' => $buy_info['feedback'],

            // 'date_create' => $buy_info['date_create'],
            // 'date_accept' => $buy_info['date_accept'],
            'date_done' => $buy_info['date_done']
        );
    }

    echo ajax_gen(
        'data', $buy_data
    );
?>
