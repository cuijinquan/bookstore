<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // get info of own orders (buying or selling)
    // args: mode, begin

    $post_mode = ajax_arg('mode', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    $auth_user_id = session_get_force('auth_user_id');

    switch ($post_mode) {
        case 'b_d':
            $get_buying = true;
            $list_mode = 'd';
            break;

        case 'b_ca':
            $get_buying = true;
            $list_mode = 'ca';
            break;

        case 'b_all':
            $get_buying = true;
            $list_mode = 'cad';
            break;

        case 's_c':
            $get_buying = false;
            $list_mode = 'c';
            break;

        case 's_a':
            $get_buying = false;
            $list_mode = 'a';
            break;

        case 's_d':
            $get_buying = false;
            $list_mode = 'd';
            break;

        case 's_ca':
            $get_buying = false;
            $list_mode = 'ca';
            break;

        case 's_ad':
            $get_buying = false;
            $list_mode = 'ad';
            break;

        case 's_all':
            $get_buying = false;
            $list_mode = 'cad';
            break;

        default:
            header("HTTP/1.1 403 Forbidden");
            die('bad call');
            break;
    }

    if ($get_buying) {
        $data_all = db_buy_list_buyer(
            $auth_user_id, $list_mode, $post_begin
        );
    } else {
        $data_all = db_buy_list_seller(
            $auth_user_id, $list_mode, $post_begin
        );
    }

    $buy_data = array();

    while ($buy_info = $data_all->fetch_assoc()) {
        $buy_data[] = array(
            'buy_id' => $buy_info['buy_id'],
            // 'buyer_user_id' => intval($buy_info['buyer_user_id']),
            'buy_book_id' => intval($buy_info['buy_book_id']),

            'seller_user_id' => $buy_info['seller_user_id'],
            'book_name' => $buy_info['book_name'],

            'address' => $buy_info['address'],
            // 'feedback' => $buy_info['feedback'],

            // 'date_create' => $buy_info['date_create'],
            'bool_accept' => ($buy_info['date_accept'] !== null),
            'bool_done' => ($buy_info['date_done'] !== null)
        );
    }

    echo ajax_gen(
        'data', $buy_data
    );
?>
