<?php

session_start();

header("Content-Type: application/json");

include "../../DB/db.php";
include "../Model/task_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "member") {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit();
}
$task_id = $_POST["task_id"] ?? "";
$status = $_POST["status"] ?? "";
$member_id = $_SESSION["user_id"];

$allowed_status = ["todo", "in_progress", "review", "done"];

if ($task_id == "" || !is_numeric($task_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid task selected."
    ]);
    exit();
}

if (!in_array($status, $allowed_status)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid task status."
    ]);
    exit();
}

$task = get_member_task_by_id($conn, $task_id, $member_id);

if (!$task) {
    echo json_encode([
        "success" => false,
        "message" => "Task not found or access denied."
    ]);
    exit();
}

$updated = update_task_status_by_member($conn, $task_id, $status, $member_id);

if ($updated) {
    if (function_exists("add_activity_log")) {
        add_activity_log(
            $conn,
            null,
            $task["project_id"],
            $member_id,
            "member_task_status_updated",
            "Member updated task status: " . $task["title"] . " to " . $status
        );
    }

    echo json_encode([
        "success" => true,
        "message" => "Task status updated successfully."
    ]);
    exit();
}

echo json_encode([
    "success" => false,
    "message" => "Failed to update task status."
]);
exit();