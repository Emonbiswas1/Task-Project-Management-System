<?php

session_start();

include "../../DB/db.php";
include "../Model/time_log_model.php";
include "../Model/task_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "member") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "add_time_log") {
        $task_id = $_POST["task_id"] ?? "";
        $hours_logged = trim($_POST["hours_logged"] ?? "");
        $note = trim($_POST["note"] ?? "");
        $member_id = $_SESSION["user_id"];

        if ($task_id == "" || !is_numeric($task_id)) {
            $errors[] = "Invalid task selected.";
        }

        if ($hours_logged == "") {
            $errors[] = "Hours logged is required.";
        } elseif (!is_numeric($hours_logged) || $hours_logged <= 0) {
            $errors[] = "Hours logged must be a positive number.";
        }

        if ($note == "") {
            $errors[] = "Note is required.";
        }

 $task = null;

        if ($task_id != "" && is_numeric($task_id)) {
            $task = get_member_task_by_id($conn, $task_id, $member_id);

            if (!$task) {
                $errors[] = "Task not found or access denied.";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/task_detail.php?task_id=" . $task_id);
            exit();
        }

        $created = add_time_log($conn, $task_id, $member_id, $hours_logged, $note);

        if ($created) {
            if (function_exists("add_activity_log")) {
                add_activity_log(
                    $conn,
                    null,
                    $task["project_id"],
                    $member_id,
                    "time_logged",
                    "Member logged " . $hours_logged . " hour(s) on task: " . $task["title"]
                );
            }

            $_SESSION["success"] = "Time log added successfully.";
            header("Location: ../View/task_detail.php?task_id=" . $task_id);
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to add time log."];
            header("Location: ../View/task_detail.php?task_id=" . $task_id);
            exit();
        }
    }
}

header("Location: ../View/tasks.php");
exit();