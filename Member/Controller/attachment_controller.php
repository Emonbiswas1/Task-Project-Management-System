<?php

session_start();

include "../../DB/db.php";
include "../Model/attachment_model.php";
include "../Model/task_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "member") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "upload_attachment") {
        $task_id = $_POST["task_id"] ?? "";
        $is_client_visible = $_POST["is_client_visible"] ?? "";
        $member_id = $_SESSION["user_id"];

        if ($task_id == "" || !is_numeric($task_id)) {
            $errors[] = "Invalid task selected.";
        }

        if ($is_client_visible !== "0" && $is_client_visible !== "1") {
            $errors[] = "Invalid file visibility selected.";
        }

