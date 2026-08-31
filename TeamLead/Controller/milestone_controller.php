<?php

session_start();

include "../../DB/db.php";
include "../Model/milestone_model.php";
include "../Model/project_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "team_lead") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "create_milestone") {
        $project_id = $_POST["project_id"] ?? "";
        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $due_date = trim($_POST["due_date"] ?? "");
        $status = $_POST["status"] ?? "";
        $is_client_visible = $_POST["is_client_visible"] ?? "";

        if ($project_id == "") {
            $errors[] = "Project is required.";
        }

        if ($title == "") {
            $errors[] = "Milestone title is required.";
        }

        if ($description == "") {
            $errors[] = "Milestone description is required.";
        }

        if ($due_date == "") {
            $errors[] = "Due date is required.";
        } 
        elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $due_date)) {
            $errors[] = "Due date format must be YYYY-MM-DD.";
        }

        $allowed_status = ["pending", "completed"];

        if (!in_array($status, $allowed_status)) {
            $errors[] = "Invalid milestone status.";
        }

        if ($is_client_visible !== "0" && $is_client_visible !== "1") {
            $errors[] = "Invalid client visibility selected.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/milestones.php");
            exit();
        }

        $created = create_milestone(
            $conn,
            $project_id,
            $title,
            $description,
            $due_date,
            $status,
            $is_client_visible
        );

        if ($created) {
            add_activity_log(
                $conn,
                null,
                $project_id,
                $_SESSION["user_id"],
                "milestone_created",
                "Milestone created: " . $title
            );

            $_SESSION["success"] = "Milestone created successfully.";
            header("Location: ../View/milestones.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to create milestone."];
            header("Location: ../View/milestones.php");
            exit();
        }
    }

    if ($action == "mark_completed") {
        $milestone_id = $_POST["milestone_id"] ?? "";
        $team_lead_id = $_SESSION["user_id"];

        if ($milestone_id == "") {
            $_SESSION["errors"] = ["Milestone ID is required."];
            header("Location: ../View/milestones.php");
            exit();
        }

        $milestone = get_milestone_by_id_and_teamlead($conn, $milestone_id, $team_lead_id);

        if (!$milestone) {
            $_SESSION["errors"] = ["Invalid milestone selected."];
            header("Location: ../View/milestones.php");
            exit();
        }

        $updated = mark_milestone_completed($conn, $milestone_id);

        if ($updated) {
            add_activity_log(
                $conn,
                null,
                $milestone["project_id"],
                $_SESSION["user_id"],
                "milestone_completed",
                "Milestone marked as completed: " . $milestone["title"]
            );

            $_SESSION["success"] = "Milestone marked as completed.";
            header("Location: ../View/milestones.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to update milestone."];
            header("Location: ../View/milestones.php");
            exit();
        }
    }
}