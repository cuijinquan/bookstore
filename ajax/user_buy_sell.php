<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_buy.php';

    // get info of own orders (buying or selling)
    // args: mode, begin

    $post_mode = ajax_arg('mode', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    $auth_user_id = session_get('auth_user_id');

    if ($auth_user_id !== null)
        switch ($post_mode) {
            case 'new':
                $get_buying = true;
                $list_mode = '???';
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
                'buyer_user_id' => intval($buy_info['buyer_user_id']),
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
            'get_success', true,
            'data', $book_data
        );
    } else {
        // not logged in

        echo ajax_gen(
            'get_success', false
        );
    }
?>
