<?php

    session_start();

    if (!isset($_SESSION['user'])) {
        header('Location: /booru/login.php');
        exit();
    }

?>