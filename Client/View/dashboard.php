<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/milestone_model.php";

$client_id = $_SESSION["user_id"];

$total_projects = count_client_projects($conn, $client_id);
$total_visible_milestones = count_client_visible_milestones($conn, $client_id);
$total_completed_milestones = count_client_completed_milestones($conn, $client_id);

$projects = get_projects_by_client($conn, $client_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Client Dashboard</h1>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</p>

    <p>
        <a href="projects.php">My Projects</a> |
        <a href="task_board.php">Task Board</a> |
        <a href="feedback.php">Feedback</a> |
         <a href="activity_feed.php">Activity Feed</a> |
          <a href="comments_files.php">Comments & Files</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Client Summary</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Visible Projects</h3>
            <p><?php echo $total_projects; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Visible Milestones</h3>
            <p><?php echo $total_visible_milestones; ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Completed Milestones</h3>
            <p><?php echo $total_completed_milestones; ?></p>
        </div>

    </div>

    <hr>

    <h2>My Projects</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>Project Name</th>
            <th>Description</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Visibility</th>
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
                    <td><?php echo htmlspecialchars($project["visibility"]); ?></td>
                    <td>
                        <a href="project_overview.php?project_id=<?php echo $project["id"]; ?>">View Overview</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No visible project found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>

