<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_book.php';

    // add a book
    // args: cat_id, name, image, detail, price, inventory

    $post_cat_id = intval(ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_name = ajax_arg('name', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_image = or_null(ajax_arg('image', FILTER_VALIDATE_REGEXP, $filter_imghash));
    $post_detail = ajax_arg('detail', FILTER_UNSAFE_RAW, null);
    $post_price = ajax_arg('price', FILTER_VALIDATE_REGEXP, $filter_text);
    $post_inventory = intval(ajax_arg('inventory', FILTER_VALIDATE_REGEXP, $filter_number));

    $auth_user_id = session_get_force('auth_user_id');

    if (db_book_add(
        $auth_user_id, $post_cat_id,
        $post_name, $post_image, $post_detail, $post_price, $post_inventory
    )) {
        // add ok

        $book_info = db_book_get_name($auth_user_id, $post_name);

        $set_success = true;
        $book_id = $book_info['book_id'];
    } else {
        // add fail

        $set_success = false;
        $book_id = null;
    }

    echo ajax_gen(
        'set_success', $set_success,
        'book_id', $book_id
    );
?>
