<?php

session_start();

include "../../DB/db.php";
include "../Model/feedback_model.php";
include "../Model/milestone_model.php";
include "../Model/activity_model.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../Common/View/login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action == "submit_feedback") {
        if ($_SESSION["role"] != "client") {
            $_SESSION["errors"] = ["Unauthorized access."];
            header("Location: ../../Common/View/login.php");
            exit();
        }

        $milestone_id = $_POST["milestone_id"] ?? "";
        $feedback_text = trim($_POST["feedback_text"] ?? "");
        $approval_status = $_POST["approval_status"] ?? "";
        $client_id = $_SESSION["user_id"];

        if ($milestone_id == "" || !is_numeric($milestone_id)) {
            $errors[] = "Invalid milestone selected.";
        }

        if ($feedback_text == "") {
            $errors[] = "Feedback text is required.";
        }

        $allowed_status = ["approved", "revision_requested"];

        if (!in_array($approval_status, $allowed_status)) {
            $errors[] = "Please select a valid approval status.";
        }

        $milestone = null;

        if ($milestone_id != "" && is_numeric($milestone_id)) {
            $milestone = get_client_visible_milestone_by_id($conn, $milestone_id, $client_id);

            if (!$milestone) {
                $errors[] = "Milestone not found or access denied.";
            }
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            header("Location: ../../Client/View/feedback.php");
            exit();
        }

        $created = create_client_feedback($conn, $milestone_id, $client_id, $feedback_text, $approval_status);

        if ($created) {
            if (function_exists("add_activity_log")) {
                add_activity_log(
                    $conn,
                    null,
                    $milestone["project_id"],
                    $client_id,
                    "client_feedback_submitted",
                    "Client submitted feedback for milestone: " . $milestone["title"]
                );
            }

            $_SESSION["success"] = "Feedback submitted successfully.";
            header("Location: ../../Client/View/feedback.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to submit feedback."];
            header("Location: ../../Client/View/feedback.php");
            exit();
        }
    }

    if ($action == "acknowledge_feedback") {
        if ($_SESSION["role"] != "team_lead") {
            $_SESSION["errors"] = ["Unauthorized access."];
            header("Location: ../../Common/View/login.php");
            exit();
        }

        $feedback_id = $_POST["feedback_id"] ?? "";
        $team_lead_id = $_SESSION["user_id"];

        if ($feedback_id == "" || !is_numeric($feedback_id)) {
            $_SESSION["errors"] = ["Invalid feedback selected."];
            header("Location: ../../TeamLead/View/client_feedback.php");
            exit();
        }

        $feedback = get_feedback_by_id_and_teamlead($conn, $feedback_id, $team_lead_id);

        if (!$feedback) {
            $_SESSION["errors"] = ["Feedback not found or access denied."];
            header("Location: ../../TeamLead/View/client_feedback.php");
            exit();
        }

        $updated = acknowledge_client_feedback($conn, $feedback_id);

        if ($updated) {
            if (function_exists("add_activity_log")) {
                add_activity_log(
                    $conn,
                    $feedback["workspace_id"],
                    $feedback["project_id"],
                    $team_lead_id,
                    "client_feedback_acknowledged",
                    "Client feedback acknowledged for milestone: " . $feedback["milestone_title"]
                );
            }

            $_SESSION["success"] = "Client feedback acknowledged successfully.";
            header("Location: ../../TeamLead/View/client_feedback.php");
            exit();
        } 
        else {
            $_SESSION["errors"] = ["Failed to acknowledge feedback."];
            header("Location: ../../TeamLead/View/client_feedback.php");
            exit();
        }
    }
}

if (isset($_SESSION["role"]) && $_SESSION["role"] == "team_lead") {
    header("Location: ../../TeamLead/View/client_feedback.php");
    exit();
}

header("Location: ../../Client/View/feedback.php");
exit();