<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';

    $post_image = ajax_arg('image', FILTER_VALIDATE_REGEXP, $filter_text);

    $image_content = file_get_contents('../img/' . $post_image . '.jpg');

    if ($image_content !== false) {
        echo ajax_gen(
            'image_success', true,
            'image_content', 'data:image/jpeg;base64,' . base64_encode($image_content)
        );
    } else {
        echo ajax_gen(
            'image_success', false
        );
    }
?>
