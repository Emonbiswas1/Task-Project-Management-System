<?php

function check_role($allowed_roles) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["role"])) {
        header("Location: ../../Common/View/login.php");
        exit();
    }

    if (!in_array($_SESSION["role"], $allowed_roles)) {
        echo "<h2>Access Denied</h2>";
        echo "<p>You do not have permission to access this page.</p>";
        echo "<p><a href='../../index.php'>Go back</a></p>";
        exit();
    }
}