<?php

session_start();

header("Content-Type: application/json");

include "../../DB/db.php";
include "../Model/task_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "team_lead") {
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

$allowed_status = ["todo", "in_progress", "review", "done"];

if ($task_id == "" || !is_numeric($task_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid task ID."
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

$team_lead_id = $_SESSION["user_id"];

$updated = update_task_status_by_teamlead($conn, $task_id, $status, $team_lead_id);

if ($updated) {
    add_activity_log(
        $conn,
        null,
        null,
        $_SESSION["user_id"],
        "task_status_updated",
        "Task ID " . $task_id . " status updated to " . $status
    );

    echo json_encode([
        "success" => true,
        "message" => "Task status updated successfully."
    ]);
    exit();
}
else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update task status."
    ]);
    exit();
}