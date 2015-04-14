<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_cat.php';

    // get sub catalogs in a catalog
    // args: cat_id, begin

    $post_cat_id = intval(ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number));
    $post_begin = intval(ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number));

    $data_all = db_cat_list_parent($post_cat_id, $post_begin);

    $cat_data = array();

    while ($cat_info = $data_all->fetch_assoc()) {
        $cat_data[] = array(
            'cat_id' => $cat_info['cat_id'],

            'name' => $cat_info['name'],
            // 'image' => $cat_info['image'],
            'detail' => $cat_info['detail']

            // 'cat_count' => $cat_info['cat_count'],
            // 'book_count' => $cat_info['book_count']
        );
    }

    echo ajax_gen(
        'data', $cat_data
    );
?>
