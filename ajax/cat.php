<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_cat.php';

    $cat_id = ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number);

    $cat_info = db_cat_get($cat_id);

    if ($cat_info) {
        echo ajax_gen(
            'get_success', true,

            'cat_id', $cat_info['cat_id'],

            'name', $cat_info['name'],
            'image', $cat_info['image'],
            'detail', $cat_info['detail']

            // TODO: catalog path?
        );
    } else {
        echo ajax_gen(
            'get_success', false
        );
    }
?>
