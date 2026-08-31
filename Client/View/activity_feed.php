<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/activity_model.php";

$client_id = $_SESSION["user_id"];
$activities = get_client_activity_feed($conn, $client_id);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Client Activity Feed</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <h1>Client Activity Feed</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">My Projects</a> |
        <a href="task_board.php">Task Board</a> |
        <a href="feedback.php">Feedback</a> |
         <a href="comments_files.php">Comments & Files</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Recent Project Activity</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>Project</th>
            <th>User</th>
            <th>Action Type</th>
            <th>Description</th>
            <th>Date</th>
        </tr>

        <?php if ($activities && mysqli_num_rows($activities) > 0): ?>
            <?php while ($activity = mysqli_fetch_assoc($activities)): ?>
                <tr>
                    <td><?php echo $activity["id"]; ?></td>
                    <td><?php echo htmlspecialchars($activity["workspace_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($activity["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($activity["user_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($activity["action_type"]); ?></td>
                    <td><?php echo htmlspecialchars($activity["description"]); ?></td>
                    <td><?php echo htmlspecialchars($activity["created_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No activity found for your projects.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>

</html>