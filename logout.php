<?php
    // reset and destroy all sessions when logout
    session_start();
    $_SESSION = array();
    session_destroy();
    header("location: index.php");
?>