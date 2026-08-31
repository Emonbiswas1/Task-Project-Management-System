<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/task_model.php";

$team_lead_id = $_SESSION["user_id"];

$total_active_projects = count_teamlead_active_projects($conn, $team_lead_id);
$total_tasks = count_teamlead_total_tasks($conn, $team_lead_id);
$overdue_tasks = count_teamlead_overdue_tasks($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Lead Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Team Lead Dashboard</h1>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</p>

    <p>
        <a href="../../Common/View/profile.php">My Profile</a> |    
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Leadership Summary</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Active Projects</h3>
            <p><?php echo $total_active_projects; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Tasks</h3>
            <p><?php echo $total_tasks; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Overdue Tasks</h3>
            <p><?php echo $overdue_tasks; ?></p>
        </div>

    </div>

    <h2>Quick Actions</h2>

    <ul>
        <li><a href="workspaces.php">Manage Workspaces</a></li>
        <li><a href="projects.php">Manage Projects</a></li>
        <li><a href="project_members.php">Manage Project Members</a></li>
        <li><a href="tasks.php">Manage Tasks</a></li>
        <li><a href="milestones.php">Manage Milestones</a></li>
        <li><a href="reports.php">View Reports</a></li>
        <li><a href="burndown.php">Burndown Summary</a></li>
        <li><a href="client_feedback.php">Client Feedback</a></li>
        <li><a href="project_comments.php">Project Comments</a></li>
        <li><a href="time_report.php">Project Time Report</a></li>
        <li><a href="file_repository.php">Project File Repository</a></li>
    </ul>

</body>
</html>