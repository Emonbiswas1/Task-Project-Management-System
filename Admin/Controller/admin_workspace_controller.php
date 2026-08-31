<?php

session_start();

include "../../DB/db.php";
include "../Model/workspace_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "assign_user_to_workspace") {
    $workspace_id = $_POST["workspace_id"] ?? "";
    $user_id = $_POST["user_id"] ?? "";
    $workspace_role = $_POST["workspace_role"] ?? "";

    if ($workspace_id == "" || !is_numeric($workspace_id)) {
        $errors[] = "Invalid workspace selected.";
    }

    if ($user_id == "" || !is_numeric($user_id)) {
        $errors[] = "Invalid user selected.";
    }

    $allowed_roles = ["lead", "member", "client"];

    if (!in_array($workspace_role, $allowed_roles)) {
        $errors[] = "Invalid workspace role selected.";
    }

    if ($workspace_id != "" && is_numeric($workspace_id)) {
        $workspace = get_workspace_by_id_admin($conn, $workspace_id);

        if (!$workspace) {
            $errors[] = "Workspace not found.";
        }
    } 

    if 