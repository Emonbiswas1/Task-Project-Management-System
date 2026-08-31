<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/comment_model.php";
include "../Model/attachment_model.php";

$client_id = $_SESSION["user_id"];
$project_id = $_GET["project_id"] ?? "";

if ($project_id != "" && !is_numeric($project_id)) {
    echo "<h2>Invalid project selected.</h2>";
    echo "<p><a href='comments_files.php'>Back</a></p>";
    exit();
}

$projects = get_projects_by_client($conn, $client_id);
$comments = get_client_visible_comments($conn, $client_id, $project_id);
$attachments = get_client_visible_attachments($conn, $client_id, $project_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Comments and Files</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Comments and Files</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">My Projects</a> |
        <a href="task_board.php">Task Board</a> |
        <a href="feedback.php">Feedback</a> |
        <a href="activity_feed.php">Activity Feed</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Filter by Project</h2>

    <form method="GET" action="comments_files.php">
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
        <a href="comments_files.php">Reset</a>
    </form>

    <hr>

    <h2>Client-visible Comments</h2>

    <p>Only comments marked as Client Visible are shown here.</p>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Task</th>
            <th>Comment By</th>
            <th>User Role</th>
            <th>Comment</th>
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
                    <td><?php echo htmlspecialchars($comment["created_at"]); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No client-visible comments found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <hr>

    <h2>Client-visible Attachments</h2>

    <p>Only files marked as Client Visible are shown here.</p>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Task</th>
            <th>File Name</th>
            <th>Size</th>
            <th>Uploaded By</th>
            <th>Uploader Role</th>
            <th>Uploaded At</th>
            <th>Open</th>
        </tr>

        <?php if ($attachments && mysqli_num_rows($attachments) > 0): ?>
            <?php while ($attachment = mysqli_fetch_assoc($attachments)): ?>
                <tr>
                    <td><?php echo $attachment["id"]; ?></td>
                    <td><?php echo htmlspecialchars($attachment["project_name"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["task_title"] ?? "N/A"); ?></td>
                    <td><?php echo htmlspecialchars($attachment["file_name"]); ?></td>
                    <td><?php echo round($attachment["file_size"] / 1024, 2); ?> KB</td>
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
                <td colspan="9">No client-visible attachments found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>