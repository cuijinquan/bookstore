<?php
    require_once '../util/ajax.php';
    require_once '../util/session.php';

    // upload an image
    // args: file (see: HTTP file upload)

    // notice: allow image uploading in user registration
    // $auth_user_id = session_get_force('auth_user_id');

    if (
        isset($_FILES['file'])
        && $_FILES['file']['size'] <= $upload_limit
        && (
            $_FILES['file']['type'] === 'image/jpeg'
            || $_FILES['file']['type'] === 'image/pjpeg'
            || $_FILES['file']['type'] === 'image/png'
            || $_FILES['file']['type'] === 'image/x-png'
        )
        && $_FILES['file']['error'] === 0
    ) {
        // file accepted

        $upload_content = 'data:image/jpeg;base64,' . base64_encode(
            file_get_contents($_FILES['file']['tmp_name'])
        );
        $upload_hash = hash('md5', $upload_content); // faster hash
        $upload_filename = '../' . $upload_dir . '/' . $upload_hash;

        if (!file_exists($upload_filename)) {
            // add new file

            $upload_success = true;
            $upload_exist = false;
            file_put_contents($upload_filename, $upload_content);
        } else {
            // file already exists

            $upload_success = true;
            $upload_exist = true;
        }
    } else {
        // bad file

        $upload_success = false;
        $upload_exist = false;
        $upload_hash = null;
    }

    echo ajax_gen(
        'upload_success', $upload_success,
        'upload_exist', $upload_exist,
        'upload_hash', $upload_hash
    );
?>
