<?php
    require_once 'config.php';

    function filter_wrap($regexp) {
        return array('options' => array('regexp' => $regexp));
    }

    $filter_number = filter_wrap('/^[0-9]+$/isAD');
    $filter_text = filter_wrap('/^[^\x{00}-\x{1f}\x{7f}]+$/isAD');
    $filter_hash = filter_wrap('/^[0-9A-F]{64}$/isAD');

    function ajax_arg($key, $filter, $options) {
        global $ajax_post;

        if (isset($ajax_post[$key])) {
            $value = filter_var($ajax_post[$key], $filter, $options);
        } else {
            $value = false;
        }

        if ($value !== false) {
            return $value;
        } else {
            // return null;
            header("HTTP/1.1 403 Forbidden");
            die('bad call');
        }
    }

    function ajax_gen() {
        global $site_name;
        global $site_version;

        $content = array(
            'site_name' => $site_name,
            'site_version' => $site_version
        );

        for ($i = 0; $i < func_num_args(); $i += 2) {
            $content[func_get_arg($i)] = func_get_arg($i + 1);
        }

        return json_encode($content);
    }
?>
