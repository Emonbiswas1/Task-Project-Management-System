<?php

session_start();

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/workspace_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "team_lead") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "create_project") {
        $workspace_id = $_POST["workspace_id"] ?? "";
        $name = trim($_POST["name"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $client_id = $_POST["client_id"] ?? "";
        $deadline = $_POST["deadline"] ?? "";
        $color_label = trim($_POST["color_label"] ?? "");
        $status = $_POST["status"] ?? "";
        $visibility = $_POST["visibility"] ?? "";

        if ($workspace_id == "") {
            $errors[] = "Workspace is required.";
        }

        if ($name == "") {
            $errors[] = "Project name is required.";
        }

        if ($description == "") {
            $errors[] = "Project description is required.";
        }

        if ($client_id == "") {
            $errors[] = "Client is required.";
        }

        if ($deadline == "") {
            $errors[] = "Deadline is required.";
        }

        if ($color_label == "") {
            $errors[] = "Color label is required.";
        }

        $allowed_status = ["planning", "active", "on_hold", "completed", "archived"];
        if (!in_array($status, $allowed_status)) {
            $errors[] = "Invalid project status.";
        }

        $allowed_visibility = ["internal", "client_visible"];
        if (!in_array($visibility, $allowed_visibility)) {
            $errors[] = "Invalid project visibility.";
        }

        $owner_id = $_SESSION["user_id"];
        $workspace = get_workspace_by_id_and_owner($conn, $workspace_id, $owner_id);

        if (!$workspace) {
            $errors[] = "Invalid workspace selected.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/create_project.php");
            exit();
        }

        $created = create_project($conn, $workspace_id, $name, $description, $client_id, $deadline, $color_label, $status, $visibility);

        if ($created) {
            add_activity_log(
                $conn,
                $workspace_id,
                null,
                $_SESSION["user_id"],
                "project_created",
                "Project created: " . $name
            );

            $_SESSION["success"] = "Project created successfully.";
            header("Location: ../View/projects.php");
            exit();
        }
        else {
            $_SESSION["errors"] = ["Failed to create project."];
            header("Location: ../View/create_project.php");
            exit();
        }
    }

        if ($action == "update_project") {
        $project_id = $_POST["project_id"] ?? "";
        $workspace_id = $_POST["workspace_id"] ?? "";
        $name = trim($_POST["name"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $client_id = $_POST["client_id"] ?? "";
        $deadline = $_POST["deadline"] ?? "";
        $color_label = trim($_POST["color_label"] ?? "");
        $status = $_POST["status"] ?? "";
        $visibility = $_POST["visibility"] ?? "";
        $team_lead_id = $_SESSION["user_id"];

        if ($project_id == "" || !is_numeric($project_id)) {
            $errors[] = "Invalid project selected.";
        }

        if ($workspace_id == "") {
            $errors[] = "Workspace is required.";
        }

        if ($name == "") {
            $errors[] = "Project name is required.";
        }

        if ($description == "") {
            $errors[] = "Project description is required.";
        }

        if ($client_id == "") {
            $errors[] = "Client is required.";
        }

        if ($deadline == "") {
            $errors[] = "Deadline is required.";
        } 
        elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $deadline)) {
            $errors[] = "Deadline format must be YYYY-MM-DD.";
        }

        if ($color_label == "") {
            $errors[] = "Color label is required.";
        }

        $allowed_status = ["planning", "active", "on_hold", "completed", "archived"];

        if (!in_array($status, $allowed_status)) {
            $errors[] = "Invalid project status.";
        }

        $allowed_visibility = ["internal", "client_visible"];

        if (!in_array($visibility, $allowed_visibility)) {
            $errors[] = "Invalid project visibility.";
        }

        $workspace = get_workspace_by_id_and_owner($conn, $workspace_id, $team_lead_id);

        if (!$workspace) {
            $errors[] = "Invalid workspace selected.";
        }

        $project = get_project_by_id_and_teamlead($conn, $project_id, $team_lead_id);

        if (!$project) {
            $errors[] = "Project not found or access denied.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/edit_project.php?project_id=" . $project_id);
            exit();
        }

        $updated = update_project_by_teamlead(
            $conn,
            $project_id,
            $workspace_id,
            $name,
            $description,
            $client_id,
            $deadline,
            $color_label,
            $status,
            $visibility,
            $team_lead_id
        );

        if ($updated) {
            if (function_exists("add_activity_log")) {
                add_activity_log(
                    $conn,
                    $workspace_id,
                    $project_id,
                    $_SESSION["user_id"],
                    "project_updated",
                    "Project updated: " . $name
                );
            }

            $_SESSION["success"] = "Project updated successfully.";
            header("Location: ../View/projects.php");
            exit();
        } else {
            $_SESSION["errors"] = ["Failed to update project."];
            header("Location: ../View/edit_project.php?project_id=" . $project_id);
            exit();
        }
    }

    if ($action == "archive_project") {
        $project_id = $_POST["project_id"] ?? "";
        $team_lead_id = $_SESSION["user_id"];

        if ($project_id == "" || !is_numeric($project_id)) {
            $_SESSION["errors"] = ["Invalid project selected."];
            header("Location: ../View/projects.php");
            exit();
        }

        $project = get_project_by_id_and_teamlead($conn, $project_id, $team_lead_id);

        if (!$project) {
            $_SESSION["errors"] = ["Project not found or access denied."];
            header("Location: ../View/projects.php");
            exit();
        }

        if ($project["status"] != "completed") {
            $_SESSION["errors"] = ["Only completed projects can be archived."];
            header("Location: ../View/projects.php");
            exit();
        }

        $archived = archive_project_by_teamlead($conn, $project_id, $team_lead_id);

        if ($archived) {
            if (function_exists("add_activity_log")) {
                add_activity_log(
                    $conn,
                    $project["workspace_id"],
                    $project_id,
                    $_SESSION["user_id"],
                    "project_archived",
                    "Project archived: " . $project["name"]
                );
            }

            $_SESSION["success"] = "Project archived successfully.";
            header("Location: ../View/projects.php");
            exit();
        } else {
            $_SESSION["errors"] = ["Failed to archive project."];
            header("Location: ../View/projects.php");
            exit();
        }
    }
}