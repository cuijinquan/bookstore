<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';
    require_once '../util/db_cat.php';

    $cat_id = ajax_arg('cat_id', FILTER_UNSAFE_RAW, null);
    $cat_begin = ajax_arg('begin', FILTER_UNSAFE_RAW, null);

    $cat_data = array();

    $data_all = db_cat_list_parent($cat_id, $cat_begin);

    while ($cat_info = $data_all->fetch_assoc()) {
        $cat_data[] = array(
            'cat_id'=> $cat_info['cat_id'],
            'name'=> $cat_info['name'],
            'detail'=> $cat_info['detail']
        );
    }

    echo ajax_gen(
        'data', $cat_data
    );
?>
