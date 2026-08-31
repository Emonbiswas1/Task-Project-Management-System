<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/milestone_model.php";

$client_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";

if ($project_id == "" || !is_numeric($project_id)) {
    echo "<h2>Invalid project selected.</h2>";
    echo "<p><a href='projects.php'>Back to Projects</a></p>";
    exit();
}

$project = get_client_project_by_id($conn, $project_id, $client_id);

if (!$project) {
    echo "<h2>Project not found or access denied.</h2>";
    echo "<p><a href='projects.php'>Back to Projects</a></p>";
    exit();
}

$milestones = get_client_visible_milestones($conn, $project_id, $client_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Overview</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Project Overview</h1>

    <p>
        <a href="projects.php">My Projects</a> |
        <a href="task_board.php">Task Board</a> |
        <a href="feedback.php">Feedback</a> |
         <a href="activity_feed.php">Activity Feed</a> |
          <a href="comments_files.php">Comments & Files</a> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2><?php echo htmlspecialchars($project["name"]); ?></h2>

    <p><strong>Workspace:</strong> <?php echo htmlspecialchars($project["workspace_name"]); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($project["description"]); ?></p>
    <p><strong>Deadline:</strong> <?php echo htmlspecialchars($project["deadline"]); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($project["status"]); ?></p>
    <p><strong>Visibility:</strong> <?php echo htmlspecialchars($project["visibility"]); ?></p>

    <hr>

    <h2>Client-visible Milestones</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Milestone Title</th>
            <th>Description</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Completed At</th>
        </tr>

        <?php if ($milestones && mysqli_num_rows($milestones) > 0): ?>
            <?php while ($milestone = mysqli_fetch_assoc($milestones)): ?>
                <tr>
                    <td><?php echo $milestone["id"]; ?></td>
                    <td><?php echo htmlspecialchars($milestone["title"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["description"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["due_date"]); ?></td>
                    <td><?php echo htmlspecialchars($milestone["status"]); ?></td>
                    <td>
                        <?php
                        if ($milestone["completed_at"]) {
                            echo htmlspecialchars($milestone["completed_at"]);
                        } else {
                            echo "Not completed";
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No client-visible milestone found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>