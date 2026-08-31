<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/project_model.php";

$client_id = $_SESSION["user_id"];
$projects = get_projects_by_client($conn, $client_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Projects</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>My Projects</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="feedback.php">Feedback</a> |
         <a href="activity_feed.php">Activity Feed</a> |
          <a href="comments_files.php">Comments & Files</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>Project Name</th>
            <th>Description</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
            <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                <tr>
                    <td><?php echo $project["id"]; ?></td>
                    <td><?php echo htmlspecialchars($project["workspace_name"]); ?></td>
                    <td><?php echo htmlspecialchars($project["name"]); ?></td>
                    <td><?php echo htmlspecialchars($project["description"]); ?></td>
                    <td><?php echo htmlspecialchars($project["deadline"]); ?></td>
                    <td><?php echo htmlspecialchars($project["status"]); ?></td>
                    <td>
                        <a href="project_overview.php?project_id=<?php echo $project["id"]; ?>">View Overview</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No visible project found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>