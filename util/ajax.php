<?php
    require_once 'config.php';

    function get_arg($key) {
        global $post;

        if (isset($post[$key])) {
            return $post[$key];
        } else {
            // return null;
            header("HTTP/1.1 403 Forbidden");
            die('bad call');
        }
    }

    function gen_ajax() {
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
