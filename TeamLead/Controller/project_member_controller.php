<?php

session_start();

include "../../DB/db.php";
include "../Model/project_model.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "team_lead") {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "assign_member") {
        $project_id = $_POST["project_id"] ?? "";
        $user_id = $_POST["user_id"] ?? "";
        $team_lead_id = $_SESSION["user_id"];

        if ($project_id == "") {
            $errors[] = "Project is required.";
        }

        if ($user_id == "") {
            $errors[] = "Member is required.";
        }

        if ($project_id != "") {
            $projects = get_projects_by_teamlead($conn, $team_lead_id);
            $valid_project = false;

            if ($projects && mysqli_num_rows($projects) > 0) {
                while ($project = mysqli_fetch_assoc($projects)) {
                    if ($project["id"] == $project_id) {
                        $valid_project = true;
                        break;
                    }
                }
            }

            if (!$valid_project) {
                $errors[] = "Invalid project selected.";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../View/project_members.php");
            exit();
        }

        if (check_project_member_exists($conn, $project_id, $user_id)) {
            $_SESSION["errors"] = ["This member is already assigned to this project."];
            header("Location: ../View/project_members.php");
            exit();
        }

        $assigned = add_project_member($conn, $project_id, $user_id);

        if ($assigned) {
            $_SESSION["success"] = "Project member assigned successfully.";
            header("Location: ../View/project_members.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to assign project member."];
            header("Location: ../View/project_members.php");
            exit();
        }
    }

    if ($action == "remove_member") {
        $project_member_id = $_POST["project_member_id"] ?? "";
        $team_lead_id = $_SESSION["user_id"];

        if ($project_member_id == "") {
            $_SESSION["errors"] = ["Project member ID is required."];
            header("Location: ../View/project_members.php");
            exit();
        }

        $project_member = get_project_member_by_id_and_teamlead($conn, $project_member_id, $team_lead_id);

        if (!$project_member) {
            $_SESSION["errors"] = ["Invalid project member selected."];
            header("Location: ../View/project_members.php");
            exit();
        }

        $removed = remove_project_member($conn, $project_member_id);

        if ($removed) {
            $_SESSION["success"] = "Project member removed successfully.";
            header("Location: ../View/project_members.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to remove project member."];
            header("Location: ../View/project_members.php");
            exit();
        }
    }
}