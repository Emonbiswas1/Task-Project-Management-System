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

