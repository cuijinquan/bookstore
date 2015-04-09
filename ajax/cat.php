<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_cat.php';

    // get info of a catalog
    // args: cat_id

    $cat_id = ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number);

    $cat_info = db_cat_get($cat_id);

    if ($cat_info) {
        // cat exists

        echo ajax_gen(
            'get_success', true,

            'cat_id', $cat_info['cat_id'],

            'name', $cat_info['name'],
            'image', $cat_info['image'],
            'detail', $cat_info['detail']

            // TODO: catalog path?
        );
    } else {
        // cat not found

        echo ajax_gen(
            'get_success', false
        );
    }
?>
