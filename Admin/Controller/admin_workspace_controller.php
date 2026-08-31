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

    if ($workspace_id != "" && is_numeric($workspace_id) && $user_id != "" && is_numeric($user_id)) {
        $existing_member = check_workspace_member_exists($conn, $workspace_id, $user_id);

        if ($existing_member) {
            $errors[] = "This user is already added to this workspace.";
        }
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        header("Location: ../View/workspace_user_assign.php");
        exit();
    }

    $added = add_workspace_member_by_admin($conn, $workspace_id, $user_id, $workspace_role);

    if ($added) {
        if (function_exists("add_activity_log")) {
            add_activity_log(
                $conn,
                $workspace_id,
                null,
                $_SESSION["user_id"],
                "workspace_user_assigned",
                "Admin assigned user ID " . $user_id . " to workspace ID " . $workspace_id . " as " . $workspace_role
            );
        }

        $_SESSION["success"] = "User assigned to workspace successfully.";
        header("Location: ../View/workspace_user_assign.php");
        exit();
    } 
    else {
        $_SESSION["errors"] = ["Failed to assign user to workspace."];
        header("Location: ../View/workspace_user_assign.php");
        exit();
    }
}

    if ($action == "change_workspace_status") {
        $workspace_id = $_POST["workspace_id"] ?? "";
        $is_active = $_POST["is_active"] ?? "";

        if ($workspace_id == "" || !is_numeric($workspace_id)) {
            $errors[] = "Invalid workspace selected.";
        }

        if ($is_active !== "0" && $is_active !== "1") {
            $errors[] = "Invalid workspace status selected.";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/workspaces.php");
            exit();
        }

        $workspace = get_workspace_by_id_admin($conn, $workspace_id);

        if (!$workspace) {
            $_SESSION["errors"] = ["Workspace not found."];
            header("Location: ../View/workspaces.php");
            exit();
        }

        $updated = update_workspace_status_admin($conn, $workspace_id, $is_active);

        if ($updated) {
            $_SESSION["success"] = "Workspace status updated successfully.";
        } 
        else {
            $_SESSION["errors"] = ["Failed to update workspace status."];
        }

        header("Location: ../View/workspaces.php");
        exit();
    }

    if ($action == "delete_workspace") {
        $workspace_id = $_POST["workspace_id"] ?? "";

        if ($workspace_id == "" || !is_numeric($workspace_id)) {
            $_SESSION["errors"] = ["Invalid workspace selected."];
            header("Location: ../View/workspaces.php");
            exit();
        }

        $workspace = get_workspace_by_id_admin($conn, $workspace_id);

        if (!$workspace) {
            $_SESSION["errors"] = ["Workspace not found."];
            header("Location: ../View/workspaces.php");
            exit();
        }

        $deleted = delete_workspace_admin($conn, $workspace_id);

        if ($deleted) {
            $_SESSION["success"] = "Workspace deleted successfully.";
        } 
        else {
            $_SESSION["errors"] = ["Failed to delete workspace."];
        }

        header("Location: ../View/workspaces.php");
        exit();
    }

    if ($action == "remove_workspace_member") {
        $workspace_member_id = $_POST["workspace_member_id"] ?? "";
        $workspace_id = $_POST["workspace_id"] ?? "";

        if ($workspace_member_id == "" || !is_numeric($workspace_member_id)) {
            $_SESSION["errors"] = ["Invalid workspace member selected."];
            header("Location: ../View/workspaces.php");
            exit();
        }

        if ($workspace_id == "" || !is_numeric($workspace_id)) {
            $_SESSION["errors"] = ["Invalid workspace selected."];
            header("Location: ../View/workspaces.php");
            exit();
        }

        $workspace_member = get_workspace_member_by_id_admin($conn, $workspace_member_id);

        if (!$workspace_member) {
            $_SESSION["errors"] = ["Workspace member not found."];
            header("Location: ../View/workspace_members.php?workspace_id=" . $workspace_id);
            exit();
        }

        $removed = remove_workspace_member_admin($conn, $workspace_member_id);

        if ($removed) {
            $_SESSION["success"] = "Workspace member removed successfully.";
        } 
        else {
            $_SESSION["errors"] = ["Failed to remove workspace member."];
        }

        header("Location: ../View/workspace_members.php?workspace_id=" . $workspace_id);
        exit();
    }
}