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
 <h2>Log Work Hours</h2>

    <p>
        <strong>Total Hours You Logged On This Task:</strong>
        <?php echo $total_logged_hours; ?> hour(s)
    </p>

    <form id="timeLogForm" action="../Controller/time_log_controller.php" method="POST">
        <input type="hidden" name="action" value="add_time_log">
        <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">

        <div>
            <label>Hours Worked</label><br>
            <input type="text" name="hours_logged" id="time_log_hours" placeholder="Example: 2.5">
            <small id="timeLogHoursError"></small>
        </div>

        <br>

        <div>
            <label>Note</label><br>
            <textarea name="note" id="time_log_note" placeholder="Write what you worked on"></textarea>
            <small id="timeLogNoteError"></small>
        </div>

        <br>

        <button type="submit">Add Time Log</button>
    </form>

    <hr>

    <h2>My Time Logs</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Member</th>
            <th>Hours</th>
            <th>Note</th>
            <th>Logged At</th>
        </tr>

        <?php if ($time_logs && mysqli_num_rows($time_logs) > 0): ?>
            <?php while ($log = mysqli_fetch_assoc($time_logs)): ?>
                <tr>
                    <td><?php echo $log["id"]; ?></td>
                    <td><?php echo htmlspecialchars($log["member_name"]); ?></td>
                    <td><?php echo htmlspecialchars($log["hours_logged"]); ?></td>
                    <td><?php echo htmlspecialchars($log["note"]); ?></td>
                    <td><?php echo htmlspecialchars($log["logged_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No time log found for this task.</td>
            </tr>
        <?php endif; ?>
    </table>

        <hr>
<h2>Upload Attachment</h2>

    <form id="attachmentForm" action="../Controller/attachment_controller.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload_attachment">
        <input type="hidden" name="task_id" value="<?php echo $task["id"]; ?>">

        <div>
            <label>Attachment File</label><br>
            <input type="file" name="attachment_file" id="attachment_file">
            <small id="attachmentFileError"></small>
        </div>

        <br>

        <div>
            <label>File Visibility</label><br>
            <select name="is_client_visible" id="attachment_visibility">
                <option value="">Select Visibility</option>
                <option value="0">Internal Only</option>
                <option value="1">Client Visible</option>
            </select>
            <small id="attachmentVisibilityError"></small>
        </div>

        <br>

        <button type="submit">Upload Attachment</button>
    </form>

    <hr>

    <h2>Task Attachments</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>Size</th>
            <th>Visibility</th>
            <th>Uploaded By</th>
            <th>Uploaded At</th>
            <th>View</th>
        </tr>

        <?php if ($attachments && mysqli_num_rows($attachments) > 0): ?>
            <?php while ($attachment = mysqli_fetch_assoc($attachments)): ?>
                <tr>
                    <td><?php echo $attachment["id"]; ?></td>
                    <td><?php echo htmlspecialchars($attachment["file_name"]); ?></td>
                    <td><?php echo round($attachment["file_size"] / 1024, 2); ?> KB</td>
                    <td>
                        <?php
                        if ($attachment["is_client_visible"] == 1) {
                            echo "Client Visible";
                        } else {
                            echo "Internal Only";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($attachment["uploaded_by_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["uploaded_at"]); ?></td>
                    <td>
                        <a href="../../<?php echo htmlspecialchars($attachment["file_path"]); ?>" target="_blank">Open</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No attachment found for this task.</td>
            </tr>
        <?php endif; ?>
    </table>


