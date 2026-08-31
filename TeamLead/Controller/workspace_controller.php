<?php

session_start();

include "../../DB/db.php";
include "../Model/workspace_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "team_lead") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "create_workspace") {
        $name = trim($_POST["name"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $plan = $_POST["plan"] ?? "free";
        $owner_id = $_SESSION["user_id"];

        if ($name == "") {
            $errors[] = "Workspace name is required.";
        }

        if ($description == "") {
            $errors[] = "Workspace description is required.";
        }

        $allowed_plans = ["free", "pro"];

        if (!in_array($plan, $allowed_plans)) {
            $errors[] = "Invalid workspace plan.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/workspaces.php");
            exit();
        }

        $invite_code = "WS-" . strtoupper(substr(md5(time() . $name . rand()), 0, 8));

        $workspace_id = create_workspace($conn, $name, $description, $owner_id, $invite_code, $plan);

        if ($workspace_id) {
            add_workspace_member($conn, $workspace_id, $owner_id, "lead");

            add_activity_log(
                $conn,
                $workspace_id,
                null,
                $owner_id,
                "workspace_created",
                "Workspace created: " . $name
            );

            $_SESSION["success"] = "Workspace created successfully. Invite Code: " . $invite_code;
            header("Location: ../View/workspaces.php");
            exit();
        }
        else {
            $_SESSION["errors"] = ["Failed to create workspace."];
            header("Location: ../View/workspaces.php");
            exit();
        }
    }
}