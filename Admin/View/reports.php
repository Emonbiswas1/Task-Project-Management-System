<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/report_model.php";

$total_users = report_total_users($conn);
$total_workspaces = report_total_workspaces($conn);
$total_projects = report_total_projects($conn);
$total_tasks = report_total_tasks($conn);
$completed_tasks = report_completed_tasks($conn);
$pending_milestones = report_pending_milestones($conn);
$completed_milestones = report_completed_milestones($conn);
$total_hours_logged = report_total_hours_logged($conn);
$recent_logs = report_recent_activity_logs($conn);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Admin Reports</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="users.php">Users</a> |
        <a href="workspaces.php">Workspaces</a> |
        <a href="projects.php">Projects</a> |
        <a href="tasks.php">Tasks</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Platform Summary</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Workspaces</h3>
            <p><?php echo $total_workspaces; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Projects</h3>
            <p><?php echo $total_projects; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Tasks</h3>
            <p><?php echo $total_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Completed Tasks</h3>
            <p><?php echo $completed_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Pending Milestones</h3>
            <p><?php echo $pending_milestones; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Completed Milestones</h3>
            <p><?php echo $completed_milestones; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Hours Logged</h3>
            <p><?php echo $total_hours_logged; ?></p>
        </div>

    </div>

    <hr>

    <h2>Recent Activity Logs</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Workspace</th>
            <th>Project</th>
            <th>Action Type</th>
            <th>Description</th>
            <th>Created At</th>
        </tr>

        <?php if ($recent_logs && mysqli_num_rows($recent_logs) > 0): ?>
            <?php while ($log = mysqli_fetch_assoc($recent_logs)): ?>
                <tr>
                    <td><?php echo $log["id"]; ?></td>
                    <td><?php echo htmlspecialchars($log["user_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($log["workspace_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($log["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($log["action_type"]); ?></td>
                    <td><?php echo htmlspecialchars($log["description"]); ?></td>
                    <td><?php echo $log["created_at"]; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No activity logs found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>