<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["member"]);

include "../../DB/db.php";
include "../Model/report_model.php";

$member_id = $_SESSION["user_id"];

$summary = get_member_productivity_summary($conn, $member_id);
$total_hours = get_member_total_logged_hours($conn, $member_id);
$recent_time_logs = get_member_recent_time_logs($conn, $member_id);
$recent_comments = get_member_recent_comments($conn, $member_id);
$recent_attachments = get_member_recent_attachments($conn, $member_id);

$total_tasks = $summary["total_tasks"] ?? 0;
$completed_tasks = $summary["completed_tasks"] ?? 0;
$pending_tasks = $summary["pending_tasks"] ?? 0;
$overdue_tasks = $summary["overdue_tasks"] ?? 0;

if ($total_tasks > 0) {
    $completion_percentage = round(($completed_tasks / $total_tasks) * 100, 2);
} else {
    $completion_percentage = 0;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Productivity Summary</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Productivity Summary</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="tasks.php">My Tasks</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>My Work Summary</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Assigned Tasks</h3>
            <p><?php echo $total_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Completed Tasks</h3>
            <p><?php echo $completed_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Pending Tasks</h3>
            <p><?php echo $pending_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Overdue Tasks</h3>
            <p><?php echo $overdue_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Hours Logged</h3>
            <p><?php echo $total_hours; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Completion Percentage</h3>
            <p><?php echo $completion_percentage; ?>%</p>
        </div>

    </div>

    <hr>

    <h2>Recent Time Logs</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Task</th>
            <th>Hours</th>
            <th>Note</th>
            <th>Logged At</th>
        </tr>

        <?php if ($recent_time_logs && mysqli_num_rows($recent_time_logs) > 0): ?>
            <?php while ($log = mysqli_fetch_assoc($recent_time_logs)): ?>
                <tr>
                    <td><?php echo $log["id"]; ?></td>
                    <td><?php echo htmlspecialchars($log["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($log["task_title"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($log["hours_logged"]); ?></td>
                    <td><?php echo htmlspecialchars($log["note"]); ?></td>
                    <td><?php echo htmlspecialchars($log["logged_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No time logs found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <hr>

<h2>Recent Comments</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Task</th>
            <th>Comment</th>
            <th>Visibility</th>
            <th>Created At</th>
        </tr>

        <?php if ($recent_comments && mysqli_num_rows($recent_comments) > 0): ?>
            <?php while ($comment = mysqli_fetch_assoc($recent_comments)): ?>
                <tr>
                    <td><?php echo $comment["id"]; ?></td>
                    <td><?php echo htmlspecialchars($comment["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($comment["task_title"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($comment["body"]); ?></td>
                    <td>
                        <?php
                        if ($comment["is_internal"] == 1) {
                            echo "Internal Only";
                        } else {
                            echo "Client Visible";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($comment["created_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No comments found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <hr>

    <h2>Recent Attachments</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Task</th>
            <th>File Name</th>
            <th>Size</th>
            <th>Visibility</th>
            <th>Uploaded At</th>
            <th>Open</th>
        </tr>

        <?php if ($recent_attachments && mysqli_num_rows($recent_attachments) > 0): ?>
            <?php while ($attachment = mysqli_fetch_assoc($recent_attachments)): ?>
                <tr>
                    <td><?php echo $attachment["id"]; ?></td>
                    <td><?php echo htmlspecialchars($attachment["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["task_title"] ?? "N/A"); ?></td>
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
                    <td><?php echo htmlspecialchars($attachment["uploaded_at"]); ?></td>
                    <td>
                        <a href="../../<?php echo htmlspecialchars($attachment["file_path"]); ?>" target="_blank">Open</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No attachments found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>