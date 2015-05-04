<?php
    // name of the site
    $site_name = 'Yet Another Bookstore';
    // version of the site (and the AJAX protocol)
    $site_version = '1.0';

    // method(s) of AJAX (get, post or both)
    $ajax_post = $_POST; // $ajax_post = $_REQUEST;
    $ajax_maxlen = 65536;

    // longest session time with no action
    $session_timeout = 60 * 60 * 6;
    // longest possible session time
    $session_create_timeout = 60 * 60 * 24;

    // database config
    $db_ip = '127.0.0.1';
    $db_port = 3306;
    $db_username = 'bookstore';
    $db_password = 'bookstore';
    $db_name = 'bookstore';

    // dir contains uploaded files
    $upload_dir = 'upload';
    // max file size in file uploading
    $upload_limit = 1048576;
?>
