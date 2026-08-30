<?php
session_start();

if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == "member") {
        header("Location: Member/View/dashboard.php");
        exit();
    } 
    elseif ($_SESSION["role"] == "team_lead") {
        header("Location: TeamLead/View/dashboard.php");
        exit();
    } 
    elseif ($_SESSION["role"] == "client") {
        header("Location: Client/View/dashboard.php");
        exit();
    } 
    elseif ($_SESSION["role"] == "admin") {
        header("Location: Admin/View/dashboard.php");
        exit();
    }
}

header("Location: Common/View/login.php");
exit();