<?php

include "../../includes/auth_check.php";
include "../../includes/role_check.php";

check_role(["client"]);

include "../../DB/db.php";
include "../Model/project_model.php";
include "../Model/task_model.php";

$client_id = $_SESSION["user_id"];

$projects = get_projects_by_client($conn, $client_id);
$tasks = get_client_visible_tasks($conn, $client_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Task Board</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <h1>Client Task Board</h1>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="projects.php">My Projects</a> |
        <a href="feedback.php">Feedback</a> |
         <a href="activity_feed.php">Activity Feed</a> |
          <a href="comments_files.php">Comments & Files</a> |
        <a href="../../Common/View/profile.php">My Profile</a> |
        <a href="../../logout.php">Logout</a>
    </p>

    <hr>

    <h2>Filter Tasks</h2>

    <div>
        <label>Project</label><br>
        <select id="client_task_project">
            <option value="">All Projects</option>
            <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
                <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                    <option value="<?php echo $project["id"]; ?>">
                        <?php echo htmlspecialchars($project["name"]); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
    </div>

    <br>

    <div>
        <label>Status</label><br>
        <select id="client_task_status">
            <option value="">All Status</option>
            <option value="todo">To Do</option>
            <option value="in_progress">In Progress</option>
            <option value="review">Review</option>
            <option value="done">Done</option>
        </select>
    </div>

    <br>

    <small id="clientTaskFilterMessage"></small>

    <hr>

    <h2>Task List</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Milestone</th>
            <th>Task Title</th>
            <th>Description</th>
            <th>Assigned Member</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Estimated Hours</th>
        </tr>

        <tbody id="clientTaskTableBody">
            <?php if ($tasks && mysqli_num_rows($tasks) > 0): ?>
                <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td><?php echo $task["id"]; ?></td>
                        <td><?php echo htmlspecialchars($task["project_name"] ?? "N/A"); ?></td>
                        <td>
                            <?php
                            if ($task["milestone_title"]) {
                                echo htmlspecialchars($task["milestone_title"]);
                            } else {
                                echo "No Milestone";
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($task["title"]); ?></td>
                        <td><?php echo htmlspecialchars($task["description"]); ?></td>
                        <td><?php echo htmlspecialchars($task["assigned_member"] ?? "Not Assigned"); ?></td>
                        <td><?php echo htmlspecialchars($task["priority"]); ?></td>
                        <td><?php echo htmlspecialchars($task["status"]); ?></td>
                        <td><?php echo htmlspecialchars($task["due_date"]); ?></td>
                        <td><?php echo htmlspecialchars($task["estimated_hours"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No task found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="../../assets/js/ajax.js"></script>
</body>
</html>