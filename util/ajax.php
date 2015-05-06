<?php
    require_once 'config.php';

    // regexps to filters (see: filter_var)
    function filter_wrap($regexp) {
        return array('options' => array('regexp' => $regexp));
    }

    $filter_number = filter_wrap('/^[0-9]+$/isAD');
    $filter_text = filter_wrap('/^[^\x{00}-\x{1f}\x{7f}]+$/isAD');
    $filter_hash = filter_wrap('/^[0-9A-F]{64}$/isAD');
    $filter_imghash = filter_wrap('/^[0-9A-F]{64}|$/isAD');

    // convert empty string to null
    // used on image links
    function or_null($value) {
        if ($value !== '') {
            return $value;
        } else {
            return null;
        }
    }

    // throw an ajax error
    function ajax_err() {
        header('HTTP/1.1 400 Bad Request');
        die('ajax error');
    }

    // read arguments in the HTTP request
    function ajax_arg($key, $filter, $options) {
        global $ajax_post;
        global $ajax_maxlen;

        if (isset($ajax_post[$key])) {
            $value = filter_var($ajax_post[$key], $filter, $options);
        } else {
            $value = false;
        }

        if ($value !== false && strlen($value) <= $ajax_maxlen) {
            return $value;
        } else {
            // argument illegal or not exists
            ajax_err();
        }
    }

    // generate JSON string
    function ajax_gen() {
        global $site_name;
        global $site_version;

        $content = array(
            // 'site_name' => $site_name,
            'site_version' => $site_version
        );

        for ($i = 0; $i < func_num_args(); $i += 2) {
            $content[func_get_arg($i)] = func_get_arg($i + 1);
        }

        return json_encode($content);
    }
?>
