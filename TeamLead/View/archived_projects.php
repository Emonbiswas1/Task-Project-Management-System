<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";

$team_lead_id = $_SESSION["user_id"];
$projects = get_archived_projects_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Archived Projects</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Archived Projects</h1>

    <p>
        <a href="projects.php">Back to Projects</a> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>Project Name</th>
            <th>Client</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Visibility</th>
            <th>Color</th>
            <th>Created At</th>
        </tr>

        <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
            <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                <tr>
                    <td><?php echo $project["id"]; ?></td>
                    <td><?php echo htmlspecialchars($project["workspace_name"]); ?></td>
                    <td><?php echo htmlspecialchars($project["name"]); ?></td>
                    <td><?php echo htmlspecialchars($project["client_name"] ?? "No Client"); ?></td>
                    <td><?php echo htmlspecialchars($project["deadline"]); ?></td>
                    <td><?php echo htmlspecialchars($project["status"]); ?></td>
                    <td><?php echo htmlspecialchars($project["visibility"]); ?></td>
                    <td><?php echo htmlspecialchars($project["color_label"]); ?></td>
                    <td><?php echo $project["created_at"]; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No archived project found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html> 