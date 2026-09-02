<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";

$team_lead_id = $_SESSION["user_id"];
$projects = get_projects_by_teamlead($conn, $team_lead_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Projects</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Manage Projects</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="create_project.php">Create New Project</a> |
        <a href="archived_projects.php">Archived Projects</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <?php
    if (isset($_SESSION["success"])) {
        echo "<div style='color:green;'>";
        echo "<p>" . htmlspecialchars($_SESSION["success"]) . "</p>";
        echo "</div>";
        unset($_SESSION["success"]);
    }

    if (isset($_SESSION["errors"])) {
        echo "<div style='color:red;'>";
        foreach ($_SESSION["errors"] as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
        echo "</div>";
        unset($_SESSION["errors"]);
    }
    ?>

    <h2>Project List</h2>

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
            <th>Action</th>
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
                    <td>
                        <a href="edit_project.php?project_id=<?php echo $project["id"]; ?>">Edit</a> |
                        <a href="project_comments.php?project_id=<?php echo $project["id"]; ?>">Comments</a> |
                        <a href="file_repository.php?project_id=<?php echo $project["id"]; ?>">Files</a>
                    
                        <?php if ($project["status"] == "completed"): ?>
                            <form action="../Controller/project_controller.php" method="POST" class="archive-project-form" style="display:inline;">
                                <input type="hidden" name="action" value="archive_project">
                                <input type="hidden" name="project_id" value="<?php echo $project["id"]; ?>">
                                <button type="submit">Archive</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">No project found.</td>
            </tr>
        <?php endif; ?>
    </table>
<script src="../../assets/js/validation.js"></script>
</body>
</html>