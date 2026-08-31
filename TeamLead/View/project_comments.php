<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/comment_model.php";

$team_lead_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";

$projects = get_projects_by_teamlead($conn, $team_lead_id);
$comments = false;

if ($project_id != "" && is_numeric($project_id)) {
    $comments = get_project_comments_by_teamlead($conn, $project_id, $team_lead_id);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Comments Overview</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Project Comments Overview</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">Projects</a> |
        <a href="tasks.php">Tasks</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Select Project</h2>

    <form method="GET" action="project_comments.php">
        <label>Project</label><br>
        <select name="project_id">
            <option value="">Select Project</option>
            <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                    <option value="<?php echo $project["id"]; ?>" <?php if ($project_id == $project["id"]) echo "selected"; ?>>
                        <?php echo htmlspecialchars($project["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <button type="submit">View Comments</button>
        <a href="project_comments.php">Reset</a>
    </form>

    <hr>

    <h2>Comments List</h2>

    <?php if ($project_id == ""): ?>

        <p>Please select a project to view comments.</p>

    <?php else: ?>

        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Project</th>
                <th>Task</th>
                <th>Comment By</th>
                <th>User Role</th>
                <th>Comment</th>
                <th>Visibility</th>
                <th>Created At</th>
            </tr>

            <?php if ($comments && mysqli_num_rows($comments) > 0): ?>
                <?php while ($comment = mysqli_fetch_assoc($comments)): ?>
                    <tr>
                        <td><?php echo $comment["id"]; ?></td>
                        <td><?php echo htmlspecialchars($comment["project_name"] ?? "N/A"); ?></td>
                        <td><?php echo htmlspecialchars($comment["task_title"] ?? "N/A"); ?></td>
                        <td><?php echo htmlspecialchars($comment["user_name"] ?? "Unknown"); ?></td>
                        <td><?php echo htmlspecialchars($comment["user_role"] ?? "Unknown"); ?></td>
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
                    <td colspan="8">No comments found for this project.</td>
                </tr>
            <?php endif; ?>
        </table>

    <?php endif; ?>

</body>
</html>