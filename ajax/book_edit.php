<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // edit book's information
    // common args: login_user_id
    // args: book_id, image, detail, price, inventory

    $auth_user_id = intval(ajax_arg('login_user_id', FILTER_VALIDATE_REGEXP, $filter_number));
    session_check('auth_user_id', $auth_user_id);

    $post_book_id = intval(ajax_arg('book_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_image = or_null(ajax_arg('image', FILTER_VALIDATE_REGEXP, $filter_imghash));
    $post_detail = ajax_arg('detail', FILTER_UNSAFE_RAW, null);
    $post_price = ajax_arg('price', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_inventory = intval(ajax_arg('inventory', FILTER_VALIDATE_REGEXP, $filter_number));

    $book_info = db_book_get($post_book_id);

    if ($book_info && intval($book_info['owner_user_id']) === $auth_user_id) {
        // owner_user_id, parent_cat_id, name
        // $book_info['name'] = $post_name;
        $book_info['image'] = $post_image;
        $book_info['detail'] = $post_detail;
        $book_info['price'] = $post_price;
        $book_info['inventory'] = $post_inventory;

        if (db_book_set($book_info)) {
            // edit ok

            $set_success = true;
            $book_id = $book_info['book_id'];
        } else {
            // edit fail

            $set_success = false;
            $book_id = $book_info['book_id'];
        }
    } else {
        // book not found

        $set_success = false;
        $book_id = null;
    }

    echo ajax_gen(
        'set_success', $set_success,
        'book_id', $book_id
    );
?>
