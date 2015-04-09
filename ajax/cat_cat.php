<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_cat.php';

    // get sub catalogs in a catalog
    // args: cat_id, begin

    $cat_id = ajax_arg('cat_id', FILTER_VALIDATE_REGEXP, $filter_number);
    $cat_begin = ajax_arg('begin', FILTER_VALIDATE_REGEXP, $filter_number);

    $cat_data = array();

    $data_all = db_cat_list_parent($cat_id, $cat_begin);

    while ($cat_info = $data_all->fetch_assoc()) {
        $cat_data[] = array(
            'cat_id'=> $cat_info['cat_id'],

            'name'=> $cat_info['name'],
            'image'=> $cat_info['image'],
            'detail'=> $cat_info['detail']
        );
    }

    echo ajax_gen(
        'data', $cat_data
    );
?>
