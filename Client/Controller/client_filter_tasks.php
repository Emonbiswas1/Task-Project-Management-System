<?php

session_start();

header("Content-Type: application/json");

include "../../DB/db.php";
include "../Model/task_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access.",
        "tasks" => []
    ]);
    exit();
}

$client_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";
$status = $_GET["status"] ?? "";

$allowed_status = ["todo", "in_progress", "review", "done"];

if ($project_id != "" && !is_numeric($project_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid project selected.",
        "tasks" => []
    ]);
    exit();
}

if ($status != "" && !in_array($status, $allowed_status)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid task status.",
        "tasks" => []
    ]);
    exit();
}

$result = get_client_visible_tasks($conn, $client_id, $project_id, $status);

$tasks = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = [
            "id" => $row["id"],
            "project_name" => $row["project_name"],
            "milestone_title" => $row["milestone_title"],
            "title" => $row["title"],
            "description" => $row["description"],
            "assigned_member" => $row["assigned_member"],
            "priority" => $row["priority"],
            "status" => $row["status"],
            "due_date" => $row["due_date"],
            "estimated_hours" => $row["estimated_hours"]
        ];
    }

    echo json_encode([
        "success" => true,
        "tasks" => $tasks
    ]);
    exit();
}

echo json_encode([
    "success" => false,
    "message" => "Failed to load tasks.",
    "tasks" => []
]);
exit();