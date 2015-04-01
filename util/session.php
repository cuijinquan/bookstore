<?php
    session_start();

    if (isset($_SESSION['session_time'])) {
        if (time() - $_SESSION['session_time'] > 10) {
            // timeout
            // reset session
            session_destroy();
            session_start();
        }
    }

    $_SESSION['session_time'] = time();

    // $_SESSION['test'] = $_SESSION['test'] . '.';
    // echo $_SESSION['session_time'];
    // echo $_SESSION['test'];
?>
