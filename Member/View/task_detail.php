<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["member"]);

include "../../DB/db.php";
include "../Model/task_model.php";
include "../Model/comment_model.php";
include "../Model/time_log_model.php";
include "../Model/attachment_model.php";

$member_id = $_SESSION["user_id"];
$task_id = $_GET["task_id"] ?? "";

if ($task_id == "" || !is_numeric($task_id)) {
    echo "<h2>Invalid task selected.</h2>";
    echo "<p><a href='tasks.php'>Back to Tasks</a></p>";
    exit();
}

$task = get_member_task_by_id($conn, $task_id, $member_id);

if (!$task) {
    echo "<h2>Task not found or access denied.</h2>";
    echo "<p><a href='tasks.php'>Back to Tasks</a></p>";
    exit();
}

$comments = get_comments_by_task($conn, $task_id);
$time_logs = get_time_logs_by_task_and_member($conn, $task_id, $member_id);
$total_logged_hours = get_total_hours_by_task_and_member($conn, $task_id, $member_id);
$attachments = get_attachments_by_task($conn, $task_id);


?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Details</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Task Details</h1>
    <p>
        <a href="tasks.php">Back to My Tasks</a> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="productivity.php">Productivity Summary</a> |
        <a href="../../logout.php">Logout</a>
    </p>
    <hr>

    <h2><?php echo htmlspecialchars($task["title"]); ?></h2>

    <p><strong>Project:</strong> <?php echo htmlspecialchars($task["project_name"] ?? "No Project"); ?></p>
    <p><strong>Project Description:</strong> <?php echo htmlspecialchars($task["project_description"] ?? ""); ?></p>

    <p>
        <strong>Milestone:</strong>
        <?php
        if ($task["milestone_title"]) {
            echo htmlspecialchars($task["milestone_title"]);
        } else {
            echo "No Milestone";
        }
        ?>
    </p>

    <p><strong>Description:</strong> <?php echo htmlspecialchars($task["description"]); ?></p>
    <p><strong>Created By:</strong> <?php echo htmlspecialchars($task["created_by_name"] ?? "Unknown"); ?></p>
    <p><strong>Priority:</strong> <?php echo htmlspecialchars($task["priority"]); ?></p>
    <p>
    <strong>Status:</strong>
    <select class="member-task-status-select" data-task-id="<?php echo $task["id"]; ?>">
        <option value="todo" <?php if ($task["status"] == "todo") echo "selected"; ?>>To Do</option>
        <option value="in_progress" <?php if ($task["status"] == "in_progress") echo "selected"; ?>>In Progress</option>
        <option value="review" <?php if ($task["status"] == "review") echo "selected"; ?>>Review</option>
        <option value="done" <?php if ($task["status"] == "done") echo "selected"; ?>>Done</option>
    </select>

    <small class="member-task-status-message"></small>
    </p>
    <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task["due_date"]); ?></p>
    <p><strong>Estimated Hours:</strong> <?php echo htmlspecialchars($task["estimated_hours"]); ?></p>
    <p><strong>Created At:</strong> <?php echo htmlspecialchars($task["created_at"]); ?></p>
    
    <script src="../../assets/js/ajax.js"></script>

    <hr>

    <?php
     if (isset($_SESSION["errors"])) {
        echo "<div style='color:red;'>";
        foreach ($_SESSION["errors"] as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
        echo "</div>";
        unset($_SESSION["errors"]);
    }

    if (isset($_SESSION["success"])) {
        echo "<div style='color:green;'>";
        echo "<p>" . htmlspecialchars($_SESSION["success"]) . "</p>";
        echo "</div>";
        unset($_SESSION["success"]);
    }
    ?>



    <?php
    if (isset($_SESSION["errors"])) {
        echo "<div style='color:red;'>";
        foreach ($_SESSION["errors"] as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
        echo "</div>";
        unset($_SESSION["errors"]);
    }

    if (isset($_SESSION["success"])) {
        echo "<div style='color:green;'>";
        echo "<p>" . htmlspecialchars($_SESSION["success"]) . "</p>";
        echo "</div>";
        unset($_SESSION["success"]);
    }
    ?>


