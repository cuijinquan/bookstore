<?php
    require_once 'config.php';

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
