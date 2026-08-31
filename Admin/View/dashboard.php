<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["admin"]);

include "../../DB/db.php";
include "../Model/user_model.php";
include "../Model/workspace_model.php";
include "../Model/project_model.php";
include "../Model/task_model.php";

$total_workspaces = count_all_workspaces($conn);
$total_users = count_all_users($conn);
$total_members = count_users_by_role($conn, "member");
$total_team_leads = count_users_by_role($conn, "team_lead");
$total_clients = count_users_by_role($conn, "client");
$total_admins = count_users_by_role($conn, "admin");
$total_active_projects = count_active_projects($conn);
$total_tasks_today = count_tasks_created_today($conn);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <h1>Admin Dashboard</h1>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</p>

    <p>
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Platform Summary</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Workspaces</h3>
            <p><?php echo $total_workspaces; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Active Projects</h3>
            <p><?php echo $total_active_projects; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Tasks Created Today</h3>
            <p><?php echo $total_tasks_today; ?></p>
        </div>

    </div>

    <h2>Users By Role</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Role</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>Member</td>
            <td><?php echo $total_members; ?></td>
        </tr>
        <tr>
            <td>Team Lead</td>
            <td><?php echo $total_team_leads; ?></td>
        </tr>
        <tr>
            <td>Client</td>
            <td><?php echo $total_clients; ?></td>
        </tr>
        <tr>
            <td>Admin</td>
            <td><?php echo $total_admins; ?></td>
        </tr>
    </table>

    <h2>Admin Quick Actions</h2>
    <ul>
        <li><a href="create_user.php">Create User</a></li>
        <li><a href="users.php">Manage Users</a></li>
        <li><a href="workspaces.php">Manage Workspaces</a></li>
        <li><a href="projects.php">View All Projects</a></li>
        <li><a href="tasks.php">View All Tasks</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="workspace_user_assign.php">Assign User to Workspace</a></li>
    </ul>

</body>

</html>