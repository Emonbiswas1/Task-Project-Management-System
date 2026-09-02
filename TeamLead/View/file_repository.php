<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["team_lead"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/attachment_model.php";

$team_lead_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";

if ($project_id != "" && !is_numeric($project_id)) {
    echo "<h2>Invalid project selected.</h2>";
    echo "<p><a href='file_repository.php'>Back</a></p>";
    exit();
}

$projects = get_projects_by_teamlead($conn, $team_lead_id);
$attachments = get_project_attachments_by_teamlead($conn, $team_lead_id, $project_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Project File Repository</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Project File Repository</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">Projects</a> |
        <a href="tasks.php">Tasks</a> |
        <a href="time_report.php">Time Report</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Filter Files</h2>

    <form method="GET" action="file_repository.php">
        <label>Project</label><br>
        <select name="project_id">
            <option value="">All Projects</option>

            <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                    <option value="<?php echo $project["id"]; ?>" <?php if ($project_id == $project["id"]) echo "selected"; ?>>
                        <?php echo htmlspecialchars($project["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <button type="submit">Filter</button>
        <a href="file_repository.php">Reset</a>
    </form>

    <hr>

    <h2>Uploaded Files</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Workspace</th>
            <th>Project</th>
            <th>Task</th>
            <th>File Name</th>
            <th>Size</th>
            <th>Visibility</th>
            <th>Uploaded By</th>
            <th>User Role</th>
            <th>Uploaded At</th>
            <th>Open</th>
        </tr>

        <?php if ($attachments && mysqli_num_rows($attachments) > 0): ?>
            <?php while ($attachment = mysqli_fetch_assoc($attachments)): ?>
                <tr>
                    <td><?php echo $attachment["id"]; ?></td>
                    <td><?php echo htmlspecialchars($attachment["workspace_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["task_title"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["file_name"]); ?></td>
                    <td><?php echo round($attachment["file_size"] / 1024, 2); ?> KB</td>
                    <td>
                        <?php
                        if ($attachment["is_client_visible"] == 1) {
                            echo "Client Visible";
                        } else {
                            echo "Internal Only";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($attachment["uploaded_by_name"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["uploaded_by_role"] ?? "Unknown"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["uploaded_at"]); ?></td>
                    <td>
                        <a href="../../<?php echo htmlspecialchars($attachment["file_path"]); ?>" target="_blank">Open</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">No files found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>