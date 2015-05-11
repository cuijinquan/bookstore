<?php
    require_once 'config.php';

    // set up the PHP session

    session_start();

    if (
        (!isset($_SESSION['session_time']))
        || (!isset($_SESSION['session_create']))
        || (time() - $_SESSION['session_time'] >= $session_timeout)
        || (time() - $_SESSION['session_create'] >= $session_create_timeout)
    ) {
        // expired

        // reset the session
        session_destroy();
        session_start();

        $_SESSION['session_create'] = time();
    }

    $_SESSION['session_time'] = time();

    // throw a session error
    function session_err() {
        header('HTTP/1.1 401 Unauthorized');
        die('session error');
    }

    // get value in the session data
    function session_get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return null;
        }
    }

    // get value in the session data or error
    function session_get_force($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            // value not exists
            session_err();
        }
    }

    // set value in the session data
    function session_set($key, $value) {
        $_SESSION[$key] = $value;
    }

    // delete value in the session data
    function session_delete($key) {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
        } else {
            // value not exists
            session_err();
        }

        return $value;
    }
?>
