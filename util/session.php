<?php
    require_once 'config.php';

    session_start();

    if (
        (!isset($_SESSION['session_time']))
        || (!isset($_SESSION['session_create']))
        || (time() - $_SESSION['session_time'] >= $session_timeout)
        || (time() - $_SESSION['session_create'] >= $session_create_timeout)
    ) {
        // reset session
        session_destroy();
        session_start();

        $_SESSION['session_create'] = time();
    }

    $_SESSION['session_time'] = time();

    function session_get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return null;
        }
    }

    function session_set($key, $value) {
        $_SESSION[$key] = $value;
    }

    function session_delete($key) {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
        } else {
            $value = null;
        }

        return $value;
    }

    // $_SESSION['test'] = $_SESSION['test'] . '.';
    // echo $_SESSION['session_time'] . ' ';
    // echo $_SESSION['session_create'] . ' ';
    // echo $_SESSION['test'];
?>
