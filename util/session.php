<?php
    session_start();

    if (
        (!isset($_SESSION['session_time']))
        || (!isset($_SESSION['session_create']))
        || (time() - $_SESSION['session_time'] > 60 * 60)
        || (time() - $_SESSION['session_create'] > 60 * 60 * 24)
    ) {
        // reset session
        session_destroy();
        session_start();

        $_SESSION['session_create'] = time();
    }

    $_SESSION['session_time'] = time();

    // $_SESSION['test'] = $_SESSION['test'] . '.';
    // echo $_SESSION['session_time'] . ' ';
    // echo $_SESSION['session_create'] . ' ';
    // echo $_SESSION['test'];
?>
