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

$task = null;

        if ($task_id != "" && is_numeric($task_id)) {
            $task = get_member_task_by_id($conn, $task_id, $member_id);

            if (!$task) {
                $errors[] = "Task not found or access denied.";
            }
        }

        if (!isset($_FILES["attachment_file"]) || $_FILES["attachment_file"]["name"] == "") {
            $errors[] = "Attachment file is required.";
        }

        $file_name = "";
        $file_tmp = "";
        $file_size = 0;
        $file_error = 0;
        $file_extension = "";
        $allowed_extensions = ["jpg", "jpeg", "png", "pdf", "doc", "docx", "txt", "zip"];

        if (isset($_FILES["attachment_file"]) && $_FILES["attachment_file"]["name"] != "") {
            $file_name = $_FILES["attachment_file"]["name"];
            $file_tmp = $_FILES["attachment_file"]["tmp_name"];
            $file_size = $_FILES["attachment_file"]["size"];
            $file_error = $_FILES["attachment_file"]["error"];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($file_error !== 0) {
                $errors[] = "File upload failed.";
            }

            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = "Only JPG, JPEG, PNG, PDF, DOC, DOCX, TXT, and ZIP files are allowed.";
            }

            if ($file_size > 10 * 1024 * 1024) {
                $errors[] = "File size must be less than 10MB.";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/task_detail.php?task_id=" . $task_id);
            exit();
        }

        $upload_folder = "../assets/uploads/task_attachments/";

        if (!is_dir($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        $safe_file_name = "task_" . $task_id . "_user_" . $member_id . "_" . time() . "." . $file_extension;
        $destination = $upload_folder . $safe_file_name;

        if (move_uploaded_file($file_tmp, $destination)) {
            $file_path = "assets/uploads/task_attachments/" . $safe_file_name;

            $uploaded = add_task_attachment(
              $conn,
              $task_id,
              $member_id,
              $file_name,
              $file_path,
              $file_size,
              $is_client_visible
            );

            if ($uploaded) {
                if (function_exists("add_activity_log")) {
                    add_activity_log(
                        $conn,
                        null,
                        $task["project_id"],
                        $member_id,
                        "task_attachment_uploaded",
                        "Member uploaded attachment for task: " . $task["title"]
                    );
                }

                $_SESSION["success"] = "Attachment uploaded successfully.";
                header("Location: ../View/task_detail.php?task_id=" . $task_id);
                exit();
            } 
            else {
                $_SESSION["errors"] = ["Failed to save attachment information."];
                header("Location: ../View/task_detail.php?task_id=" . $task_id);
                exit();
            }
        } 
        else {
            $_SESSION["errors"] = ["Failed to upload file."];
            header("Location: ../View/task_detail.php?task_id=" . $task_id);
            exit();
        }
    }
}

header("Location: ../View/tasks.php");
exit();