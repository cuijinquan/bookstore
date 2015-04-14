<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_cat.php';

    // get info of a catalog
    // args: cat_id

    $post_cat_id = intval(ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number));

    $cat_info = db_cat_get($post_cat_id);

    if ($cat_info) {
        // cat exists

        echo ajax_gen(
            'get_success', true,

            'cat_id', $cat_info['cat_id'],

            'name', $cat_info['name'],
            'image', $cat_info['image'],
            'detail', $cat_info['detail'],

            'cat_count', $cat_info['cat_count'],
            'book_count', $cat_info['book_count']

            // TODO: catalog path?
        );
    } else {
        // cat not found

        echo ajax_gen(
            'get_success', false
        );
    }
?>
